<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    use HasFactory;

    protected  $fillable = [
        'teacher_id',
        'category_id',
        'title',
        'description',
        'price',
    ];


    protected $hidden = [];


    protected  $casts = [
        'price' => 'decimal:2',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function savedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_user');
    }

    
    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(CoursePurchase::class);
    }
    
    
}
