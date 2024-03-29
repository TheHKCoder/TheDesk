<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChatroomTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chatroom', function (Blueprint $table) {
            $table->increments('id');
            $table->string('unique_id', 50);
            $table->string('name', 50)->nullable();
            $table->mediumText('description')->nullable();
            $table->string('type', 10);

            $table->timestamp('create_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('update_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->string('status', 2)->default('1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chatroom');
    }
}
