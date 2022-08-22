<?php

namespace Database\Factories\Modules\Core\Entities;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Ticket;

class TicketFactory extends Factory
{
 /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Ticket::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'sale_id'=>Sale::factory(),
            'customer_id'=>null,
            'subject'=>$this->getRandomSubject(),
            'subject_enum'=>$this->faker->numberBetween(1, 8),
            'description'=>$this->faker->sentence(rand(6,10)),
            'ticket_category_enum'=>$this->faker->numberBetween(1, 3),
            'ticket_status_enum'=>$this->faker->numberBetween(1, 3),
            'last_message_type_enum'=>$this->faker->numberBetween(1, 3),
            'last_message_date'=>now(),
            'mediation_notified'=>rand(0,1),
            'ignore_balance_block'=>0,            
            'average_response_time'=>$this->faker->numberBetween(10, 3000),
        ];
    }

    public function getRandomSubject(){
        $subjects = [
            'Data da entrega',
            'Não resebi códico de rastreio',
            'Produto Nao entregue',
            'Vocês não entregam o produto, quero o dinheiro de volta',
            'Rastreio Mercadoria não recebi mais email de envio',
            'Quando chega meu produto?',
            'Porque à demora do produto',
            'Me passaram o código de rastreamento mas até agora não foi liberado para vizualição',
            'Prazo de entrega',
            'Comprei número errado',
            'Cancelar a compra',
            'Mudança de endereço',
            'Como faço para rastrear meu pedido?'
        ];
        return Arr::random($subjects);
    }
}