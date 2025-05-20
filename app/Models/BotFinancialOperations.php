<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property int|null $user_id
 * @property int $summa Пополнение,Расходы
 * @property string|null $type Tip
 * @property string $date
 * @property string|null $status Status
 * @property string|null $comment Примечание
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotFinancialOperations newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotFinancialOperations newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotFinancialOperations query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotFinancialOperations whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotFinancialOperations whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotFinancialOperations whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotFinancialOperations whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotFinancialOperations whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotFinancialOperations whereSumma($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotFinancialOperations whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotFinancialOperations whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotFinancialOperations whereUserId($value)
 * @property string $type_payment
 * @property string $telegram_who
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotFinancialOperations whereTelegramWho($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotFinancialOperations whereTypePayment($value)
 * @mixin \Eloquent
 */
class BotFinancialOperations extends Model
{
    const TYPE_IN = 'in';
    const TYPE_OUT = 'out';
    const TYPE_PAYMENT_CASH = 'cash';
    const TYPE_PAYMENT_TRANSFER = 'transfer';

    protected $table = 'bot_financial_operations';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'telegram_who' => 'array',
        ];
    }

    public function getTypePaymentLabel(): string
    {
        return match ($this->type_payment) {
            self::TYPE_PAYMENT_CASH => 'Наличные',
            self::TYPE_PAYMENT_TRANSFER => 'Перечисление',
            default => 'Неизвестно',
        };
    }

    public function getIsPaymentCash()
    {
        return $this->type_payment == self::TYPE_PAYMENT_CASH;
    }

    public function getIsTypeIn()
    {
        return $this->type == self::TYPE_IN;
    }

    public function getIsTypeOut()
    {
        return $this->type == self::TYPE_OUT;
    }

    public function getIsPaymentTransfer()
    {
        return $this->type_payment == self::TYPE_PAYMENT_TRANSFER;
    }
}
