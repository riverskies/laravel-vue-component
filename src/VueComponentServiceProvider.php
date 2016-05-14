<?php

namespace Riveskies\Laravel\VueComponent;

use Blade;
use Illuminate\Support\ServiceProvider;

class VueComponentServiceProvider extends ServiceProvider
{
    protected $directive;

    public function __construct()
    {
        $this->directive = new VueComponentDirective;
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive(
            $this->directive->openingTag(), [$this->directive, 'openingHandler']
        );

        Blade::directive(
            $this->directive->closingTag(), [$this->directive, 'closingHandler']
        );
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
