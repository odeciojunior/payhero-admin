<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\User;

class DemoProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::whereNull("is_cloudfox")->first();

        Product::create([
            "user_id" => $user->id,
            "name" => "Controle Game Com Fio 360 Computador Notebook ",
            "project_id" => 1,
            "description" => "Diversas cores ",
            "photo" =>
                "https://nexuspay-digital-products.s3.amazonaws.com/admin/yWZ8XSaNH1oZU7P4EtWpgn7kjWoPPwxOGG4NI3Q0.png",
            "price" => 210,
        ]);

        Product::create([
            "user_id" => $user->id,
            "name" => "Relógio Smartwatch Bluetooh",
            "project_id" => 1,
            "description" => "Android/IOS",
            "photo" =>
                "https://nexuspay-digital-products.s3.amazonaws.com/admin/2rkbIwt9ljoNUqRWH2f1kWjToTRjrJu5vDBQIrVn.png",
            "price" => 140,
        ]);

        Product::create([
            "user_id" => $user->id,
            "name" => "Kit 3 USB-C Para Celulares, Notebooks e Videogames",
            "project_id" => 1,
            "description" => "Diversos tamanhos",
            "photo" =>
                "https://nexuspay-digital-products.s3.amazonaws.com/admin/krsodNjYgN7up9LNPlMsV6mSLnTR1hAGwY5s8V5W.png",
            "price" => 112,
        ]);

        Product::create([
            "user_id" => $user->id,
            "name" => "Headphone Gamer Sem Fio Bluetooth",
            "project_id" => 1,
            "description" => "USB, Som Surround 7.1, Drivers 50mm",
            "photo" =>
                "https://nexuspay-digital-products.s3.amazonaws.com/admin/fhwhvhynQ914DZAtoLtakcaGRYb5tGRPjUU2sOuQ.png",
            "price" => 130,
        ]);
    }
}
