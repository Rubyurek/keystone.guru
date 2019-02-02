<?php
/**
 * Created by PhpStorm.
 * User: Wouter
 * Date: 03/12/2018
 * Time: 23:57
 */

namespace App\Logic\Scheduler;

use App\Jobs\ProcessRouteFloorThumbnail;
use App\Models\DungeonRoute;
use App\Models\Floor;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FindOutdatedThumbnails
{
    use ChecksForDuplicateJobs;

    function __invoke()
    {
        Log::channel('scheduler')->debug('>> Finding thumbnails');
        /** @var Collection $routes */
        // Published routes get priority! This is only really relevant initially while processing the thumbnail queue
        $routes = DungeonRoute::orderBy('published', 'desc')->get();
        Log::channel('scheduler')->debug(sprintf('Checking %s routes for thumbnails', $routes->count()));

        $processed = 0;

        $currentJobCount = DB::table('jobs')->count();
        foreach ($routes as $dungeonRoute) {
            /** @var DungeonRoute $dungeonRoute */
            $updatedAt = Carbon::createFromTimeString($dungeonRoute->updated_at);
            $thumbnailUpdatedAt = Carbon::createFromTimeString($dungeonRoute->thumbnail_updated_at);

            // Add a limit to the amount of jobs that can be queued at once. When we have 100 jobs, the server is busy
            // enough as-is and we don't need to queue more jobs. Mostly this is done because
            // ProcessRouteFloorThumbnail::thumbnailsExistsForRoute() gets more expensive the more jobs there are
            // It has to deserialize the job's payload for each attempt to queue more jobs. If this goes to the 100s
            // it will cause the server to come to a crawl for no real reason. Thus, this magic 100 is introduced.
            if ($currentJobCount < 100 &&
                ((// Updated at is greater than the thumbnail updated at (don't keep updating thumbnails..)
                        $updatedAt->greaterThan($thumbnailUpdatedAt) &&
                        // If the route has been updated in the past x minutes...
                        $updatedAt->addMinute(config('keystoneguru.thumbnail_refresh_min'))->isPast())
                    ||
                    // Update every month regardless
                    $thumbnailUpdatedAt->addMonth(1)->isPast()
                    ||
                    // Thumbnail does not exist in the folder it should
                    !ProcessRouteFloorThumbnail::thumbnailsExistsForRoute($dungeonRoute)
                )) {

                if (!$this->isJobQueuedForModel(\App\Jobs\ProcessRouteFloorThumbnail::class, $dungeonRoute)) {
                    Log::channel('scheduler')->debug(sprintf('Queueing job for route %s (%s floors)', $dungeonRoute->public_key, $dungeonRoute->dungeon->floors->count()));

                    $dungeonRoute->queueRefreshThumbnails();

                    // Refresh the current job count, it should be increased now
                    $currentJobCount = DB::table('jobs')->count();
                    $processed++;
                }
            }
        }

        Log::channel('scheduler')->debug(sprintf('Scheduled processing for %s routes', $processed));
        Log::channel('scheduler')->debug('OK Finding thumbnails');
    }
}