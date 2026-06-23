<?php

namespace Tests;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DebugTest extends TestCase
{
    use \Illuminate\Foundation\Testing\DatabaseTransactions;

    /** @test */
    public function debug()
    {
        echo PHP_EOL . "DB: " . config('database.default') . PHP_EOL;
        echo "DB_NAME: " . config('database.connections.' . config('database.default') . '.database') . PHP_EOL;
        
        // Create a table
        Schema::create('users', function ($table) {
            $table->id();
            $table->string('name');
        });
        
        echo "Schema created!" . PHP_EOL;
        
        // Verify
        $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table'");
        print_r($tables);
    }
}
