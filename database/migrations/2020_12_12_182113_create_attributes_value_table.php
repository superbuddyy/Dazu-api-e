<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttributesValueTable extends Migration
{
    public function up(): void
    {
        Schema::create('attribute_value', function (Blueprint $table): void {
            $table->increments('id');
            $table->text('value');
            $table->uuid('offer_id');
            $table->foreign('offer_id')
                ->references('id')
                ->on('offers')
                ->onDelete('cascade');
            $table->unsignedInteger('attribute_id');
            $table->foreign('attribute_id')
                ->references('id')
                ->on('attributes')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attribute_value');
    }
}
