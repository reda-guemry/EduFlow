<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'course_id',
        'status',
    ];

    
    protected $hidden = [];

    
    protected  $casts = [
        'status' => 'string',
    ];

    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
