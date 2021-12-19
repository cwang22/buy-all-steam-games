<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRecordsTable extends Migration
{

    public function up(): void
    {
        DB::unprepared('UPDATE `records` SET `original` = `original` * 100, `sale` = `sale` * 100;');

        Schema::table('records', static function (Blueprint $table) {
            $table->integer('original')->nullable()->change();
            $table->integer('sale')->nullable()->change();
            $table->dropColumn('language');
            $table->index(['cc']);
        });
    }

    public function down(): void
    {
        Schema::table('records', static function (Blueprint $table) {
            $table->decimal('original')->nullable()->change();
            $table->decimal('sale')->nullable()->change();
            $table->string('language', 20)->default('US')->after('cc');
        });

        DB::unprepared('UPDATE `records` SET `original` = `original` / 100, `sale` = `sale` / 100;');
    }
}
