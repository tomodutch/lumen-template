<?php

namespace App\Console\Commands;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;

class DataType
{
    const STRING_TYPES = [
        'string',
        'char',
        'ipAddress',
        'longText',
        'macAddress',
        'text'
    ];

    const INCREMENT_TYPES = [
        'bigIncrements',
        'increments',
        'mediumIncrements',
        'smallIncrements',
    ];

    const NUMERIC_TYPES = [
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
    ];

    const DATE_TYPES = [
        'date',
        'dateTime',
        'dateTimeTz',
        'time',
        'timeTz',
        'timestamp',
        'timestampTz'
    ];

    const BOOL_TYPES = [
        'boolean'
    ];

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

    /** @var  Collection */
    private $constraints;

    /**
     * DataType constructor.
     * @param string $name
     * @param string $type
     */
    public function __construct($name, $type, $isPrimary, $isNullable, $constraints)
    {
        $this->name = $name;
        $this->type = $type;
        $this->isPrimary = $isPrimary;
        $this->isNullable = $isNullable;
        $this->constraints = $constraints;

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
            $additionalRules[] = 'regex:/^\{?[A-F0-9]{8}-[A-F0-9]{4}-[A-F0-9]{4}-[A-F0-9]{4}-[A-F0-9]{12}\}?$/i';
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

        $constraintRules = $this->constraints->map(function ($values, $constraint) {
            if (count($values) === 0) {
                return $constraint;
            }

            if (in_array($constraint, ['in', 'not_in'])) {
                return '\Illuminate\Validation\Rule::notIn(' . json_encode($values) . ')';
            }


            return $constraint . ':' . implode(',', $values);
        })->toArray();

        return array_unique(array_merge($standardRules, $additionalRules, $constraintRules));
    }

    public function isPrimaryKey()
    {
        return $this->isPrimary || in_array($this->type, self::INCREMENT_TYPES);
    }

    public function isIncrements()
    {
        return in_array($this->type, self::INCREMENT_TYPES);
    }

    public function isBoolean()
    {
        return in_array($this->type, self::BOOL_TYPES);
    }

    public function isUuid()
    {
        return $this->type === 'uuid';
    }

    public function isDate()
    {
        return in_array($this->type, self::DATE_TYPES);
    }

    /**
     * @return bool
     */
    public function isString(): bool
    {
        return in_array($this->type, self::STRING_TYPES);
    }

    /**
     * @return bool
     */
    public function isNumeric(): bool
    {
        return in_array($this->type, self::NUMERIC_TYPES);
    }

    public static function fromString($definition)
    {
        list($name, $type, $constraints) = self::parse($definition);

        $rules = [];
        $isPrimary = false;
        $isNullable = false;
        foreach ($constraints as $option) {
            list($function, $arguments) = $constraints;
            switch (true) {
                case $option === 'primary':
                    $isPrimary = true;
                    break;
                case $option === 'nullable':
                    $isNullable = true;
                    break;
                case $function === 'default':
                case in_array($function, ['in', 'not_in']):
                case in_array($type, self::STRING_TYPES) && in_array($function, [
                        'accepted',
                        'active_url',
                        'after',
                        'after_or_equal',
                        'alpha',
                        'alpha_dash',
                        'alpha_num',
                        'before',
                        'before_or_equal',
                        'between',
                        'different',
                        'email',
                        'filled',
                        'ip',
                        'ipv4',
                        'ipv6',
                        'json',
                        'max',
                        'min',
                        'numeric',
                        'regex',
                        'required',
                        'required_if',
                        'required_unless',
                        'required_with',
                        'required_with_all',
                        'required_without',
                        'required_without_all',
                        'same',
                        'size',
                        'timezone',
                        'unique',
                        'url'
                    ]):
                case in_array($type, self::NUMERIC_TYPES) && in_array(strtolower($function), [
                        'min',
                        'max',
                        'between',
                        'between',
                        'different',
                        'digits',
                        'digits_between',
                        'exists',
                        'filled',
                        'integer',
                        'max',
                        'min',
                        'nullable',
                        'numeric',
                        'present',
                        'regex',
                        'required',
                        'required_if',
                        'required_unless',
                        'required_with',
                        'required_with_all',
                        'required_without',
                        'required_without_all',
                        'same',
                        'size',
                        'unique',
                    ]):
                case in_array($type, self::DATE_TYPES) && in_array($function, [
                        'before',
                        'before_or_equal',
                        'between',
                    ]):
                case in_array($type, self::BOOL_TYPES):
                case in_array($function, ['required']):
                    $rules[$function] = $arguments;
                    break;
                default:
                    throw new \InvalidArgumentException("Unrecognized option \"$option\" for type \"$name\"");
            }
        }

        return new DataType(
            $name,
            $type,
            $isPrimary,
            $isNullable,
            collect($rules));
    }

    private static function parse($input)
    {
        $pieces = explode(':', $input);

        if (count($pieces) < 2) {
            throw new \InvalidArgumentException("Expected datatype for argument \"$pieces[0]\". The correct format is <name>:<datatype>");
        }

        list($name, $type) = $pieces;
        $options = array_slice($pieces, 2);

        $constraints = [];
        foreach ($options as $option) {
            $argument = strpos($option, '[');
            if ($argument === false) {
                $argument = strlen($option);
            }

            $argumentList = json_decode(substr($option, $argument));
            $function = strtolower(substr($option, 0, $argument));
            $constraints = [$function, $argumentList];
        }

        return [$name, $type, $constraints];
    }
}
