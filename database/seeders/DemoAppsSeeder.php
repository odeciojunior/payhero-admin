<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Entities\ActivecampaignIntegration;
use Modules\Core\Entities\AstronMembersIntegration;
use Modules\Core\Entities\HotbilletIntegration;
use Modules\Core\Entities\HotzappIntegration;
use Modules\Core\Entities\MelhorenvioIntegration;
use Modules\Core\Entities\NotazzIntegration;
use Modules\Core\Entities\NotificacoesInteligentesIntegration;
use Modules\Core\Entities\ReportanaIntegration;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\SmartfunnelIntegration;
use Modules\Core\Entities\UnicodropIntegration;
use Modules\Core\Entities\Whatsapp2Integration;

class DemoAppsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ActivecampaignIntegration::factory()->create();

        AstronMembersIntegration::factory()->create();

        HotbilletIntegration::factory()->create();

        HotzappIntegration::factory()->create();

        MelhorenvioIntegration::factory()->create();

        NotazzIntegration::factory()->create();

        ReportanaIntegration::factory()->create();

        ShopifyIntegration::factory()->create();

        SmartfunnelIntegration::factory()->create();

        UnicodropIntegration::factory()->create();

        Whatsapp2Integration::factory()->create();

        NotificacoesInteligentesIntegration::factory()->create();
    }
}
