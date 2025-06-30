<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Required for DB::connection()->getDoctrineSchemaManager() if needed, but Schema::hasColumn is simpler.

class CheckUserSchema extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'timebank:check-user-schema';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks if phone_number, notification_preference, and is_admin columns exist in the users table.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tableName = 'users';

        try {
            // Ensure database connection is attempted, which might trigger errors if DB is not set up
            // For SQLite, this will try to create the database file if it doesn't exist and DB_DATABASE is set in .env
            // For this check, we primarily care about schema, not necessarily DB content.
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $this->error("Database connection failed: " . $e->getMessage());
            $this->line("Please ensure your .env file is configured correctly for the database (e.g., for SQLite).");
            $this->line("If using SQLite and the file doesn't exist, 'php artisan migrate' might be needed first if DB_DATABASE is set.");
            return 1;
        }


        $hasPhoneNumber = Schema::hasColumn($tableName, 'phone_number');
        $hasNotificationPreference = Schema::hasColumn($tableName, 'notification_preference');
        $hasIsAdmin = Schema::hasColumn($tableName, 'is_admin');

        if ($hasPhoneNumber) {
            $this->info("Column 'phone_number' exists in '{$tableName}' table.");
        } else {
            $this->warn("Column 'phone_number' does NOT exist in '{$tableName}' table.");
        }

        if ($hasNotificationPreference) {
            $this->info("Column 'notification_preference' exists in '{$tableName}' table.");
        } else {
            $this->warn("Column 'notification_preference' does NOT exist in '{$tableName}' table.");
        }

        if ($hasIsAdmin) {
            $this->info("Column 'is_admin' exists in '{$tableName}' table.");
        } else {
            $this->warn("Column 'is_admin' does NOT exist in '{$tableName}' table.");
        }

        if (!$hasPhoneNumber || !$hasNotificationPreference || !$hasIsAdmin) {
            $this->line("One or more columns are missing. You might need to run migrations: php artisan migrate");
        }

        return 0;
    }
}
