<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CallOtherCommands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'call:all-commands {users=100} {groups=20} {invitations=30}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Call all load testing commands from this command';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = $this->argument('users');
        $groups = $this->argument('groups');
        $invitations = $this->argument('invitations');

        $this->info('Calling users:create-and-save-tokens...');
        Artisan::call('users:create-and-save-tokens', [
            'count' => $users,
        ]);
        $this->info('First command completed.');

        $this->info('Calling groups:create-groups-and-save-id...');
        Artisan::call('groups:create-groups-and-save-id', [
            'count' => $groups
        ]);
        $this->info('Second command completed.');

        $this->info('All commands have been called successfully.');

        return 0;
    }
}
