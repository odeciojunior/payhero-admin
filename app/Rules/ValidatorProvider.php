<?php

namespace App\Rules;

use Illuminate\Support\ServiceProvider;

class ValidatorProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $me = $this;
        $this->app["validator"]->resolver(function ($translator, $data, $rules, $messages, $attributes) use ($me) {
            $messages += $me->getMessages();

            return new Validator($translator, $data, $rules, $messages, $attributes);
        });
    }

    protected function getMessages()
    {
        return [
            "cnpj" => "CNPJ inválido",
            "cpf" => "CPF inválido",
            "template_string_min_visitor_in_finalizing_purchase_config" =>
                "É necessario ter o template {visitantes} no texto.",
        ];
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
