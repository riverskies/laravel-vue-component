<?php

namespace RiverSkies\Laravel;

interface VueDirectiveInterface
{
    /**
     * Returns the Blade opening tag.
     *
     * @return string
     */
    public function openingTag();

    /**
     * Compiles the Blade opening.
     *
     * @param $expression
     * @return mixed
     */
    public function openingHandler($expression);

    /**
     * Returns the Blade closing tag.
     *
     * @return mixed
     */
    public function closingTag();

    /**
     * Compiles the Blade closing.
     *
     * @param $expression
     * @return mixed
     */
    public function closingHandler($expression);
}