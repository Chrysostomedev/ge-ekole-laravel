<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Grade>
 */
class GradeFactory extends Factory
{
    public function definition()
{
    return [
        'student_id' => null,
        'classroom_id' => null,
        'subject' => $this->faker->randomElement(['Maths','Français' ,'Philosophie','SVT', 'Anglais','informatique']),
        'score' => $this->faker->numberBetween(5,20),
        'max_score' => 20,
        'term' => 'Trimestre 1',
    ];
}

}
