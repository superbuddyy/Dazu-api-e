<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfferSubscriptionsTable extends Migration
{
    public function up(): void
    {
        Schema::create('offer_subscriptions', function (Blueprint $table): void {
            $table->increments('id');
            $table->boolean('urgent');
            $table->boolean('bargain');
            $table->integer('raises');
            $table->timestamp('end_date');

            $table->uuid('offer_id');
            $table->foreign('offer_id')
                ->references('id')
                ->on('offers')
                ->onDelete('cascade');

            $table->unsignedInteger('subscription_id');
            $table->foreign('subscription_id')
                ->references('id')
                ->on('subscriptions')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_subscriptions');
    }
}
