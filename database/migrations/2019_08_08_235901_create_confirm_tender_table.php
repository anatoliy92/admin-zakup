<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Langs;

class CreateConfirmTenderTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'confirm_tender',
            function (Blueprint $table) {
                $table->increments('id');
                $table->integer('contract_id')->comment("Поставщик услуг");
                $table->integer('tender_id')->comment("Тендер");
                $table->tinyInteger('confirm');
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('confirm_tender');
    }
}
