<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModelPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('model_permission', function (Blueprint $table) {
            $table->unsignedInteger('permission_id');

            $table->string('model_type');
            $table->unsignedInteger('model_id');

            $table->unsignedInteger('resource_id')->nullable();
            $table->tinyInteger('state');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('model_permission');
    }
}