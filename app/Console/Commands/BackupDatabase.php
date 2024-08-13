<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the database to a file';

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
     * @return int
     */
    public function handle()
    {
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $host = env('DB_HOST');
        $database = env('DB_DATABASE');
        $filename = "backup-" . now()->format('Y-m-d_H-i-s') . ".sql";

        $this->info("DB Username: " . $username);
        $this->info("DB Password: " . $password);
        $this->info("DB Host: " . $host);
        $this->info("DB Database: " . $database);

        $command = sprintf(
            'mysqldump --user=%s --password=\'%s\' --host=%s %s --result-file=%s',
            $username,
            $password,
            $host,
            $database,
            storage_path('app/backups/' . $filename)
        );

        $this->info('Running command: ' . $command);
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            $this->error('Database backup failed');
            return 1;
        }

        $this->info('Database backup was successful');
        return 0;
    }
}
