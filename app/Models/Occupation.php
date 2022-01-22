<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Occupation extends Model
{
    use HasFactory;

    protected $table = 'occupation';

    protected $primaryKey = 'uuid';

    protected $fillable = [
        'uuid',
        'member_id',
        'name',
        'salary',
    ];
    protected $hidden = [
        'id',
    ];

    protected $casts = [
        'uuid' => 'string',
    ];

    // one to many relationship with user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
