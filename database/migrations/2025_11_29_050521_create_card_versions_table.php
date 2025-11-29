<?php

use App\Models\Card;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('card_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Card::class, 'card_id')->constrained('cards')->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->longText('json');
            $table->string('preview_thumb')->nullable();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::table('cards', function (Blueprint $table) {
            $table->foreign('current_version_id')
                ->references('id')
                ->on('card_versions')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->dropForeign(['current_version_id']);
        });

        Schema::dropIfExists('card_versions');
    }
};
