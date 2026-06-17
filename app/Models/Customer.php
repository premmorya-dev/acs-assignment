<?php

namespace App\Models;

use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    /** @use HasFactory<CustomerFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'phone_number',
        'email',
        'payment_amount',
        'payment_status',
    ];

    protected function casts(): array
    {
        return [
            'payment_amount' => 'decimal:2',
        ];
    }

    public function communicationLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CommunicationLog::class);
    }
}
