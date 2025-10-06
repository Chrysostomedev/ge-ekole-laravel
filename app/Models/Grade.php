<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'classroom_id', 'subject', 'score'];

    // Une note appartient à un élève
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Une note appartient à une classe
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
}
