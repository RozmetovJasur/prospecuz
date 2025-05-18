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
        Schema::create('bot_financial_operations', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable()->default(null);
            $table->bigInteger('summa')->default(0)->comment("Пополнение,Расходы");
            $table->string('type')->nullable()->comment("Tip");
            $table->date('date');
            $table->string('status')->nullable()->comment("Status");
            $table->string('comment')->nullable()->comment("Примечание");
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->integer('telegram_code')->nullable();
            $table->string('telegram_chat_id')->nullable();
            $table->bigInteger('main_balance')->default(0)->comment('Баланс');
            $table->bigInteger('in_summa')->default(0)->comment('Пополнение');
            $table->bigInteger('out_summa')->default(0)->comment('Расходы');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bot_financial_operations');
    }
};
