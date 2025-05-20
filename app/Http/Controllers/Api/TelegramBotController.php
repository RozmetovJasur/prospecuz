<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BotFinancialOperations;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use WeStacks\TeleBot\TeleBot;

class TelegramBotController extends Controller
{
    private TeleBot $bot;

    public function __construct()
    {
        $this->bot = new TeleBot(env('TELEGRAM_REPORT_BOT_TOKEN'));
    }

    public function index(Request $request)
    {
        $chatID = $request->input('message.chat.id');
        $messageText = $request->input('message.text');
        $messageId = $request->input('message.message_id');
        $from = $request->input('message.from');

//        Log::info("chatId:" . $chatID);
//        Log::info("input:" . json_encode($request->all(), JSON_UNESCAPED_UNICODE));

//        if (!in_array($chatID, self::$allowedAcceptChatId)) {
//            return;
//        }

        try {

            if (!empty($messageText)) {
                $user = User::where('telegram_chat_id', $chatID)->first();

                if (empty($user)) {
                    return $this->activateUser($chatID, $messageText);
                }

                if ($messageText == '/reset') {
                    Cache::put('reset', 'reset', 60);

                    return $this->bot->sendMessage([
                        'chat_id' => $chatID,
                        'reply_parameters' => [
                            'message_id' => $messageId
                        ],
                        'parse_mode' => 'HTML',
                        'text' => "⚠️ Подтвердите YES или NO."
                    ]);
                }

                if (Cache::has('reset') && $messageText == 'YES') {

                    User::where('id', $user->id)->update([
                        'in_summa' => 0,
                        'out_summa' => 0
                    ]);

                    Cache::forget('reset');

                    return $this->bot->sendMessage([
                        'chat_id' => $chatID,
                        'reply_parameters' => [
                            'message_id' => $messageId
                        ],
                        'parse_mode' => 'HTML',
                        'text' => "✅ Данные за текущий месяц успешно удалены."
                    ]);
                }

                if (Cache::has('reset') && $messageText == 'NO') {
                    Cache::forget('reset');
                    return $this->bot->sendMessage([
                        'chat_id' => $chatID,
                        'reply_parameters' => [
                            'message_id' => $messageId
                        ],
                        'parse_mode' => 'HTML',
                        'text' => "✅ Действие было успешно отменено."
                    ]);
                }

                $lines = explode("\n", trim($messageText));

                if (count($lines) == 4 && str_contains($messageText, '🟢 Пополнение')
                    && str_contains($messageText, '💰 Тип оплата')
                    && str_contains($messageText, '✍️ Примечание')
                    && str_contains($messageText, '📅 Дата')) {

                    $patterns = [
                        '/(?<=🟢 Пополнение: ).+/',
                        '/(?<=💰 Тип оплата: ).+/',
                        '/(?<=✍️ Примечание: ).+/',
                        '/(?<=📅 Дата: ).+/'
                    ];

                    $transactionData = [];
                    foreach ($patterns as $key => $pattern) {
                        preg_match($pattern, $messageText, $matches);
                        $result = $matches[0] ?? null;
                        $transactionData[] = $key == 0 ? str_replace([' ', ' ', ' '], ['', '', ''], $result) : $result;
                    }

                    if (count($transactionData) == 4) {

                        [$summa, $type, $comment, $date] = $transactionData;

                        /** @var User $user */
                        $cashBalance = $user->cash_balance;
                        $transferBalance = $user->transfer_balance;
                        $inCashSumma = $user->in_cash_summa;
                        $inTransferSumma = $user->in_transfer_summa;

                        $payment = new BotFinancialOperations();
                        $payment->user_id = $user->id;
                        $payment->summa = $summa;
                        $payment->comment = $comment;
                        $payment->type = $payment::TYPE_IN;
                        $payment->telegram_who = $from;
                        $payment->type_payment = str_contains($type, 'Налич') ? $payment::TYPE_PAYMENT_CASH : $payment::TYPE_PAYMENT_TRANSFER;
                        $payment->date = $date;
                        $payment->saveOrFail();

                        $user = $this->operationProcess($payment, $user);

                        return $this->bot->sendMessage([
                            'chat_id' => $user->telegram_chat_id,
                            'reply_parameters' => [
                                'message_id' => $messageId
                            ],
                            'parse_mode' => 'HTML',
                            'text' => "✅ Успешно!" . PHP_EOL . PHP_EOL .
                                "🟢 Пополнение: " . nf($payment->summa) . PHP_EOL .
                                "📅 Дата: " . $payment->date . PHP_EOL .
                                "✍️ Примечание: " . $payment->comment . PHP_EOL . PHP_EOL .

                                "💵 Приход за текущий месяц(Наличные): " . (balanceFormat($inCashSumma, $user->in_cash_summa)) . PHP_EOL .
                                "💵 Приход за текущий месяц(Перечисление): " . (balanceFormat($inTransferSumma, $user->in_transfer_summa)) . PHP_EOL . PHP_EOL .

                                "🏦 Баланс(Наличные): " . (balanceFormat($cashBalance, $user->cash_balance)) . PHP_EOL .
                                "🏦 Баланс(Перечисление): " . (balanceFormat($transferBalance, $user->transfer_balance))
                        ]);

                    }

                }

                if (count($lines) == 4 && str_contains($messageText, '🔴 Расходы')
                    && str_contains($messageText, '💰 Тип оплата')
                    && str_contains($messageText, '✍️ Примечание')
                    && str_contains($messageText, '📅 Дата')) {

                    $patterns = [
                        '/(?<=🔴 Расходы: ).+/',
                        '/(?<=💰 Тип оплата: ).+/',
                        '/(?<=✍️ Примечание: ).+/',
                        '/(?<=📅 Дата: ).+/'
                    ];

                    $transactionData = [];
                    foreach ($patterns as $key => $pattern) {
                        preg_match($pattern, $messageText, $matches);
                        $result = $matches[0] ?? null;
                        $transactionData[] = $key == 0 ? str_replace([' ', ' ', ' '], ['', '', ''], $result) : $result;
                    }

                    if (count($transactionData) == 4) {

                        [$summa, $type, $comment, $date] = $transactionData;

                        /** @var User $user */
                        $cashBalance = $user->cash_balance;
                        $transferBalance = $user->transfer_balance;
                        $outCashSumma = $user->out_cash_summa;
                        $outTransferSumma = $user->out_transfer_summa;

                        $payment = new BotFinancialOperations();
                        $payment->user_id = $user->id;
                        $payment->summa = $summa;
                        $payment->comment = $comment;
                        $payment->date = $date;
                        $payment->telegram_who = $from;
                        $payment->type = $payment::TYPE_OUT;
                        $payment->type_payment = str_contains($type, 'Налич') ? $payment::TYPE_PAYMENT_CASH : $payment::TYPE_PAYMENT_TRANSFER;
                        $payment->saveOrFail();

                        $user = $this->operationProcess($payment, $user);
                        return $this->bot->sendMessage([
                            'chat_id' => $user->telegram_chat_id,
                            'reply_parameters' => [
                                'message_id' => $messageId
                            ],
                            'parse_mode' => 'HTML',
                            'text' => "✅ Успешно!" . PHP_EOL . PHP_EOL .
                                "🔴 Расходы: " . nf($payment->summa) . PHP_EOL .
                                "📅 Дата: " . $payment->date . PHP_EOL .
                                "✍️ Примечание: " . $payment->comment . PHP_EOL . PHP_EOL .

                                "💵 Расходы за текущий месяц(Наличные): " . (balanceFormat($outCashSumma, $user->out_cash_summa)) . PHP_EOL .
                                "💵 Расходы за текущий месяц(Перечисление): " . (balanceFormat($outTransferSumma, $user->out_transfer_summa)) . PHP_EOL . PHP_EOL .

                                "🏦 Баланс(Наличные): " . (balanceFormat($cashBalance, $user->cash_balance)) . PHP_EOL .
                                "🏦 Баланс(Перечисление): " . (balanceFormat($transferBalance, $user->transfer_balance))
                        ]);
                    }
                }

                if ($messageText == '/balance') {

                    /** @var $user User */
                    return $this->bot->sendMessage([
                        'chat_id' => $user->telegram_chat_id,
                        'reply_parameters' => [
                            'message_id' => $messageId
                        ],
                        'parse_mode' => 'HTML',
                        'text' => "🟢 Приход за текущий месяц(Наличные): " . (nf($user->in_cash_summa)) . PHP_EOL .
                            "🟢 Приход за текущий месяц(Перечисление): " . (nf($user->in_transfer_summa)) . PHP_EOL . PHP_EOL .

                            "🔴 Расход за текущий месяц(Наличные): " . (nf($user->out_cash_summa)) . PHP_EOL .
                            "🔴 Расход за текущий месяц(Перечисление): " . (nf($user->out_transfer_summa)) . PHP_EOL . PHP_EOL .

                            "🏦 Баланс(Наличные): " . (nf($user->cash_balance)) . PHP_EOL .
                            "🏦 Баланс(Перечисление): " . (nf($user->transfer_balance))
                    ]);
                }
            }
        } catch (Exception $e) {
            return $this->bot->sendMessage([
                'chat_id' => $chatID,
                'text' => $e->getMessage()
            ]);
        }
    }

    private function operationProcess(BotFinancialOperations $botFinancialOperation, User $user)
    {
        if ($botFinancialOperation->getIsTypeIn()) {

            if ($botFinancialOperation->getIsPaymentCash()) {
                $user->cash_balance += $botFinancialOperation->summa;
                $user->in_cash_summa += $botFinancialOperation->summa;
            } else {
                $user->transfer_balance += $botFinancialOperation->summa;
                $user->in_transfer_summa += $botFinancialOperation->summa;
            }
        }

        if ($botFinancialOperation->getIsTypeOut()) {
            if ($botFinancialOperation->getIsPaymentCash()) {
                $user->cash_balance -= $botFinancialOperation->summa;
                $user->out_cash_summa += $botFinancialOperation->summa;
            } else {
                $user->transfer_balance -= $botFinancialOperation->summa;
                $user->out_transfer_summa += $botFinancialOperation->summa;
            }
        }

        $user->saveOrFail();

        return $user;
    }

    private function activateUser($chatID, $messageText)
    {
        $user = User::where('telegram_code', intval($messageText))->first();
        if ($user) {
            $user->telegram_code = null;
            $user->telegram_chat_id = $chatID;
            $user->saveOrFail();

            return $this->bot->sendMessage([
                'chat_id' => $chatID,
                'text' => "Вы успешно активировали бот.",
            ]);
        }

        return $this->sendMessage($chatID, 'Введите код для подключения к системе.');
    }

    private function sendMessage($chatID, $text)
    {
        return $this->bot->sendMessage(['chat_id' => $chatID, 'text' => $text]);
    }
}
