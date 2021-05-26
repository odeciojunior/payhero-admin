<?php

namespace App\Console\Commands;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Modules\Core\Services\CloudFlareService;
use Modules\Plans\Http\Controllers\PlansApiController;

class GenericCommand extends Command
{
    protected $signature = 'generic {user?}';

    protected $description = 'Command description';

    // private $cloudflareService;

    // public function __construct()
    // {
    //     //parent::__construct();

    //     //$this->cloudflareService = new CloudFlareService();
    // }

    public function handle()
    {        
        $this->testConfigAditionalInformation();
    }

    public function testConfigAditionalInformation(){

        $controller = new PlansApiController();

        $request = new Request();
        $request->merge([
            'plan'=>680116,
            'update_all'=>false,
            'products'=>[
                [
                    'product'=>671054,
                    'details'=>[
                        ['type'=>'text','label'=>'Informe seu nome']
                    ]
                ],
                [
                    'product'=>671055,
                    'details'=>[
                        ['type'=>'file','label'=>'Adicione um arquivo']
                    ]
                ]
            ]
        ]);

        dd($controller->saveConfigCustomProducts($request));

    }
}



