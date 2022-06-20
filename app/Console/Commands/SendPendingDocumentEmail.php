<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Company;
use Modules\Core\Events\SendEmailPendingDocumentEvent;

class SendPendingDocumentEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:notify-pending-document';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $companies = DB::table('companies as c')->select('c.id','c.user_id','us.name','us.email')
        ->join('users as us','c.user_id','=','us.id')
        ->leftJoin('company_documents as doc','c.id','=','doc.company_id')
        ->whereNull('doc.id')->where('us.email_verified',true)
        ->whereNull('c.date_last_document_notification')->get();

        $userIds = [];
        foreach($companies as $company){
            $data = [
                'domainName'=>'Cloudfox',
                'clientEmail'=>$company->email,
                'clientName'=>explode(' ',$company->name)['0']??' Cliente',
                'companyId'=>$company->id
            ];            
            
            event(new SendEmailPendingDocumentEvent($data));

            $userIds[] = $company->user_id;
        }

        $users = DB::table('users as us')->select('us.id','us.name','us.email')        
        ->leftJoin('user_documents as doc','us.id','=','doc.user_id')
        ->whereNull('doc.id')->where('us.email_verified',true)
        ->whereNotIn('us.id',$userIds)
        ->whereNull('us.date_last_document_notification')->get();

        foreach($users as $user){
            $data = [
                'domainName'=>'Cloudfox',
                'clientEmail'=>$user->email,
                'clientName'=>explode(' ',$user->name)['0']??' Cliente',
                'userId'=>$user->id
            ];
            
            event(new SendEmailPendingDocumentEvent($data));
        }
    }
}
