<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('checkouts', function (Blueprint $table) {
            // Alterar os campos para TEXT
            $table->text('referer')->collation('utf8mb4_unicode_ci')->change();
            $table->text('utm_source')->collation('utf8mb4_unicode_ci')->change();
            $table->text('utm_medium')->collation('utf8mb4_unicode_ci')->change();
            $table->text('utm_campaign')->collation('utf8mb4_unicode_ci')->change();
            $table->text('utm_term')->collation('utf8mb4_unicode_ci')->change();
            $table->text('utm_content')->collation('utf8mb4_unicode_ci')->change();
            $table->text('src')->collation('utf8mb4_unicode_ci')->change();
            $table->text('original_url')->collation('utf8mb4_unicode_ci')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('checkouts', function (Blueprint $table) {
            // Reverter os campos para VARCHAR(255)
            $table->string('referer', 255)->collation('utf8mb4_unicode_ci')->change();
            $table->string('utm_source', 255)->collation('utf8mb4_unicode_ci')->change();
            $table->string('utm_medium', 255)->collation('utf8mb4_unicode_ci')->change();
            $table->string('utm_campaign', 255)->collation('utf8mb4_unicode_ci')->change();
            $table->string('utm_term', 255)->collation('utf8mb4_unicode_ci')->change();
            $table->string('utm_content', 255)->collation('utf8mb4_unicode_ci')->change();
            $table->string('src', 255)->collation('utf8mb4_unicode_ci')->change();
            $table->string('original_url', 255)->collation('utf8mb4_unicode_ci')->change();
        });
    }
};
