<?php

namespace RiverSkies\Laravel;

class VueComponentDirective implements BladeDirectiveInterface
{
    /**
     * List of expressions in case of nesting.
     *
     * @var array
     */
    protected $expressions = [];

    /**
     * Returns the Blade opening tag.
     *
     * @return string
     */
    public function openingTag()
    {
        return 'vue';
    }

    /**
     * Compiles the Blade opening.
     *
     * @param $expression
     * @return mixed
     */
    public function openingHandler($expression)
    {
        $this->registerExpression($expression);

        return "
                <?php
                    if(isset{$expression}) {
                        echo '<component is=\"' . trim($expression, '()') . '\" inline-template>';
                    }
                ?>
            ";
    }

    /**
     * Returns the Blade closing tag.
     *
     * @return mixed
     */
    public function closingTag()
    {
        return 'endvue';
    }

    /**
     * Compiles the Blade closing.
     *
     * @param $expression
     * @return mixed
     */
    public function closingHandler($expression)
    {
        $lastExpression = $this->popLastExpression();

        return "
                <?php
                    if(isset{$lastExpression}) {
                        echo '</component>';
                    }
                ?>
            ";
    }

    /**
     * Adds an expression to the expressions array.
     *
     * @param $expression
     */
    private function registerExpression($expression)
    {
        array_push($this->expressions, $expression);
    }

    /**
     * Removes an expression from the extensions array.
     *
     * @throws \Exception
     * @return string
     */
    private function popLastExpression()
    {
        if (empty($this->expressions)) {
            throw new \Exception('Cannot end a vue without first starting one.');
        }

        return array_pop($this->expressions);
    }
}