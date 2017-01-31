<?php

namespace sndsgd\form\rule;

/**
 * Ensure an uploaded file is of a given type(s)
 */
class UploadedFileTypeRule extends RuleAbstract
{
    /**
     * @inheritDoc
     */
    protected $errorMessage = null;

    /**
     * The acceptable mime types
     *
     * @var array<string>
     */
    protected $mimeTypes;

    /**
     * @param string ...$mimeTypes The acceptable mime types
     */
    public function __construct(string ...$mimeTypes)
    {
        $this->mimeTypes = $mimeTypes;
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        $template = _("file-type:%s");
        $types = implode(",", $this->mimeTypes);
        return sprintf($template, $types);
    }

    /**
     * Get the available options wrapped in single quotes
     *
     * @return array<string>
     */
    private function getWrappedOptions()
    {
        $ret = [];
        foreach ($this->mimeTypes as $type) {
            $ret[] = var_export($type, true);
        }
        return $ret;
    }

    /**
     * @inheritDoc
     */
    public function getErrorMessage(): string
    {
        if ($this->errorMessage === null) {
            $template = (count($this->mimeTypes) === 1)
                ? _("must be a file of the following type: %s")
                : _("must be a file of the following types: %s");
        } else {
            $template = $this->errorMessage;
        }

        $types = implode(", ", $this->getWrappedOptions());
        return sprintf($template, $types);
    }

    /**
     * @inheritDoc
     */
    public function validate(
        &$value,
        \sndsgd\form\Validator $validator = null
    ): bool
    {
        return (
            $value instanceof \sndsgd\http\UploadedFile &&
            $value->isType($this->mimeTypes)
        );
    }
}
