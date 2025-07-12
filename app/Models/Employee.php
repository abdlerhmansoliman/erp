<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;

class Employee extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'position_id',
        'department_id',
        'full_name',
        'email',
        'phone',
        'address',
        'gender',
        'status',
        'birth_date',
        'hire_date',
        'salary',
        'national_id',
        'notes',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function position()
    {
        return $this->belongsTo(Position::class);
    }
    public function department(){
        return $this->belongsTo(Department::class);
    }
    public function salaries(){
        return $this->hasMany(Salary::class);
    }
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
