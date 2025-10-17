<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasColumn('payments', 'is_read')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->boolean('is_read')->default(false)->after('updated_at');
            });
        }
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'is_read')) {
                $table->dropColumn('is_read');
            }
        });
    }
};
