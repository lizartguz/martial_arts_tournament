<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'name',
        'lastname',
        'email',
        'occupation',
        'ci',
        //'issued',
        'number_phone',
        'password',
        'image',
        'pin',
        'version_app',
        'is_update',
        //'type',
        'device_identifier',
        'payment_state',
        'access_type',
        'user_type',
        'own_state',
        'user_dependency_id',
        'state',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'profile_photo_url',
    ];


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /*public function subscription_user()
    {
        return $this->belongsToMany(SubscriptionM::class, 'users_subscriptions')->withPivot(['reg_date',
        'expiration_notice',
        'date_expiry',
        'amount',
        'is_discount',
        'cost',
        'data_save',
        'state',]);
    }*/

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function userSubscriptions()
    {
        //return $this->hasMany(SubscriptionM::class, 'users_subscriptions', 'id');
        return $this->hasMany(UsersSubscriptionM::class,'user_id', 'id');
    }

    public function notification(){ 
        return $this->hasMany(User::class,'user_id', 'id'); 
    }

    public function dependencies(){
        return $this->hasMany(UserDependencyM::class, 'dependent_user_id', 'id');
    }

    public function subscriptionsThroughDependencies(){
        return $this->hasManyThrough(
            UsersSubscriptionM::class,
            UserDependencyM::class,
            'dependent_user_id',       // Foreign key en UserDependencyM
            'id',                      // Foreign key en UsersSubscriptionM (relacionado con user_subscription_id)
            'id',                      // Local key en User
            'user_subscription_id'     // Local key en UserDependencyM
        );
    }

    public function networkCredentialsCreated()
    {
        return $this->hasMany(NetworkCredentialM::class, 'user_creator_id', 'id');
    }

    public function networkCredentialHistoriesCreated()
    {
        return $this->hasMany(NetworkCredentialHistoryM::class, 'user_creator_id', 'id');
    }

    public function fcmTokens()
    {
        return $this->hasMany(FcmToken::class, 'user_id', 'id');
    }
}
