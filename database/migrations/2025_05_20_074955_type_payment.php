<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'main_balance',
                'in_summa',
                'out_summa'
            ]);
        });

        Schema::table('bot_financial_operations', function (Blueprint $table) {
            $table->string('type_payment');
            $table->json('telegram_who');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('cash_balance')->default(0)->comment('Баланс наличные');
            $table->bigInteger('transfer_balance')->default(0)->comment('Баланс перечисления');
            $table->bigInteger('in_cash_summa')->default(0)->comment('Пополнение наличные');
            $table->bigInteger('in_transfer_summa')->default(0)->comment('Пополнение перечисления');
            $table->bigInteger('out_cash_summa')->default(0)->comment('Расходы наличные');
            $table->bigInteger('out_transfer_summa')->default(0)->comment('Расходы перечисления');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'cash_balance',
                'transfer_balance',
                'in_cash_summa',
                'in_transfer_summa',
                'out_cash_summa',
                'out_transfer_summa',
            ]);
        });

        Schema::table('bot_financial_operations', function (Blueprint $table) {
            $table->dropColumn('type_payment');
            $table->dropColumn('telegram_who');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('main_balance')->default(0);
            $table->bigInteger('in_summa')->default(0);
            $table->bigInteger('out_summa')->default(0);
        });
    }
};
