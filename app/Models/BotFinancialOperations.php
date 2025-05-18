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
 * @mixin \Eloquent
 */
class BotFinancialOperations extends Model
{
    const TYPE_IN = 'in';
    const TYPE_OUT = 'out';

    protected $table = 'bot_financial_operations';
}
