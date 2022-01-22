<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loginhistory extends Model
{
    use HasFactory;
    
    protected $table = 'login_history';

    protected $fillable = [
        'ip_address',
        'email',
    ];
}
