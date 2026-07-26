<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TruncateContentDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:truncate-content {--force : Force execution without confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncate all content data tables strictly within the current project database, preserving users, roles, permissions, and migrations';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dbName = DB::getDatabaseName();

        if (! $this->option('force') && ! $this->confirm("Are you sure you want to truncate content data in database '{$dbName}' (preserving users, roles, and permissions)?")) {
            $this->info('Truncate command cancelled.');

            return Command::SUCCESS;
        }

        $excludedTables = [
            'users',
            'roles',
            'permissions',
            'model_has_roles',
            'model_has_permissions',
            'role_has_permissions',
            'migrations',
            'failed_jobs',
            'sqlite_sequence',
        ];

        // Fetch ONLY tables belonging strictly to the active project database
        $tables = $this->getProjectTables($dbName);

        Schema::disableForeignKeyConstraints();

        $truncatedCount = 0;

        foreach ($tables as $table) {
            $cleanName = str_contains($table, '.') ? explode('.', $table)[1] : $table;
            $normalizedName = strtolower($cleanName);

            if (in_array($normalizedName, array_map('strtolower', $excludedTables), true)) {
                $this->line("  <comment>[Skipped]</comment> Preserved protected table: <info>{$cleanName}</info>");

                continue;
            }

            DB::table($cleanName)->truncate();
            $truncatedCount++;
            $this->line("  <fg=red>[Truncated]</fg=red> Cleared project table: <info>{$cleanName}</info>");
        }

        Schema::enableForeignKeyConstraints();

        $this->newLine();
        $this->info("Successfully truncated {$truncatedCount} project tables in database '{$dbName}'. Users, roles, and permissions preserved.");

        return Command::SUCCESS;
    }

    protected function getProjectTables(string $dbName): array
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            $rows = DB::select('SELECT name FROM sqlite_master WHERE type="table" AND name NOT LIKE "sqlite_%"');

            return array_map(fn ($r) => $r->name, $rows);
        }

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            $rows = DB::select('SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_TYPE = "BASE TABLE"', [$dbName]);

            return array_map(fn ($r) => $r->TABLE_NAME ?? $r->table_name, $rows);
        }

        return Schema::getTableListing();
    }
}
