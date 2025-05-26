<?php

declare(strict_types=1);

use App\Enums\CivilStatus;
use App\Enums\Gender;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('people', function (Blueprint $table): void {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('dob')->nullable();
            $table->enum('gender', array_column(Gender::cases(), 'value'));
            $table->enum('civil_status', array_column(CivilStatus::cases(), 'value'))->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('spiritual_information', function (Blueprint $table): void {
            $table->unsignedBigInteger('person_id')->nullable()->after('id');
            $table->foreign('person_id')->references('id')->on('people')->onDelete('cascade');

            $table->date('membership_at')->nullable();
            $table->date('baptized_at')->nullable();
            $table->date('saved_at')->nullable();
            $table->text('testimony')->nullable();
        });

        // Contact information table
        Schema::create('contact_information', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('person_id');
            $table->foreign('person_id')->references('id')->on('people')->onDelete('cascade');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('alternate_phone')->nullable();
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people');
        Schema::dropIfExists('spiritual_information');
        Schema::dropIfExists('contact_information');
    }
};
