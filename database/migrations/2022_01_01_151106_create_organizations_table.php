<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('fantasy_name');
            $table->string('corporate_name')->unique();
            $table->string('domain')->unique()->nullable();
            $table->string('cpf_cnpj', 14)->unique();
            $table->string('logo')->nullable();
            $table->string('social_contract')->nullable();
            $table->unsignedTinyInteger('organization_type')->constrained();
            $table->json('interests')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organizations');
    }
}
