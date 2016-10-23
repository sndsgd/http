<?php

namespace sndsgd\http\data;

/**
 * A collection of parameters decoded from an http request
 */
class Collection implements \Countable
{
    /**
     * The 'max_input_vars' ini setting
     *
     * @var int
     */
    protected $maxVars;

    /**
     * The 'max_input_nesting_level' ini setting
     *
     * @var int
     */
    protected $maxNestingLevels;

    /**
     * The number of values in the collection
     *
     * @var int
     */
    protected $count = 0;

    /**
     * @var array<string,mixed>
     */
    protected $values = [];

    /**
     * Create a collection of parameters
     */
    public function __construct(int $maxVars, int $maxNestingLevels)
    {
        $this->maxVars = $maxVars;
        $this->maxNestingLevels = $maxNestingLevels;
    }

    /**
     * @see http://php.net/manual/en/class.countable.php
     * @return int
     */
    public function count(): int
    {
        return $this->count;
    }

    /**
     * Get all values in the collection
     *
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * Add a value to the collection
     *
     * @param string|number $key The index/key to add the value under
     * @param string|\sndsgd\http\UploadedFile $value The value to add
     */
    public function addValue(string $key, $value)
    {
        # ensure the total input variables doesn't exceed `max_input_variables`
        if ($this->count === $this->maxVars) {
            throw new DecodeException("max_input_variables exceeded");
        }

        # if the key has both open and close brackets
        # and the close comes after the open the key contains nesting
        if (
            ($openPos = strpos($key, "[")) !== false &&
            ($closePos = strpos($key, "]")) !== false &&
            $openPos < $closePos
        ) {
            $this->addNestedValue($key, $value, $openPos);
        } else {
            \sndsgd\Arr::addValue($this->values, $key, $value);
        }

        $this->count++;
    }

    /**
     * Add a value that has a nested key
     *
     * @param string $key
     * @param mixed $value
     * @param integer $pos The position of the first open bracket
     */
    protected function addNestedValue(string $key, $value, int $pos)
    {
        # convert the levels into an array of strings
        $levels = (strpos($key, "][") !== false)
            ? explode("][", substr($key, $pos + 1, -1))
            : [substr($key, $pos + 1, -1)];

        array_unshift($levels, substr($key, 0, $pos));

        # ensure the nesting doesn't exceed `max_nesting_levels`
        $levelLen = count($levels);
        if ($levelLen > $this->maxNestingLevels) {
            throw new DecodeException("max_input_nesting_level exceeded");
        }

        $lastKey = array_pop($levels);
        $values = &$this->values;
        foreach ($levels as $level) {
            if ($level === "") {
                $level = count($values);
            }
            if (!array_key_exists($level, $values)) {
                $values[$level] = [];
            }
            $values = &$values[$level];
        }

        if ($lastKey === "") {
            $lastKey = count($values);
        }

        \sndsgd\Arr::addValue($values, $lastKey, $value);
    }
}
