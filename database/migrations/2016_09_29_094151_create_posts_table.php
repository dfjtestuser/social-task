<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->char('id')->unique();
            $table->integer('user_id');
            $table->datetime('created_time');
            $table->string('caption')->nullable()->index();
            $table->text('message')->nullable();
            $table->text('attachments')->nullable();
            $table->mediumText('picture')->nullable();
            $table->mediumText('full_picture')->nullable();
            $table->binary('description')->nullable();
            $table->string('name')->nullable();
            $table->string('link')->nullable();
        });
    }

     /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
