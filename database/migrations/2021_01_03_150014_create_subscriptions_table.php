<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description');
            $table->integer('price');
            $table->integer('duration');
            $table->integer('number_of_refreshes');
            $table->integer('refresh_price');
            $table->integer('number_of_raises');
            $table->integer('raise_price');
            $table->boolean('raise_three');
            $table->boolean('raise_ten');
            $table->integer('urgent_price');
            $table->integer('bargain_price');
            $table->boolean('featured_on_homepage');
            $table->boolean('featured_on_search_results_and_categories');

            $table->longText('config')->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
}
