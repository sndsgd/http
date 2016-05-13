<?php

namespace sndsgd\form\rule;

/**
 * Ensure a value is an integer
 */
class UploadedFileRule extends RuleAbstract
{
    /**
     * {@inheritdoc}
     */
    protected $description = "type:file";

    /**
     * {@inheritdoc}
     */
    protected $errorMessage = "must be a file";

    /**
     * {@inheritdoc}
     */
    public function validate(
        &$value,
        \sndsgd\form\Validator $validator = null
    ): bool
    {
        return ($value instanceof \sndsgd\http\UploadedFile);
    }
}
