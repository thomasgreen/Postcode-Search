<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class IssueToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:issue-token {userId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('userId');

        $user = User::find($userId);

        if (!$user) {
            $this->error('User not found.');
            return 1;
        }

        // Issue a Sanctum token
        $token = $user->createToken('Console Token')->plainTextToken;

        // Output the token
        $this->info('Token: ' . $token);

        return 0;
    }
}
