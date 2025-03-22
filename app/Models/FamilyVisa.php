<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyVisa extends Model
{
    use HasFactory;

    protected $fillable = [
        'visa_id', 'full_name', 'phone_number', 'status', 'amount', 'tracking_id', 
        'gmail', 'pak_visa_password', 'gmail_password', 'gender', 'date'
    ];

    /**
     * Get the primary visa record associated with this family member.
     */
    public function primaryVisa()
    {
        return $this->belongsTo(Visa::class, 'visa_id');
    }
    
}
