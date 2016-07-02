<?php

namespace sndsgd\form\rule;

class UploadedFileRule extends RuleAbstract
{
    /**
     * {@inheritdoc}
     */
    public function getDescription(): string
    {
        return _("type:file");
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorMessage(): string
    {
        return _("must be a file");
    }

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
