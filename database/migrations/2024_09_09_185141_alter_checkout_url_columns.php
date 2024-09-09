<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
            // Verificar se os índices existem antes de tentar removê-los
            $indexes = DB::select(DB::raw('SHOW INDEXES FROM checkouts'));

            $indexNames = array_column($indexes, 'Key_name');

            if (in_array('checkouts_src_IDX', $indexNames)) {
                $table->dropIndex('checkouts_src_IDX');
            }
            if (in_array('checkouts_referer_IDX', $indexNames)) {
                $table->dropIndex('checkouts_referer_IDX');
            }
            if (in_array('checkouts_original_url_IDX', $indexNames)) {
                $table->dropIndex('checkouts_original_url_IDX');
            }
            if (in_array('checkouts_utm_source_IDX', $indexNames)) {
                $table->dropIndex('checkouts_utm_source_IDX');
            }
            if (in_array('checkouts_utm_medium_IDX', $indexNames)) {
                $table->dropIndex('checkouts_utm_medium_IDX');
            }
            if (in_array('checkouts_utm_campaign_IDX', $indexNames)) {
                $table->dropIndex('checkouts_utm_campaign_IDX');
            }
            if (in_array('checkouts_utm_term_IDX', $indexNames)) {
                $table->dropIndex('checkouts_utm_term_IDX');
            }
            if (in_array('checkouts_utm_content_IDX', $indexNames)) {
                $table->dropIndex('checkouts_utm_content_IDX');
            }
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
            // Recriar os índices removidos
            $table->index('src', 'checkouts_src_IDX');
            $table->index('referer', 'checkouts_referer_IDX');
            $table->index('original_url', 'checkouts_original_url_IDX');
            $table->index('utm_source', 'checkouts_utm_source_IDX');
            $table->index('utm_medium', 'checkouts_utm_medium_IDX');
            $table->index('utm_campaign', 'checkouts_utm_campaign_IDX');
            $table->index('utm_term', 'checkouts_utm_term_IDX');
            $table->index('utm_content', 'checkouts_utm_content_IDX');
        });
    }
};
