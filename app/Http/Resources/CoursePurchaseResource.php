<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CoursePurchaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'course_id' => $this->course_id,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'status' => $this->status,
            'stripe_checkout_session_id' => $this->stripe_checkout_session_id,
            'stripe_payment_intent_id' => $this->stripe_payment_intent_id,
            'stripe_payment_id' => $this->stripe_payment_id,
            'paid_at' => $this->paid_at,
            'failure_reason' => $this->failure_reason,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
