<?php

namespace RiverSkies\Laravel;

use Blade;
use Illuminate\Support\ServiceProvider;

class VueComponentServiceProvider extends ServiceProvider
{
    /**
     * List of expressions in case of nesting.
     *
     * @var array
     */
    protected $expressions = [];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('vue', function($expression) {
            $this->registerExpression($expression);

            return "
                <?php
                    if(isset{$expression}) {
                        echo '<component is=\"' . trim($expression, '()') . '\" inline-template>';
                    }
                ?>
            ";
        });

        Blade::directive('endvue', function() {
            $lastExpression = $this->popLastExpression();

            return "
                <?php
                    if(isset{$lastExpression}) {
                        echo '</component>';
                    }
                ?>
            ";
        });
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

    /**
     * Adds an expression to the expressions array.
     *
     * @param $expression
     */
    function registerExpression($expression)
    {
        array_push($this->expressions, $expression);
    }

    /**
     * Removes an expression from the extensions array.
     *
     * @throws \Exception
     * @return string
     */
    function popLastExpression()
    {
        if (empty($this->expressions)) {
            throw new \Exception('Cannot end a vue without first starting one.');
        }

        return array_pop($this->expressions);
    }
}
