<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MythApiClient extends Migration
{

    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('myth_api_manager_models', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->morphs('syncable');
            $table->string("manager_name");
            $table->string("manager_id");
            $table->boolean('sync')->default(true);
            $table->timestamp('sync_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('myth_api_manager_models');
    }
}
