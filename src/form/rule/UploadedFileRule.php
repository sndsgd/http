<?php

namespace sndsgd\form\rule;

class UploadedFileRule extends RuleAbstract
{
    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return _("type:file");
    }

    /**
     * @inheritDoc
     */
    public function getErrorMessage(): string
    {
        return _("must be a file");
    }

    /**
     * @inheritDoc
     */
    public function validate(
        &$value,
        \sndsgd\form\Validator $validator = null
    ): bool
    {
        return ($value instanceof \sndsgd\http\UploadedFile);
    }
}
