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
        $standardRules = [];
        if ($this->isNullable()) {
            $standardRules[] = 'nullable';
        } else {
            $standardRules[] = 'required';
        }

        $additionalRules = [];

        if ($this->type === 'uuid') {
            $additionalRules[] = 'string';
            $additionalRules[] = '\\' . ValidUUID::class;
        }

        if (in_array($this->type, [
            'string',
            'char',
            'ipAddress',
            'longText',
            'macAddress',
            'text'
        ])) {
            $additionalRules[] = 'string';
        }

        if (in_array($this->type, [
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
        ])) {
            $additionalRules[] = 'numeric';
        }

        if ($this->isDate()) {
            $additionalRules[] = 'date';
        }

        if ($this->type === 'boolean') {
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
