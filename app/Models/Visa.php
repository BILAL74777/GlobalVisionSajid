<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visa extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name', 'phone_number', 'status', 'amount', 'tracking_id', 
        'gmail', 'pak_visa_password', 'gmail_password', 'gender', 'date', 'entry_type'
    ];

    /**
     * Get the family members associated with this visa (only if entry_type is 'Family').
     */
    public function familyMembers()
    {
        return $this->hasMany(FamilyVisa::class, 'visa_id');
    }
}
