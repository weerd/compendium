<?php

namespace Compendium\Console\Commands;

use Compendium\Models\User;
use Illuminate\Console\Command;

class ClearDropboxToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dropbox:clear-token {user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear the Dropbox authentication token for the specified user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $user = User::findOrFail($this->argument('user'));

        $user->dropbox_token = null;
        $user->save();

        return $this->comment(PHP_EOL . 'The Dropbox authentication token for user with ID = ' . $user->id . ', has been cleared.' . PHP_EOL);
    }
}
