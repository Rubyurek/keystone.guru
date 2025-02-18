<?php

namespace App\Console\Commands\Environment;

use App\Console\Commands\Traits\ExecutesShellCommands;
use Artisan;
use Illuminate\Console\Command;

class UpdatePrepare extends Command
{
    use ExecutesShellCommands;

    const NO_DEV = [
        'live'    => true,
        'local'   => false,
        'mapping' => true,
        'staging' => true,
        'testing' => false,
    ];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prepares the environment for an update';

    /**
     * @var string
     */
    protected $signature = 'environment:updateprepare {environment}';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $environment = $this->argument('environment');

        $this->shell([
            // Git commands
            'git checkout .',
            'git clean -f',
            'git pull',
        ]);

        $this->shell([
            'npm install',
            'npm audit fix',
        ]);

        // Install composer here - a next command can then have the updated definitions of the autoloader when called
        // Any code after this will use the old definitions and get class not found errors
        $this->shell([
            // Prevent root warning from blocking the entire thing; only install dev dependencies in local
            sprintf('export COMPOSER_ALLOW_SUPERUSER=1; composer install %s', (self::NO_DEV[$environment] ? '--no-dev' : '')),
            'export COMPOSER_ALLOW_SUPERUSER=1; composer dump-autoload',
        ]);

        Artisan::call('db:backup');

        return 0;
    }
}
