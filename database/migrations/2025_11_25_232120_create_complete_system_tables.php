<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Certificate table
        Schema::create('certificate', function (Blueprint $table) {
            $table->id('certificate_id');
            $table->string('certificate_type')->comment('نوع الشهادة أو اسمها');
            $table->unsignedBigInteger('nutritionist_id');
            $table->timestamps();
            
            $table->foreign('nutritionist_id')->references('nutritionist_id')->on('nutritionists')->onDelete('cascade');
        });

        // 2. Locations table
        Schema::create('locations', function (Blueprint $table) {
            $table->id('location_id');
            $table->decimal('latitude_x', 10, 8);
            $table->decimal('longitude_y', 11, 8);
            $table->string('description')->nullable()->comment('وصف الموقع');
            $table->timestamps();
        });

        // 3. Clients table
        Schema::create('clients', function (Blueprint $table) {
            $table->unsignedBigInteger('clients_id')->primary();
            $table->unsignedBigInteger('diets_id')->nullable()->comment('الحمية الحالية');
            $table->timestamps();
            
            $table->foreign('clients_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('diets_id')->references('diets_id')->on('diets')->onDelete('set null');
        });

        // 4. Client Addresses (pivot)
        Schema::create('client_addresses', function (Blueprint $table) {
            $table->unsignedBigInteger('clients_id');
            $table->unsignedBigInteger('location_id');
            $table->timestamps();
            
            $table->primary(['clients_id', 'location_id']);
            $table->foreign('clients_id')->references('clients_id')->on('clients')->onDelete('cascade');
            $table->foreign('location_id')->references('location_id')->on('locations')->onDelete('cascade');
        });

        // 5. Lifestyle table
        Schema::create('lifestyle', function (Blueprint $table) {
            $table->unsignedBigInteger('clients_id')->primary();
            $table->boolean('smoking')->default(false)->comment('هل المستخدم مدخن');
            $table->string('activity_level', 50)->comment('مستوى النشاط البدني');
            $table->decimal('sleeping_hours', 4, 2)->comment('عدد ساعات النوم');
            $table->timestamps();
            
            $table->foreign('clients_id')->references('clients_id')->on('clients')->onDelete('cascade');
        });

        // 6. Body Data table
        Schema::create('body_data', function (Blueprint $table) {
            $table->unsignedBigInteger('clients_id')->primary();
            $table->date('birth_date');
            $table->decimal('height_cm', 5, 2)->comment('الطول بالسنتيمتر');
            $table->decimal('weight_kg', 5, 2)->comment('الوزن بالكيلوغرام');
            $table->enum('sex', ['Male', 'Female']);
            $table->timestamps();
            
            $table->foreign('clients_id')->references('clients_id')->on('clients')->onDelete('cascade');
        });

        // 7. Chronic Diseases table
        Schema::create('chronic_diseases', function (Blueprint $table) {
            $table->id('chronic_diseases_id');
            $table->string('chronic_diseases', 100)->unique()->comment('اسم المرض المزمن');
            $table->timestamps();
        });

        // 8. Medical Record table
        Schema::create('medical_record', function (Blueprint $table) {
            $table->id('medical_record_id');
            $table->text('medical_record')->nullable()->comment('وصف السجل الطبي');
            $table->timestamps();
        });

        // 9. Clients Allergies (pivot)
        Schema::create('clients_allergies', function (Blueprint $table) {
            $table->unsignedBigInteger('clients_id');
            $table->unsignedBigInteger('allergies_id');
            $table->timestamps();
            
            $table->primary(['clients_id', 'allergies_id']);
            $table->foreign('clients_id')->references('clients_id')->on('clients')->onDelete('cascade');
            $table->foreign('allergies_id')->references('allergies_id')->on('allergies')->onDelete('cascade');
        });

        // 10. Clients Medical Record (pivot)
        Schema::create('clients_medical_record', function (Blueprint $table) {
            $table->unsignedBigInteger('clients_id');
            $table->unsignedBigInteger('medical_record_id');
            $table->timestamps();
            
            $table->primary(['clients_id', 'medical_record_id']);
            $table->foreign('clients_id')->references('clients_id')->on('clients')->onDelete('cascade');
            $table->foreign('medical_record_id')->references('medical_record_id')->on('medical_record')->onDelete('cascade');
        });

        // 11. Clients Chronic Diseases (pivot)
        Schema::create('clients_chronic_diseases', function (Blueprint $table) {
            $table->unsignedBigInteger('clients_id');
            $table->unsignedBigInteger('chronic_diseases_id');
            $table->timestamps();
            
            $table->primary(['clients_id', 'chronic_diseases_id']);
            $table->foreign('clients_id')->references('clients_id')->on('clients')->onDelete('cascade');
            $table->foreign('chronic_diseases_id')->references('chronic_diseases_id')->on('chronic_diseases')->onDelete('cascade');
        });

        // 12. Restaurants table
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id('restaurants_id');
            $table->string('name')->comment('اسم المطعم');
            $table->string('photo_url')->nullable()->comment('صورة المطعم');
            $table->timestamps();
        });

        // 13. Phone table
        Schema::create('phone', function (Blueprint $table) {
            $table->id('phone_id');
            $table->string('phone_number', 20)->comment('رقم الهاتف');
            $table->unsignedBigInteger('restaurants_id');
            $table->timestamps();
            
            $table->foreign('restaurants_id')->references('restaurants_id')->on('restaurants')->onDelete('cascade');
        });

        // 14. Restaurant Locations (pivot)
        Schema::create('restaurant_locations', function (Blueprint $table) {
            $table->unsignedBigInteger('restaurants_id');
            $table->unsignedBigInteger('location_id');
            $table->timestamps();
            
            $table->primary(['restaurants_id', 'location_id']);
            $table->foreign('restaurants_id')->references('restaurants_id')->on('restaurants')->onDelete('cascade');
            $table->foreign('location_id')->references('location_id')->on('locations')->onDelete('cascade');
        });

        // 15. Restaurant Categories (pivot)
        Schema::create('restaurant_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('restaurants_id');
            $table->unsignedBigInteger('category_id');
            $table->timestamps();
            
            $table->primary(['restaurants_id', 'category_id']);
            $table->foreign('restaurants_id')->references('restaurants_id')->on('restaurants')->onDelete('cascade');
            $table->foreign('category_id')->references('category_id')->on('category')->onDelete('cascade');
        });

        // 16. Cart table
        Schema::create('cart', function (Blueprint $table) {
            $table->id('cart_id');
            $table->date('date');
            $table->time('time');
            $table->decimal('total_price', 10, 2);
            $table->string('state', 50)->comment('حالة الطلب');
            $table->unsignedBigInteger('clients_id');
            $table->unsignedBigInteger('location_id')->nullable();
            $table->unsignedBigInteger('restaurants_id')->nullable()->comment('المطعم المرتبط بالطلب');
            $table->timestamps();
            
            $table->foreign('clients_id')->references('clients_id')->on('clients')->onDelete('cascade');
            $table->foreign('location_id')->references('location_id')->on('locations')->onDelete('set null');
            $table->foreign('restaurants_id')->references('restaurants_id')->on('restaurants')->onDelete('set null');
        });

        // 17. Cart Item table
        Schema::create('cart_item', function (Blueprint $table) {
            $table->id('cart_item_id');
            $table->integer('quantity')->default(1)->comment('كمية الوجبة');
            $table->unsignedBigInteger('cart_id');
            $table->unsignedBigInteger('meals_id');
            $table->timestamps();
            
            $table->foreign('cart_id')->references('cart_id')->on('cart')->onDelete('cascade');
            $table->foreign('meals_id')->references('meals_id')->on('meals')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_item');
        Schema::dropIfExists('cart');
        Schema::dropIfExists('restaurant_categories');
        Schema::dropIfExists('restaurant_locations');
        Schema::dropIfExists('phone');
        Schema::dropIfExists('restaurants');
        Schema::dropIfExists('clients_chronic_diseases');
        Schema::dropIfExists('clients_medical_record');
        Schema::dropIfExists('clients_allergies');
        Schema::dropIfExists('medical_record');
        Schema::dropIfExists('chronic_diseases');
        Schema::dropIfExists('body_data');
        Schema::dropIfExists('lifestyle');
        Schema::dropIfExists('client_addresses');
        Schema::dropIfExists('clients');
        Schema::dropIfExists('locations');
        Schema::dropIfExists('certificate');
    }
};
