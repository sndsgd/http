<?php

namespace sndsgd\http;

class UploadedFileError extends \sndsgd\Error
{
    /**
     * @param int $code The php upload error value
     */
    public function __construct(int $code)
    {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                $this->message = "uploaded file exceeds `upload_max_filesize` in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $this->message = "uploaded file exceeds `MAX_FILE_SIZE` in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $this->message = "uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $this->message = "no file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $this->message = "uploaded file temp directory does not exist";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $this->message = "failed to write uploaded file";
                break;
            case UPLOAD_ERR_EXTENSION:
                $this->message = "file upload failed due to an extension";
                break;
            default:
                throw new \InvalidArgumentException(
                    "invalid value provided for 'code'; ".
                    "expecting one of the php `UPLOAD_ERR_*` constants"
                );
        }
        $this->code = $code;
    }
}
