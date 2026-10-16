<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->bigIncrements('id'); // Use bigIncrements for bigInteger auto-incrementing ID
            $table->string('name', 100);
            $table->string('email', 100)->unique();
            $table->string('mobile_number', 16)->nullable();
            $table->string('password', 100);
            $table->string('profile_image', 100)->nullable();
            $table->string('forgot_password_token', 150)->nullable();
            $table->boolean('status')->default(true);
            $table->rememberToken()->nullable();
            $table->timestamps();
            $table->softDeletes(); // Deleted at for soft deletes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin');
    }
};
