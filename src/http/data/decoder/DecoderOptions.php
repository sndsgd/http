<?php

namespace sndsgd\http\data\decoder;

class DecoderOptions
{
    /**
     * The max upload filesize in bytes
     *
     * @var int
     */
    protected $maxFileSize;

    /**
     * Retrieve the `max_input_vars` ini setting
     *
     * @return int
     */
    public function getMaxVars(): int
    {
        return (int) ini_get("max_input_vars");
    }

    /**
     * Retrieve the `max_input_nesting_level` ini setting
     *
     * @return int
     */
    public function getMaxNestingLevels(): int
    {
        return (int) ini_get("max_input_nesting_level");
    }

    /**
     * Retrieve the `enable_post_data_reading` ini setting
     *
     * @return bool
     */
    public function getPostDataReadingEnabled(): bool
    {
        return (bool) ini_get("enable_post_data_reading");
    }

    /**
     * Retrieve the `max_file_uploads` ini setting
     *
     * @return int
     */
    public function getMaxFileCount(): int
    {
        return (int) ini_get("max_file_uploads");
    }

    /**
     * Retrieve the `upload_max_filesize` ini setting
     *
     * @return int
     */
    public function getMaxFileSize(): int
    {
        if ($this->maxFileSize === null) {
            $value = ini_get("upload_max_filesize");
            $units = "BKMGT";
            $unit = preg_replace("/[^$units]/i", "", $value);
            $value = floatval($value);
            if ($unit) {
                $value *= pow(1024, stripos($units, $unit[0]));
            }
            $this->maxFileSize = (int) $value;
        }
        return $this->maxFileSize;
    }
}
