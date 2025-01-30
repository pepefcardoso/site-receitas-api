<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ResetProject extends Command
{
    // The name and signature of the console command.
    protected $signature = 'config:reset-project';

    // The console command description.
    protected $description = 'Clear all Laravel caches (config, routes, views, and application cache).';

    // Execute the console command.
    public function handle()
    {
        $this->info('Clearing config cache...');
        $this->call('config:clear');

        $this->info('Clearing route cache...');
        $this->call('route:clear');

        $this->info('Clearing application cache...');
        $this->call('cache:clear');

        $this->info('Clearing view cache...');
        $this->call('view:clear');

        $this->info('Rebuilding config cache...');
        $this->call('config:cache');

        $this->info('Rebuilding route cache...');
        $this->call('route:cache');

        $this->info('Migrating and resetting tables...');
        $this->call('migrate:fresh', ['--seed' => true]);

        $this->info('All caches have been cleared and rebuilt!');
    }
}
