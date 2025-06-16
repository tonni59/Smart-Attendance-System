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
        Schema::create('school_years', function (Blueprint $table) {
            $table->id();
            $table->string('year')->unique();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('middleinitial')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('student_no')->unique()->nullable();
            $table->string('section')->nullable();
            $table->string('username')->unique()->nullable();
            $table->string('password')->nullable();
            $table->string('role')->default('student');
            $table->string('token');
            $table->timestamp('created_at')->useCurrent();
            $table->boolean('approved')->nullable();
            $table->string('school_year_id')->nullable();
            $table->foreign('school_year_id')->references('year')->on('school_years');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
