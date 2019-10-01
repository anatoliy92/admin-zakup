<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAndRemoveColsToTenderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tender', function (Blueprint $table) {
            $langs = \App\Models\Langs::all();
            foreach ($langs as $lang) { $table->dropColumn("good_" . $lang->key); }

            $table->boolean("good")->default(false)->after("section_id");
            $table->integer("sposob")->nullable()->comment("Способ закупа");
            $table->integer("status")->nullable()->comment("Статус закупа");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tender', function (Blueprint $table) {
            $langs = \App\Models\Langs::all();
            foreach ($langs as $lang) { $table->integer('good_' . $lang->key)->default(0)->after("section_id"); }

            $table->dropColumn("good");
            $table->dropColumn("sposob");
            $table->dropColumn("status");
        });
    }
}
