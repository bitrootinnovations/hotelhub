<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add composite & covering indexes for all client-wise API queries.
 * Optimised for 100+ concurrent mobile users.
 */
class AddPerformanceIndexes extends Migration
{
    public function up()
    {
        // category_masters
        Schema::table('category_masters', function (Blueprint $table) {
            if (!$this->hasIndex('category_masters', 'cat_client_status_idx'))
                $table->index(['client_id', 'status_id'], 'cat_client_status_idx');
        });

        // table_type_masters
        Schema::table('table_type_masters', function (Blueprint $table) {
            if (!$this->hasIndex('table_type_masters', 'tt_client_status_idx'))
                $table->index(['client_id', 'status_id'], 'tt_client_status_idx');
        });

        // table_masters
        Schema::table('table_masters', function (Blueprint $table) {
            if (!$this->hasIndex('table_masters', 'tbl_client_status_idx'))
                $table->index(['client_id', 'status_id'], 'tbl_client_status_idx');
            if (!$this->hasIndex('table_masters', 'tbl_client_type_idx'))
                $table->index(['client_id', 'table_type_id'], 'tbl_client_type_idx');
        });

        // menu_masters
        Schema::table('menu_masters', function (Blueprint $table) {
            if (!$this->hasIndex('menu_masters', 'menu_client_status_idx'))
                $table->index(['client_id', 'status_id'], 'menu_client_status_idx');
            if (!$this->hasIndex('menu_masters', 'menu_client_cat_idx'))
                $table->index(['client_id', 'category_id'], 'menu_client_cat_idx');
        });

        // client_masters
        Schema::table('client_masters', function (Blueprint $table) {
            if (!$this->hasIndex('client_masters', 'cm_status_idx'))
                $table->index('status_id', 'cm_status_idx');
        });
    }

    public function down()
    {
        Schema::table('category_masters',   fn($t) => $t->dropIndex('cat_client_status_idx'));
        Schema::table('table_type_masters', fn($t) => $t->dropIndex('tt_client_status_idx'));
        Schema::table('table_masters',      function ($t) {
            $t->dropIndex('tbl_client_status_idx');
            $t->dropIndex('tbl_client_type_idx');
        });
        Schema::table('menu_masters', function ($t) {
            $t->dropIndex('menu_client_status_idx');
            $t->dropIndex('menu_client_cat_idx');
        });
        Schema::table('client_masters', fn($t) => $t->dropIndex('cm_status_idx'));
    }

    private function hasIndex(string $table, string $index): bool
    {
        $indexes = \DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = '{$index}'");
        return count($indexes) > 0;
    }
}
