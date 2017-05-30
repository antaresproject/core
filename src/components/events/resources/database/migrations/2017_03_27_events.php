<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class Events extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('tbl_events', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->text('namespace');
            $table->text('name');
            $table->text('description');
            $table->integer('fire_count');
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
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('tbl_events');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

}
