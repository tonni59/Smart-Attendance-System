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
        Schema::create('class_session', function (Blueprint $table) {
            $table->id();
            $table->string('class_token');
            $table->date('class_date');
            $table->time('class_start_time');
            $table->time('class_end_time');
            $table->time('class_late');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('class_session_record');
    }
};
