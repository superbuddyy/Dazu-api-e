<?php

use App\Enums\CompanyType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('avatar')
                ->nullable();
            $table->timestamp('avatar_expire_date')
                ->nullable();
            $table->string('video_avatar')
                ->nullable();
            $table->timestamp('video_avatar_expire_date')
                ->nullable();
            $table->enum('type', CompanyType::getValues())
                ->default(CompanyType::AGENCY);

            $table->softDeletes();
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
        Schema::dropIfExists('company');
    }
}
