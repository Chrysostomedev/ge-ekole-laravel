<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGradesTable extends Migration
{
    public function up()
    {
        // On modifie l'ancienne table 'grades' pour l'adapter aux moyennes
        Schema::table('grades', function (Blueprint $table) {
            // Renommage/Modification de la colonne 'score' pour refléter qu'il s'agit d'une moyenne
            // On utilise 'score' comme moyenne sur 20, le renommage est optionnel mais conseillé
            $table->renameColumn('score', 'average_score'); 
            
            // Suppression de la colonne 'max_score' car la moyenne est implicitement sur 20
            $table->dropColumn('max_score');

            // Ajout du coefficient de la matière (essentiel pour le calcul de la moyenne générale)
            $table->unsignedSmallInteger('coefficient')->default(1)->after('subject'); 
        });

        // Assurer l'unicité: une seule moyenne par élève, par matière, par terme
        Schema::table('grades', function (Blueprint $table) {
            $table->unique(['student_id', 'subject', 'term']);
        });
        
        // Si vous voulez recréer la table (plus facile si elle n'a pas de données)
        // Schema::dropIfExists('grades');
        // Schema::create('grades', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
        //     $table->foreignId('classroom_id')->constrained('classrooms')->cascadeOnDelete();
        //     $table->string('subject'); 
        //     $table->unsignedSmallInteger('coefficient')->default(1);
        //     $table->decimal('average_score', 5, 2)->nullable(); 
        //     $table->string('term')->nullable(); 
        //     $table->timestamps();
        //     $table->unique(['student_id', 'subject', 'term']);
        // });
    }

    public function down()
    {
        // En cas de rollback, restaurer l'ancienne structure si possible
        Schema::table('grades', function (Blueprint $table) {
            // Retirer l'index unique
            $table->dropUnique(['student_id', 'subject', 'term']); 
            
            // Revenir au nom 'score' et ajouter 'max_score'
            $table->renameColumn('average_score', 'score'); 
            $table->decimal('max_score', 5, 2)->default(20); 
            $table->dropColumn('coefficient');
        });
    }
}