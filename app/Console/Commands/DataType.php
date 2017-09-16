<?php

namespace App\Console\Commands;


use App\Rules\ValidUUID;

class DataType
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * DataType constructor.
     * @param string $name
     * @param string $type
     */
    public function __construct($name, $type)
    {
        if ($this->isValidType($type) === false) {
            throw new \InvalidArgumentException("Type \"$type\" is not recognized");
        }

        $this->name = $name;
        $this->type = $type;
    }

    public function isValidType()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    public function getRules()
    {
        $standardRules = ['required'];
        $additionalRules = [];

        if ($this->type === 'uuid') {
            $additionalRules[] = '\\' . ValidUUID::class;
        }

        if ($this->type === 'string') {
            $additionalRules[] = 'string';
        }

        return array_merge($standardRules, $additionalRules);
    }

    public static function fromString($definition)
    {
        $pieces = explode(':', $definition);

        if (count($pieces) < 2) {
            throw new \InvalidArgumentException("Expected datatype for argument \"$pieces[0]\". The correct format is <name>:<datatype>");
        }

        list($name, $type) = $pieces;

        return new DataType($name, $type);
    }
}
