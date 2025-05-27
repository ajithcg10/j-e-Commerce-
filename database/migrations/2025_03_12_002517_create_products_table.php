<?php

use App\Models\User;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title', 2000);
            $table->string('slug', 2000);
            $table->longText('description');
            $table->foreignId('department_id')->index()->constrained('departments');
            $table->foreignId('categorey_id')->index()->constrained('categories');
            $table->decimal('price', 20, 4);
            $table->string('status')->index();
            $table->integer('qunatity')->nullable();
            $table->foreignIdFor(User::class, 'created_by');
            $table->foreignIdFor(User::class, 'updated_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
public function down(): void
{
    // Safely drop the foreign key constraint using raw SQL
    if (Schema::hasTable('variation_types')) {
        DB::statement('ALTER TABLE variation_types DROP CONSTRAINT IF EXISTS variation_types_product_id_foreign');
    }

    // Drop the products table
    Schema::dropIfExists('products');
}



};
