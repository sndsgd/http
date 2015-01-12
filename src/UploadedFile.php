<?php

namespace sndsgd\http;

use \sndsgd\Temp;


/**
 * A processed uploaded file
 */
class UploadedFile
{
   use \sndsgd\issue\Manager;

   /**
    * The name of the input field
    *
    * @var string
    */
   protected $name;

   /**
    * The basename of the uploaded file (filename.ext)
    *
    * @var string
    */
   protected $filename;

   /**
    * The absolute path to the uploaded file in the temp directory
    *
    * @var string
    */
   protected $tempPath;

   /**
    * The content type of the file
    *
    * @var string
    */
   protected $contentType;

   /**
    * The bytesize of the file
    *
    * @var integer
    */
   protected $size;

   /**
    * @param string $name
    * @param string $filename
    * @param string $contentType
    */
   public function __construct($name, $filename, $contentType)
   {
      $this->name = $name;
      $this->filename = $filename;
      $this->contentType = $contentType;
   }

   /**
    * Get the temp path to the file
    *
    * @return string An absolute file path
    */
   public function getTempPath()
   {
      if ($this->tempPath === null) {
         $this->tempPath = Temp::file("uploaded-file-{$this->filename}");
      }
      return $this->tempPath;
   }

   /**
    * Get the name of the file as it was saved on the client computer
    *
    * @return string
    */
   public function getFilename()
   {
      return $this->filename;
   }

   /**
    * Set the size of the file
    *
    * @param integer $bytes
    * @return sndsgd\http\UploadedFile
    */
   public function setSize($bytes)
   {
      $this->size = $bytes;
      return $this;
   }

   /**
    * Rename the file to move it from the temp path
    *
    * @param string $path The absolute path to move the file to
    * @return boolean
    */
   public function move($path)
   {
      return @rename($this->tempPath, $path);
   }
}

