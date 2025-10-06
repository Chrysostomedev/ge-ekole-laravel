<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Grade;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Créer 4 classes
        $classes = Classroom::factory(4)->create();

        // Pour chaque classe, créer des élèves et leurs notes
        foreach ($classes as $class) {
            $students = Student::factory(10)->create([
                'classroom_id' => $class->id,
            ]);

            foreach ($students as $student) {
                Grade::factory(4)->create([
                    'student_id'   => $student->id,
                    'classroom_id' => $class->id,
                ]);
            }
        }
    }
}
