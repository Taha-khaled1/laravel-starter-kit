<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_id')->nullable()->constrained('chats');
            # ربط مستلم بمستخدم
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            # ربط مستلم برسالة
            $table->foreignId('message_id')
                ->constrained('messages')
                ->cascadeOnDelete();

            # تاريخ ووقت قراءة الرسالة (اختياري)
            $table->timestamp('read_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recipients');
    }
};
