<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if(Schema::hasTable('messages')) {
            // migrate legacy messages by re-sending them with the new system
            $messages = DB::table('messages')->get();
            $messages->each(function ($message) {
                App\Helpers\ChatHelper::sendMessage($message->author_id, $message->user_id, $message->text, $message->time);
            });

            // set all the newly migrated chats to read rather than
            // attempt to see which should be read - because there's basically no
            // unread legacy messages
            App\Models\Chat::query()->update(['is_read' => true]);
        }
        Schema::dropIfExists('messages');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id')->unsigned();
            $table->integer('author_id')->unsigned();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')->onDelete('cascade');

            $table->foreign('author_id')
                ->references('id')
                ->on('users')->onDelete('cascade');

            $table->text('text')->nullable();
            $table->boolean('read')->default(false);
            $table->timestamp('time');
            $table->timestamps();
        });
    }
};
