<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'curriculum_id',
        'semester_id',
        'education_level_id',
        'education_class_id',
        'education_subject_id',
        'type_id',
        'quantity',
        'file'
    ];

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function educationLevel()
    {
        return $this->belongsTo(EducationLevel::class);
    }

    public function educationClass()
    {
        return $this->belongsTo(EducationClass::class);
    }

    public function educationSubject()
    {
        return $this->belongsTo(EducationSubject::class);
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
    }
}
