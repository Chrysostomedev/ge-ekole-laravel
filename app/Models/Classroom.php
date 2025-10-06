<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // <-- nécessaire pour les factories
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory; // <-- active la méthode factory()

    // Les champs que l'on peut remplir en masse (mass assignment)
    protected $fillable = ['name', 'level', 'teacher'];

    // Une classe a plusieurs élèves
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    // Une classe a aussi plusieurs notes (grades)
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
}
