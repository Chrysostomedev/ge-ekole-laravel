<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    /**
     * Les attributs qui peuvent être remplis en masse.
     * C'est la solution pour que Student::create($data) fonctionne.
     *
     * @var array<int, string>
     */
   protected $fillable = [
    'first_name',
    'last_name',
    'email',
    'birth_date',
    'phone_number',
    'matricule',
    'photo',
    'classroom_id',
];


    /**
     * Définition de la relation avec la classe.
     */
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * Définition de la relation avec les notes.
     */
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
}