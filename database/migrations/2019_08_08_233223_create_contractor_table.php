<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Langs;

class CreateContractorTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'contractor',
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->comment("наименование юридического лица, либо ФИО физлица");
                $table->string("contact_name", 50)->comment("ФИО контактного лица");
                $table->string("phone")->comment("Номер телефона");
                $table->string("bin")->unique()->comment("РНН/БИН");
                $table->integer("user_id")->comment("Идентификатор пользователя");
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
        Schema::dropIfExists('contractor');
    }
}
