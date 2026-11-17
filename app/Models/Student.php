<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Student extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function primaryGuardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class, 'guardian_id');
    }

    public function guardians(): BelongsToMany
    {
        return $this->belongsToMany(
            Guardian::class,
            'student_guardian'
        )->withPivot('relationship', 'is_primary')->withTimestamps();
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function fees(): HasMany
    {
        return $this->hasMany(StudentFee::class);
    }

    public function feePayments(): HasManyThrough
    {
        return $this->hasManyThrough(FeePayment::class, StudentFee::class);
    }

    public function marks(): HasMany
    {
        return $this->hasMany(Mark::class);
    }
}
