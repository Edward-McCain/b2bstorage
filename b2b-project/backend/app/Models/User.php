<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'glink',
        'role',
        'acc_type',
        'status_subscription',
        'first_name',
        'last_name',
        'user_name',
        'position',
        'email',
        'phone_number',
        'verified_email',
        'phone_ok',
        'password',
        'fcm_token',
        'fcm_token_android',
        'timezone',
        'last_logged_in',
        'is_online',
        'language',
        'messages_language',
        'country',
        'city',
        'avatar_url',
        'banned',
        'currency',
        'balance',
        'ref_balance',
        'demo_balance',
        'bonus_balance',
        'inn',
        'comp_pinfl',
        'comp_state',
        'company_type',
        'company_name',
        'company_description',
        'company_rating',
        'com_address',
        'com_leader',
        'comp_logo_url',
        'comp_phone',
        'comp_mail',
        'comp_website_url',
        'company_link',
        'company_statuses',
        'comp_verified',
        'comp_tariff',
        'deal_seen',
        'notification_email',
        'notification_email_deal',
        'notification_email_system',
        'notification_email_chat',
        'notification_email_subscription',
        'notification_sms_chat',
        'notification_sms_custom',
        'notification_sms_system',
        'is_active',
        'catch',
        'reg_date',
        'moderated',
        'gen_key',
        'referer',
        'invite_link',
        'deleted',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'verified_email' => 'boolean',
            'phone_ok' => 'boolean',
            'is_online' => 'boolean',
            'banned' => 'boolean',
            'comp_state' => 'boolean',
            'deal_seen' => 'boolean',
            'notification_email' => 'boolean',
            'notification_email_deal' => 'boolean',
            'notification_email_system' => 'boolean',
            'notification_email_chat' => 'boolean',
            'notification_email_subscription' => 'boolean',
            'notification_sms_chat' => 'boolean',
            'notification_sms_custom' => 'boolean',
            'notification_sms_system' => 'boolean',
            'is_active' => 'boolean',
            'moderated' => 'boolean',
            'deleted' => 'boolean',
            'balance' => 'decimal:10',
            'ref_balance' => 'decimal:10',
            'bonus_balance' => 'decimal:10',
            'password' => 'hashed',
            'product_fields_visibility' => 'array',
        ];
    }

    protected $appends = ['name'];

    /**
     * Boot the model and generate user_id if not set
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->user_id)) {
                $user->user_id = Str::uuid();
            }
            if (empty($user->reg_date)) {
                $user->reg_date = time();
            }
            if (empty($user->gen_key)) {
                $user->gen_key = Str::random(32);
            }
        });
    }

    /**
     * Get user's full name
     */
    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get user's name for display
     */
    public function getNameAttribute()
    {
        $fullName = trim($this->first_name . ' ' . $this->last_name);
        return $fullName ?: $this->user_name ?: $this->email;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === 1;
    }

    /**
     * Check if user is banned
     */
    public function isBanned()
    {
        return $this->banned || $this->deleted || !$this->is_active;
    }
}
