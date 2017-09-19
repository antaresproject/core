<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Events extends Migration
{

    /** @var string */
    protected static $tableName = 'tbl_events';

    /**
     * Run the migrations
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create(self::$tableName, function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->text('namespace');
            $table->integer('fire_count');
            $table->text('details');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists(self::$tableName);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

}
