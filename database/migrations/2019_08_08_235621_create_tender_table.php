<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Langs;

class CreateTenderTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tender', function (Blueprint $table) {
            $table->increments('id');;
            $table->integer('section_id');
            $table->integer('rubric_id')->nullable()->comment('Рубрика');
            $table->integer('category_id')->nullable()->comment('Категория');

            foreach (Langs::all() as $lang) {
                $table->integer('good_' . $lang->key)->default(0);
                $table->string('title_' . $lang->key)->nullable();
                $table->text('short_' . $lang->key)->nullable();
                $table->mediumText('full_' . $lang->key)->nullable();
            }

            $table->integer('update_user')->nullable();
            $table->integer('created_user')->nullable();
            $table->dateTime('until_date')->nullable()->comment('Срок жизни документа');
            $table->dateTime('published_at')->nullable();
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
        Schema::dropIfExists('tender');
    }
}
