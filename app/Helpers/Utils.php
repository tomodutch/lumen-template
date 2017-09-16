<?php

if (function_exists('keysToSnakeCase') === false) {
    /**
     *
     * Convert array keys to snake_case
     *
     * @param array $input
     * @return array
     */
    function keysToSnakeCase(array $input)
    {
        $snakeCased = [];

        foreach ($input as $key => $value) {
            $snakeCased[snake_case($key)] = $value;
        }

        return $snakeCased;
    }
}
