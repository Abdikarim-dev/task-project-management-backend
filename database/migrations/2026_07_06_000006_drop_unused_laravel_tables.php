<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** @var list<string> */
    private array $unusedTables = [
        'sessions',
        'password_reset_tokens',
        'cache',
        'cache_locks',
        'jobs',
        'job_batches',
        'failed_jobs',
    ];

    public function up(): void
    {
        foreach ($this->unusedTables as $table) {
            Schema::dropIfExists($table);
        }

        DB::table('migrations')->whereIn('migration', [
            '0001_01_01_000001_create_cache_table',
            '0001_01_01_000002_create_jobs_table',
        ])->delete();
    }

    public function down(): void
    {
        // Intentionally empty — these tables are not used by this API.
    }
};
