<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $users = DB::select("SELECT count(id) as total, owner_id FROM sales WHERE api_flag = true AND api_token_id IS NULL AND deleted_at IS NULL GROUP BY owner_id");
        foreach ($users as $user)
        {
            $tokens = DB::table('api_tokens')->select('id','user_id','company_id','created_at')
                        ->where('user_id',$user->owner_id)->where('integration_type_enum',4)->whereNull('deleted_at')
                        ->get();

            foreach ($tokens as $token)
            {
                $sales = Sale::where('api_flag',true)->where('owner_id',$token->user_id)->whereNull('api_token_id')
                        ->whereHas('transactions',function($qr) use($token){
                            $qr->where('type',Transaction::TYPE_PRODUCER)->where('company_id',$token->company_id);
                        })->get();

                foreach ($sales as $sale) {
                    $sale->update([
                        'api_token_id'=>$token->id
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
};
