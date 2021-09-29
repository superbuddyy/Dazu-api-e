<?php

declare(strict_types=1);

use App\Enums\OfferStatus;
use App\Enums\OfferType;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->uuid('id');
            $table->primary('id');

            $table->string('title');
            $table->string('slug')
                ->unique();
            $table->bigInteger('price');
            $table->bigInteger('raise_price')->default(0);
            $table->bigInteger('raise_price_three')->default(0);
            $table->bigInteger('raise_price_ten')->default(0);
            $table->bigInteger('urgent_price')->default(0);
            $table->bigInteger('bargain_price')->default(0);

            $table->enum('status', OfferStatus::getValues())
                ->default(OfferStatus::IN_ACTIVE);
            $table->string('lat');
            $table->string('lon');
            $table->string('location_name');
            $table->string('note')
                ->nullable();
            $table->json('links');
            $table->integer('refresh_count')
                ->default(0);
            $table->integer('raise_count')
                ->default(0);
            $table->timestamp('raise_at')
                ->nullable();
            $table->auditable();
            $table->softDeletes();
            $table->timestamp('expire_time')->nullable();
            $table->timestamp('visible_from_date')->nullable();
            $table->unsignedInteger('category_id');
            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');

            $table->uuid('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->boolean('has_raise_one')->nullable()->default(null);
            $table->boolean('has_raise_three')->nullable()->default(null);
            $table->boolean('has_raise_ten')->nullable()->default(null);
            $table->boolean('is_urgent')->nullable()->default(null);
            $table->boolean('is_bargain')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
}
