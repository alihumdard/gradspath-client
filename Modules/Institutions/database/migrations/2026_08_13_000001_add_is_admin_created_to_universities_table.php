<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('universities', function (Blueprint $table) {
            $table->boolean('is_admin_created')->default(true)->after('is_active');
            $table->index('is_admin_created');
        });
    }

    public function down(): void
    {
        Schema::table('universities', function (Blueprint $table) {
            $table->dropIndex(['is_admin_created']);
            $table->dropColumn('is_admin_created');
        });
    }
};
