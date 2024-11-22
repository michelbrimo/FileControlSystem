<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class CreateUsersAndSaveTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:create-and-save-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create 100 users and save their tokens to a CSV file';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $url = 'http://127.0.0.1:8000/api/register';
        $userTokens = [];

        for ($i = 1; $i <= 100; $i++) {
            $username = "user{$i}";
            $email = "user{$i}@example.com";
            $password = "password123";

            $response = Http::post($url, [
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'password_confirmation' => $password
            ]);

            if ($response->successful()) {
                $token = $response['data']['token'];

                $userTokens[] = [
                    'token' => $token
                ];
                $this->info("User {$username} created and token saved.");
            } else {
                $this->error("Failed to create user {$username}: {$response->body()}");
            }
        }

        // Write user tokens to CSV
        $csvFile = fopen(storage_path('user_tokens.csv'), 'w');
        fputcsv($csvFile, ['token']); // CSV header

        foreach ($userTokens as $userToken) {
            fputcsv($csvFile, $userToken);
        }

        fclose($csvFile);
        $this->info('User tokens saved successfully to user_tokens.csv.');

        return 0;
    }
}
