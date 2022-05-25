<?php

namespace Database\Factories\Modules\Core\Entities;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleContestation;

class SaleContestationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SaleContestation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $todayDate = $this->faker->date('Y-m-d','now');        
        return [
            'sale_id'=>Sale::factory(),
            'gateway_id'=>Gateway::SAFE2PAY_PRODUCTION_ID,
            'data'=>json_encode(['data'=>'teste']),
            'nsu'=>$this->faker->randomNumber(8),
            'gateway_case_number'=>$this->faker->randomNumber(8),
            'file_date'=>null,
            'transaction_date'=>$todayDate,
            'request_date'=>$todayDate,
            'reason'=>$this->getReasonRandom(),
            'observation'=>null,
            'is_contested'=>0,
            'file_user_completed'=>null,
            'expiration_date'=>$this->faker->date('Y-m-d','+10 days'),
            'status'=>rand(0,2)+1,
        ];
    }

    public function getReasonRandom(){

        $reasons = [
            'Bens e Servicos Nao Recebidos',
            'Credito nao processado',
            'Desacordo Comercial',
            'Erro de processamento',
            'Fraude em Ambiente de Cartao Nao Presente',
            'Mercadoria / Servico defeituoso ou diferente',
            'Mercadoria / Servicos Cancelado',
            'Mercadoria / Servicos nao recebidos',
            'Mercadoria com defeito ou em desacordo',
            'Mercadoria defeituosa/Nao confere com a descricao'
        ];

        return Arr::random($reasons);
    }
}