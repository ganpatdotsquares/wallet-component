<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes, Sortable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'dob',
        'gender',
        'profile_image',
        'verification_otp',
        'email_verified_at',
        'password',
        'status',
        'remember_token',
        'email_verification_token',
        'forgot_password_otp',
        'fcm_token',
        'stripe_account_id',
        'stripe_customer_id',
        'stripe_payouts_enabled',
        'profile_step',
        'stripe_token',
        'wallet_referral_code',
        'wallet_referred_by',
    ];
    public $sortable = [
        'user_status',
        'first_name',
        'last_name',
        'email',
        'status',
        'created_at',
        'updated_at'
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'deleted_at',
    ];

    // The attributes that should be mutated to dates.
    protected $dates = ['deleted_at'];

    public function getNameAttribute()
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }
}
