<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\GatewayFlag;
use Modules\Core\Entities\GatewayFlagTax;
use Modules\Core\Enums\Payments\CardFlagEnum;
use Modules\Core\Enums\Payments\GatewayEnum;
use Modules\Core\Services\FoxUtils;

return new class extends Migration {
    public function up(): void
    {
        $gateways = [
            [
                'id' => Gateway::MONETIX_PRODUCTION_ID,
                'name' => 'monetix_production',
                'json_config' => FoxUtils::xorEncrypt(json_encode([
                    'api_key' => config('integrations.gateways.monetix.production_token'),
                ], JSON_THROW_ON_ERROR)),
                'production_flag' => 1,
            ],
            [
                'id' => Gateway::MONETIX_SANDBOX_ID,
                'name' => 'monetix_sandbox',
                'json_config' => FoxUtils::xorEncrypt(json_encode([
                    'api_key' => config('integrations.gateways.monetix.sandbox_token'),
                ], JSON_THROW_ON_ERROR)),
                'production_flag' => 0,
            ]
        ];

        foreach ($gateways as $gateway) {
            Gateway::query()
                ->create([
                    'id' => $gateway['id'],
                    'gateway_enum' => GatewayEnum::MONETIX->value,
                    'name' => $gateway['name'],
                    'json_config' => $gateway['json_config'] ?? '',
                    'production_flag' => $gateway['production_flag'],
                    'enabled_flag' => 1,
                    'deleted_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        }

        $flags = [
            // Production
            [
                'gateway_id' => Gateway::MONETIX_PRODUCTION_ID,
                'slug' => 'visa',
                'name' => 'Visa',
                'card_flag_enum' => CardFlagEnum::VISA->value,
            ],
            [
                'gateway_id' => Gateway::MONETIX_PRODUCTION_ID,
                'slug' => 'elo',
                'name' => 'Elo',
                'card_flag_enum' => CardFlagEnum::ELO->value,
            ],
            [
                'gateway_id' => Gateway::MONETIX_PRODUCTION_ID,
                'slug' => 'amex',
                'name' => 'AMEX',
                'card_flag_enum' => CardFlagEnum::AMEX->value,
            ],
            [
                'gateway_id' => Gateway::MONETIX_PRODUCTION_ID,
                'slug' => 'mastercard',
                'name' => 'Master Card',
                'card_flag_enum' => CardFlagEnum::MASTER_CARD->value,
            ],
            [
                'gateway_id' => Gateway::MONETIX_PRODUCTION_ID,
                'slug' => 'hipercard',
                'name' => 'Hipercard',
                'card_flag_enum' => CardFlagEnum::HYPER_CARD->value,
            ],
            [
                'gateway_id' => Gateway::MONETIX_PRODUCTION_ID,
                'slug' => 'discover',
                'name' => 'Discover',
                'card_flag_enum' => CardFlagEnum::DISCOVER->value,
            ],

            // Sandbox
            [
                'gateway_id' => Gateway::MONETIX_SANDBOX_ID,
                'slug' => 'visa',
                'name' => 'Visa',
                'card_flag_enum' => CardFlagEnum::VISA->value,
            ],
            [
                'gateway_id' => Gateway::MONETIX_SANDBOX_ID,
                'slug' => 'elo',
                'name' => 'Elo',
                'card_flag_enum' => CardFlagEnum::ELO->value,
            ],
            [
                'gateway_id' => Gateway::MONETIX_SANDBOX_ID,
                'slug' => 'amex',
                'name' => 'AMEX',
                'card_flag_enum' => CardFlagEnum::AMEX->value,
            ],
            [
                'gateway_id' => Gateway::MONETIX_SANDBOX_ID,
                'slug' => 'mastercard',
                'name' => 'Master Card',
                'card_flag_enum' => CardFlagEnum::MASTER_CARD->value,
            ],
            [
                'gateway_id' => Gateway::MONETIX_SANDBOX_ID,
                'slug' => 'hipercard',
                'name' => 'Hipercard',
                'card_flag_enum' => CardFlagEnum::HYPER_CARD->value,
            ],
            [
                'gateway_id' => Gateway::MONETIX_SANDBOX_ID,
                'slug' => 'discover',
                'name' => 'Discover',
                'card_flag_enum' => CardFlagEnum::DISCOVER->value,
            ],
        ];

        $installmentsTax = [
            1 => 5.60,
            2 => 7.03,
            3 => 8.33,
            4 => 9.61,
            5 => 10.87,
            6 => 12.10,
            7 => 13.58,
            8 => 14.76,
            9 => 15.92,
            10 => 17.06,
            11 => 18.18,
            12 => 19.28,
        ];

        foreach ($flags as $flag) {
            $gatewayFlag = GatewayFlag::query()->create($flag);

            for ($i = 1; $i <= 12; $i++) {
                GatewayFlagTax::query()
                    ->create([
                        "gateway_flag_id" => $gatewayFlag->id,
                        "installments" => $i,
                        "type_enum" => 1,
                        "percent" => $installmentsTax[$i],
                    ]);
            }
        }
    }

    public function down(): void
    {
        GatewayFlagTax::query()
            ->whereIn('gateway_flag_id', function ($query) {
                $query->select('id')
                    ->from('gateway_flags')
                    ->whereIn('gateway_id', [Gateway::MONETIX_PRODUCTION_ID, Gateway::MONETIX_SANDBOX_ID]);
            })->delete();

        GatewayFlag::query()
            ->whereIn('gateway_id', [Gateway::MONETIX_PRODUCTION_ID, Gateway::MONETIX_SANDBOX_ID])
            ->delete();

        Gateway::query()
            ->whereIn('id', [Gateway::MONETIX_PRODUCTION_ID, Gateway::MONETIX_SANDBOX_ID])
            ->delete();
    }
};
