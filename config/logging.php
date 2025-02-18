<?php

use App\Logging\WithDungeonRouteContext;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [
    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */
    'default'  => env('LOG_CHANNEL', 'stack'),
    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */
    'channels' => [
        'stack'          => [
            'driver'   => 'stack',
            'channels' => ['daily', 'discord'],
        ],
        'scheduler'      => [
            'driver'   => 'stack',
            'channels' => ['scheduler_file'],
        ],
        'single'         => [
            'driver' => 'single',
            'path'   => storage_path('logs/laravel.log'),
            'level'  => 'debug',
        ],
        'scheduler_file' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/scheduler.log'),
            'level'  => 'debug',
            'days'   => 14,
        ],
        'daily'          => [
            'driver' => 'daily',
            'path'   => storage_path('logs/laravel.log'),
            'level'  => 'debug',
            'days'   => 14,
        ],
        'slack'          => [
            'driver'   => 'slack',
            'url'      => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji'    => ':boom:',
            'level'    => 'critical',
        ],
        'papertrail'     => [
            'driver'       => 'monolog',
            'level'        => 'debug',
            'handler'      => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],
        'stderr'         => [
            'driver'  => 'monolog',
            'handler' => StreamHandler::class,
            'with'    => [
                'stream' => 'php://stderr',
            ],
        ],
        'syslog'         => [
            'driver' => 'syslog',
            'level'  => 'debug',
        ],
        'errorlog'       => [
            'driver' => 'errorlog',
            'level'  => 'debug',
        ],
        'discord'        => empty(env('APP_LOG_DISCORD_WEBHOOK')) ? [] : [
            'driver' => 'custom',
            'url'    => env('APP_LOG_DISCORD_WEBHOOK'),
            'via'    => MarvinLabs\DiscordLogger\Logger::class,
            'level'  => 'warning',
//            'formatter' => Monolog\Formatter\LineFormatter::class,
//            'formatter_with' => [
//                'format' => "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",
//            ],
        ]
    ],
];
