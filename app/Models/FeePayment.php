<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeePayment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function studentFee(): BelongsTo
    {
        return $this->belongsTo(StudentFee::class);
    }
}
