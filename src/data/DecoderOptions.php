<?php

namespace sndsgd\http\data;

class DecoderOptions
{
    protected $maxVars;
    protected $maxNestingLevels;
    protected $maxFileCount;
    protected $maxFileSize;

    /**
     * Stubbable method for retrieving ini values
     *
     * @param string $name
     * @return mixed
     */
    public function readValue(string $name)
    {
        return ini_get($name);
    }

    /**
     * Retrieve the `max_input_vars` ini setting
     *
     * @return int
     */
    public function getMaxVars(): int
    {
        if ($this->maxVars === null) {
            $this->maxVars = (int) $this->readValue("max_input_vars");
        }
        return $this->maxVars;
    }

    /**
     * Retrieve the `max_input_nesting_level` ini setting
     *
     * @return int
     */
    public function getMaxNestingLevels()
    {
        if ($this->maxNestingLevels === null) {
            $this->maxNestingLevels = (int) $this->readValue("max_input_nesting_level");
        }
        return $this->maxNestingLevels;
    }

    /**
     * Retrieve the `max_file_uploads` ini setting
     *
     * @return int
     */
    public function getMaxFileCount(): int
    {
        if ($this->maxFileCount === null) {
            $this->maxFileCount = (int) $this->readValue("max_file_uploads");
        }
        return $this->maxFileCount;
    }

    /**
     * Retrieve the `upload_max_filesize` ini setting
     *
     * @return int
     */
    public function getMaxFileSize(): int
    {
        if ($this->maxFileSize === null) {
            $value = $this->readValue("upload_max_filesize");
            $units = "BKMGT";
            $unit = preg_replace("/[^$units]/i", "", $value);
            $value = floatval($value);
            if ($unit) {
                $value *= pow(1024, stripos($units, $unit[0]));
            }
            $this->maxFileSize = (int) $value;

            // $value = str_replace("M", "000000", $value);
            // $value = str_replace("K", "000", $value);
        }
        return $this->maxFileSize;
    }
}
