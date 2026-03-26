<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoursePurchase extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'amount',
        'currency',
        'status',
        'stripe_checkout_session_id',
        'stripe_payment_intent_id',
        'stripe_payment_id',
        'paid_at',
        'failure_reason',
    ];

    protected $hidden = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }



}
