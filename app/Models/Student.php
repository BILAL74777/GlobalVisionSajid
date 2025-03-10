<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Program;
use App\Models\Upload;
class Student extends Model
{
    use HasFactory;
    public $incrementing=false;
    protected $table = 'students';

    public function program() {
        return $this->belongsTo(Program::class);
    }
    public function upload() {
        return $this->belongsTo(Upload::class, 'profile_picture_id');
    }
    
}

