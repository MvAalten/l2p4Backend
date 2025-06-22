// database/migrations/2024_01_01_000004_create_game_turns_table.php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('game_turns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->foreignId('player_id')->constrained('users')->onDelete('cascade');
            $table->string('guessed_word');
            $table->integer('turn_number');
            $table->json('result'); // Store color coding results
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('game_turns');
    }
};
