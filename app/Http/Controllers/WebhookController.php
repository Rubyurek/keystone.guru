<?php

namespace App\Http\Controllers;

use App\Service\Discord\DiscordApiServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class WebhookController extends Controller
{

    /**
     * Validate an incoming github webhook
     *
     * @param Request $request
     *
     * @return void
     * @throws BadRequestException|UnauthorizedException
     * @see https://dev.to/ryan1/how-to-validate-github-webhooks-with-laravel-and-php-2he1
     */
    protected function validateGithubWebhook(Request $request)
    {
        if (($signature = $request->headers->get('X-Hub-Signature')) == null) {
            throw new BadRequestException('Header not set');
        }

        $signatureParts = explode('=', $signature);

        if (count($signatureParts) != 2) {
            throw new BadRequestException('signature has invalid format');
        }

        $knownSignature = hash_hmac('sha1', $request->getContent(), config('keystoneguru.webhook.github.secret'));

        if (!hash_equals($knownSignature, $signatureParts[1])) {
            throw new UnauthorizedException('Could not verify request signature ' . $signatureParts[1]);
        }
    }

    /**
     * @param Request $request
     * @param DiscordApiServiceInterface $discordApiService
     * @return Response
     */
    public function github(Request $request, DiscordApiServiceInterface $discordApiService)
    {
        $this->validateGithubWebhook($request);

        $commits = $request->get('commits');
        $ref     = $request->get('ref');
        $branch  = str_replace('refs/heads/', '', $ref);

        // We don't need duplicate messages in Discord since mapping is automatically managed
        if ($branch !== 'mapping') {
            $embeds = [];
            foreach ($commits as $commit) {
                // Skip system commits (such as merge branch X into Y)
                if (($commit['committer']['name'] === 'Github' && $commit['committer']['email'] === 'noreply@github.com') ||
                    // Skip commits that have originally be done on another branch
                    !$commit['distinct'] ||
                    // Skip merge commits
                    strpos($commit['message'], 'Merge remote-tracking branch') === 0
                ) {
                    continue;
                }

                $lines = explode('\\n', $commit['message']);

                $embeds[] = [
                    'title'       => sprintf(
                        '%s: %s',
                        $branch,
                        substr(array_shift($lines), 0, 256)
                    ),
                    'description' => substr(trim(view('app.commit.commit', [
                        'commit' => $commit,
                        'lines'  => $lines,
                    ])->render()), 0, 2000),
                    'url'         => $commit['url'],
                ];

                if (!empty($commit['added'])) {
                    $embeds[] = [
                        'color'       => 2328118, // #238636
                        'description' => substr(trim(view('app.commit.added', [
                            'commit' => $commit,
                        ])->render()), 0, 2000),
                    ];
                }

                if (!empty($commit['modified'])) {
                    $embeds[] = [
                        'color'       => 25284, // #0062C4
                        'description' => substr(trim(view('app.commit.modified', [
                            'commit' => $commit,
                        ])->render()), 0, 2000),
                    ];
                }

                if (!empty($commit['removed'])) {
                    $embeds[] = [
                        'color'       => 14300723, // #DA3633
                        'description' => substr(trim(view('app.commit.removed', [
                            'commit' => $commit,
                        ])->render()), 0, 2000),
                    ];
                }

                $lastKey                       = array_key_last($embeds);
                $embeds[$lastKey]['timestamp'] = $commit['timestamp'];
            }

            // Only send a message if we found a commit that was worthy of mentioning
            if (!empty($embeds)) {
                // Add footer to the last embed
                $lastKey                    = array_key_last($embeds);
                $embeds[$lastKey]['footer'] = [
                    'icon_url' => 'https://keystone.guru/images/external/discord/footer_image.png',
                    'text'     => 'Keystone.guru Discord Bot',
                ];

                $discordApiService->sendEmbeds(config('keystoneguru.webhook.github.url'), $embeds);
            }
        }

        return response()->noContent();
    }
}
