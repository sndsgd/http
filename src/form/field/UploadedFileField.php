<?php

namespace sndsgd\form\field;

class UploadedFileField extends ValueField
{
    public function __construct(string $name = "")
    {
        parent::__construct($name);
        $this->addRule(new \sndsgd\form\rule\UploadedFileRule());
    }

    public function getType(): string
    {
        return "file";
    }
}
