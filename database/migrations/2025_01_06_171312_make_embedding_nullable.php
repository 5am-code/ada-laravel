<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table('embeddings', function (Blueprint $table) {
            $table->vector('embedding', 1536)->nullable()->change();
        });
    }
};
