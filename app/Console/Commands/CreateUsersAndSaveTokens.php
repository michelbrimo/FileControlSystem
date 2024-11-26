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
    protected $signature = 'users:create-and-save-tokens {count=100}';

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
        $count = $this->argument('count');

        $url = 'http://127.0.0.1:8000/api/register';
        $userInfos = [];

        for ($i = 1; $i <= $count; $i++) {
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
                $id = $response['data']['id'];

                $userInfos[] = [
                    'token' => $token,
                    'id' => $id,
                ];
                $this->info("User {$username} created and token saved.");
            } else {
                $this->error("Failed to create user {$username}: {$response->body()}");
            }
        }

        $csvFile = fopen(storage_path('load_test/users_info.csv'), 'w');
        fputcsv($csvFile, ['token', 'id']); // CSV header

        foreach ($userInfos as $userInfo) {
            fputcsv($csvFile, [$userInfo['token'], $userInfo['id']]); // Wrap token in an array
        }

        fclose($csvFile);
        $this->info('User tokens saved successfully to users_info.csv.');

        return 0;
    }
}
