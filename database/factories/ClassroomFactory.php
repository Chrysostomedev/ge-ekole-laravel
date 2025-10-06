<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Classroom>
 */
class ClassroomFactory extends Factory
{
    public function definition()
{
    return [
        'name' => $this->faker->randomElement(['6ème 1','5ème 1','4ème 1', '3ème 1', '2nd C', '1ère A', 'Tle A']),
        'level' => $this->faker->randomElement(['6ème','5ème','4ème', '3ème','Terminale']),
        'teacher' => $this->faker->name,
    ];
}

}
