<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMapIdColumnToDungeonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dungeons', function (Blueprint $table) {
            $table->integer('map_id')->after('zone_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dungeons', function (Blueprint $table) {
            $table->dropColumn('map_id');
        });
    }
}
