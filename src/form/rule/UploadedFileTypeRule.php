<?php

namespace sndsgd\form\rule;

/**
 * Ensure an uploaded file is of a given type(s)
 */
class UploadedFileTypeRule extends RuleAbstract
{
    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getDescription(): string
    {
        $types = implode(",", $this->mimeTypes);
        return "file-type:$types";
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
     * {@inheritdoc}
     */
    public function getErrorMessage(): string
    {
        $types = \sndsgd\Arr::implode(", ", $this->getWrappedOptions(), "or ");
        if ($this->errorMessage === null) {
            return "must be a file of the following types: $types";
        }
        return sprintf($this->errorMessage, $types);
    }

    /**
     * {@inheritdoc}
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
