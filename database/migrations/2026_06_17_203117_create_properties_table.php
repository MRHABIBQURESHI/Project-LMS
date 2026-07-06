<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('slug')->unique()->nullable();
            $table->string('image')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->default('Pakistan')->nullable();
            $table->boolean('is_featured')->default(false)->nullable();
            $table->boolean('status')->default(true)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('developers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->text('website_url')->nullable();
            $table->text('number')->nullable();
            $table->text('email')->nullable();
            $table->string('slug')->unique()->nullable();
            $table->string('logo')->nullable();
            $table->boolean('status')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->nullable()->constrained('cities')->onDelete('set null');
            $table->foreignId('developer_id')->nullable()->constrained('developers')->onDelete('set null');
            $table->string('name')->nullable();
            $table->string('slug')->unique()->nullable();
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->year('completion_year')->nullable();
            $table->decimal('starting_price', 12, 2)->nullable();
            $table->boolean('is_featured')->default(false)->nullable();
            $table->boolean('status')->default(true)->nullable();
            $table->string('image')->nullable();
            $table->string('banner_image')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('property_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_featured')->default(false)->nullable();
            $table->boolean('status')->default(true)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');
            $table->foreignId('property_type_id')->nullable()->constrained('property_types')->onDelete('set null');
            $table->foreignId('city_id')->nullable()->constrained('cities')->onDelete('set null');
            $table->foreignId('developer_id')->nullable()->constrained('developers')->onDelete('set null');
            $table->string('name')->nullable();
            $table->string('slug')->unique()->nullable();
            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->integer('size')->nullable(); // in sq.ft
            $table->string('image')->nullable();
            $table->json('gallery_images')->nullable();
            $table->json('floor_plan_images')->nullable();
            $table->boolean('is_featured')->default(false)->nullable();
            $table->boolean('status')->default(true)->nullable();
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->integer('garages')->default(0)->nullable();
            $table->boolean('air_conditioning')->default(false)->nullable();
            $table->boolean('alarm')->default(false)->nullable();
            $table->boolean('balcony')->default(false)->nullable();
            $table->boolean('cable_tv')->default(false)->nullable();
            $table->boolean('central_heating')->default(false)->nullable();
            $table->boolean('dryer')->default(false)->nullable();
            $table->boolean('dishwasher')->default(false)->nullable();
            $table->boolean('garage')->default(false)->nullable();
            $table->boolean('gym')->default(false)->nullable();
            $table->boolean('library')->default(false)->nullable();
            $table->boolean('laundry_room')->default(false)->nullable();
            $table->boolean('microwave')->default(false)->nullable();
            $table->boolean('oven')->default(false)->nullable();
            $table->boolean('parking')->default(false)->nullable();
            $table->boolean('pets_allowed')->default(false)->nullable();
            $table->boolean('refrigerator')->default(false)->nullable();
            $table->boolean('security_system')->default(false)->nullable();
            $table->boolean('swimming_pool')->default(false)->nullable();
            $table->boolean('tennis_court')->default(false)->nullable();
            $table->boolean('tv_cable')->default(false)->nullable();
            $table->boolean('wifi')->default(false)->nullable();
            $table->boolean('washer')->default(false)->nullable();
            $table->boolean('wine_cellar')->default(false)->nullable();
            $table->json('location_map')->nullable();
            $table->string('video_url')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->nullable()->constrained('cities')->onDelete('set null');
            $table->foreignId('developer_id')->nullable()->constrained('developers')->onDelete('set null');
            $table->foreignId('property_type_id')->nullable()->constrained('property_types')->onDelete('set null');
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');
            $table->string('name')->nullable();
            $table->string('slug')->unique()->nullable();
            $table->string('designation')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('instagram')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('other_link')->nullable();
            $table->boolean('is_featured')->default(false)->nullable();
            $table->boolean('status')->default(false)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agents');
        Schema::dropIfExists('properties');
        Schema::dropIfExists('property_types');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('developers');
    }
};