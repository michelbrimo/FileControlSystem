<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CreateGroupsAndSaveId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'groups:create-groups-and-save-id {count=20}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create groups and save their IDs to a CSV file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = $this->argument('count');

        $url = 'http://127.0.0.1:8000/api/create-group';
        $tokens_csv = storage_path('load_test/users_info.csv');
        $groups_info = [];

        // Read tokens from CSV file
        if (($handle = fopen($tokens_csv, 'r')) !== false) {
            $tokens = [];
            $isFirstRow = true;

            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                if ($isFirstRow) {
                    $isFirstRow = false; // Skip the header
                    continue;
                }
                $tokens[] = $data[0];
            }

            fclose($handle);
        } else {
            $this->error("Unable to open the CSV file at {$tokens_csv}.");
            return 1;
        }

        // Create groups
        for ($i = 1; $i <= $count; $i++) {
            $name = "group{$i}";
            $randomToken = $tokens[array_rand($tokens)];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $randomToken,
                'Content-Type' => 'application/json',
            ])->post($url, ['name' => $name]);

            if ($response->successful()) {
                $id = $response['data']['id'];
                $name = $response['data']['name'];
                $admin_id = $response['data']['admin_id'];

                $groups_info[] = [
                    'id' => $id,
                    'name' => $name,
                    'admin_id' => $admin_id,
                ];

                $this->info("Group {$name} created and ID saved.");
            } else {
                $this->error("Failed to create group {$name}: {$response->body()}");
            }
        }

        // Write groups info to CSV
        $csvFilePath = storage_path('load_test/groups_info.csv');
        if (!is_dir(dirname($csvFilePath))) {
            mkdir(dirname($csvFilePath), 0755, true);
        }

        $csvFile = fopen($csvFilePath, 'w');
        fputcsv($csvFile, ['id', 'name', 'admin_id']); // CSV header

        foreach ($groups_info as $group_info) {
            fputcsv($csvFile, [$group_info['id'], $group_info['name'], $group_info['admin_id']]);
        }

        fclose($csvFile);
        $this->info('Groups created and IDs saved to groups_info.csv.');

        return 0;
    }
}