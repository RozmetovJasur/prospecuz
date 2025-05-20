<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $telegram_code
 * @property string|null $telegram_chat_id
 * @property int $main_balance Баланс
 * @property int $in_summa Пополнение
 * @property int $out_summa Расходы
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereInSumma($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMainBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereOutSumma($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTelegramChatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTelegramCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @property int $cash_balance Баланс наличные
 * @property int $transfer_balance Баланс перечисления
 * @property int $in_cash_summa Пополнение наличные
 * @property int $in_transfer_summa Пополнение перечисления
 * @property int $out_cash_summa Расходы наличные
 * @property int $out_transfer_summa Расходы перечисления
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCashBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereInCashSumma($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereInTransferSumma($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereOutCashSumma($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereOutTransferSumma($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTransferBalance($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
