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

    /**
     * DataType constructor.
     * @param string $name
     * @param string $type
     */
    public function __construct($name, $type)
    {
        $this->name = $name;
        $this->type = $type;

        if ($this->isValidType($type) === false) {
            throw new \InvalidArgumentException("Type \"$type\" is not recognized");
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

        if (in_array($this->type, [
            'date',
            'dateTime',
            'dateTimeTz',
            'time',
            'timeTz',
            'timestamp',
            'timestampTz',
        ])) {
            $additionalRules[] = 'date';
        }

        return array_merge($standardRules, $additionalRules);
    }

    public function isPrimaryKey()
    {
        return in_array($this->type, [
            'bigIncrements',
            'increments',
            'mediumIncrements',
            'smallIncrements',
        ]);
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
