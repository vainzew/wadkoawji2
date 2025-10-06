<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('setting')) {
            Schema::table('setting', function (Blueprint $table) {
                if (!Schema::hasColumn('setting', 'tax_enabled')) {
                    $table->boolean('tax_enabled')->default(false);
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('setting') && Schema::hasColumn('setting', 'tax_enabled')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->dropColumn('tax_enabled');
            });
        }
    }
};

