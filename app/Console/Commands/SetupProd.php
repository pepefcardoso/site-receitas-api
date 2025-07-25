<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ResetProject extends Command
{
    protected $signature = 'config:setup-prod';
    protected $description = 'Set up the production environment for the application.';

    public function handle()
    {
        $this->call('config:clear');
        $this->call('route:clear');
        $this->call('cache:clear');
        $this->call('view:clear');
        $this->call('config:cache');
        $this->call('route:cache');
        $this->call('migrate:fresh');
        $this->call('optimize');
        $this->call('storage:link');
        $this->call('key:generate');
    }
}
