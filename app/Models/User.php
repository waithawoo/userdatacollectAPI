<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Tymon\JWTAuth\Contracts\JWTSubject;

use App\Models\Member;
use Illuminate\Support\Str;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'uuid';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'email',
        'password',
        'role',
        'membership_code'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'id',
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'uuid' => 'string',
        'email_verified_at' => 'datetime',
    ];

    // public static function boot(){
    //     parent::boot();
    //     self::creating(function ($model){
    //         $model->uuid = Str::uuid()->toString();
    //     });
    // }

    public function getRouteKeyName(){
        return 'uuid';
    }
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier() {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [];
    }

    // To get a specific user data as json response
    public static function getData($user)
    {

        if($user->role == config('const.NORMAL_USER')){
            $occupations = User::where('uuid', $user->uuid)->first()->occupation;
            if($occupations != null){
                // foreach ($occupations as $each) {
                    $user->occupations = $occupations;
                // }
            }else{
                $user->occupations = null;
            }
        }
        
        return $user;
    }
    
    // To get all users data as json response
    public static function getAll(){
        $normal_users = User::where('role',config('const.NORMAL_USER'))->get();
        foreach($normal_users as $user){
            $occupations = User::where('uuid', $user->uuid)->first()->occupation;
            $user->occupations = $occupations;            
        }
        return $normal_users;
    }

    // one to many relationship with occupatin
    public function occupation()
    {
        return $this->hasMany(Occupation::class);
    }
}
