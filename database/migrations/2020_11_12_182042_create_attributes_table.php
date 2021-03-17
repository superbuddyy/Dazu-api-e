<?php

declare(strict_types=1);

use App\Enums\AttributeType;
use App\Enums\AttributeUnit;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttributesTable extends Migration
{
    public function up(): void
    {
        Schema::create('attributes', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('name');
            $table->string('description', 100)
                ->nullable();
            $table->string('slug');
            $table->enum('type', AttributeType::getValues())
                ->default(AttributeType::BOOLEAN);
            $table->enum('unit', AttributeUnit::getValues())
                ->default(AttributeUnit::NONE);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attributes');
    }
}
