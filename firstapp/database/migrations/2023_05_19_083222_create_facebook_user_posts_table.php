<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook_user_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fb_user_id')
            ->references('id')
            ->on('facebook_users')
            ->cascadeOnUpdate()
            ->cascadeOnDelete();
            $table->string('message');
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
        Schema::dropIfExists('facebook_user_posts');
    }
};
