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

            $config = [
                'is_bargain' => true,
                'is_urgent' => true,
                'free_refreshes' => 1,
                'bump_prices' => [
                    1 => '2.99',
                    3 => '3.99',
                    10 => '9.99'
                ]
            ];

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
