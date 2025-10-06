<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassroomsTable extends Migration
{
    public function up()
    {
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id(); // id auto-incrémenté
            $table->string('name'); // ex: "6ème A", "Terminale S"
            $table->string('level')->nullable(); // ex: "6ème", "Terminale"
            $table->string('teacher')->nullable(); // professeur référent
            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('classrooms');
    }
}
