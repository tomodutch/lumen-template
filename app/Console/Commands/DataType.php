<?php

namespace App\Console\Commands;


use App\Rules\ValidUUID;
use Illuminate\Database\Schema\Blueprint;

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

    private $isPrimary = false;

    private $isNullable = true;

    /**
     * DataType constructor.
     * @param string $name
     * @param string $type
     */
    public function __construct($name, $type, $isPrimary, $isNullable)
    {
        $this->name = $name;
        $this->type = $type;
        $this->isPrimary = $isPrimary;
        $this->isNullable = $isNullable;

        if ($this->isValidType() === false) {
            throw new \InvalidArgumentException("Type \"$type\" is not recognized");
        }

        if ($isPrimary && $isNullable) {
            throw new \InvalidArgumentException("Primary key should not be nullable for type \"$name\"");
        }
    }

    public function isValidType()
    {
        $blueprint = new Blueprint('');

        return method_exists($blueprint, $this->type);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    public function isNullable()
    {
        return $this->isNullable;
    }

    public function getRules()
    {
        $standardRules = ['required'];
        $additionalRules = [];
        if ($this->isNullable()) {
            $standardRules = ['nullable'];
        }

        if ($this->isUuid()) {
            $additionalRules[] = 'string';
            $additionalRules[] = '\\' . ValidUUID::class;
        }

        if ($this->isString()) {
            $additionalRules[] = 'string';
        }

        if ($this->isNumeric()) {
            $additionalRules[] = 'numeric';
        }

        if ($this->isDate()) {
            $additionalRules[] = 'date';
        }

        if ($this->isBoolean()) {
            $additionalRules[] = 'boolean';
        }

        return array_merge($standardRules, $additionalRules);
    }

    public function isPrimaryKey()
    {
        return $this->isPrimary || $this->isIncrements();
    }

    public function isIncrements()
    {
        return in_array($this->type, [
            'bigIncrements',
            'increments',
            'mediumIncrements',
            'smallIncrements',
        ]);
    }

    public function isBoolean()
    {
        return $this->type === 'boolean';
    }

    public function isUuid()
    {
        return $this->type === 'uuid';
    }

    public function isDate()
    {
        return in_array($this->type, [
            'date',
            'dateTime',
            'dateTimeTz',
            'time',
            'timeTz',
            'timestamp',
            'timestampTz',
        ]);
    }

    /**
     * @return bool
     */
    public function isString(): bool
    {
        return in_array($this->type, [
            'string',
            'char',
            'ipAddress',
            'longText',
            'macAddress',
            'text'
        ]);
    }

    /**
     * @return bool
     */
    public function isNumeric(): bool
    {
        return in_array($this->type, [
            'bigIncrements',
            'increments',
            'mediumIncrements',
            'smallIncrements',
            'decimal',
            'double',
            'float',
            'integer',
            'mediumInteger',
            'smallInteger',
            'tinyInteger',
            'unsignedBigInteger',
            'unsignedInteger',
            'unsignedMediumInteger',
            'unsignedSmallInteger',
            'unsignedTinyInteger'
        ]);
    }

    public static function fromString($definition)
    {
        $pieces = explode(':', $definition);

        if (count($pieces) < 2) {
            throw new \InvalidArgumentException("Expected datatype for argument \"$pieces[0]\". The correct format is <name>:<datatype>");
        }

        list($name, $type) = $pieces;
        $options = array_slice($pieces, 2);

        $isPrimary = false;
        $isNullable = false;
        foreach ($options as $option) {
            switch (true) {
                case $option === 'primary':
                    $isPrimary = true;
                    break;
                case $option === 'nullable':
                    $isNullable = true;
                    break;
                default:
                    throw new \InvalidArgumentException("Unrecognized option \"$option\" for type \"$name\"");
            }
        }

        return new DataType($name, $type, $isPrimary, $isNullable);
    }
}
