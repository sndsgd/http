<?php

namespace sndsgd\http\data\decoder;

class MultipartDataDecoderTest extends \PHPUnit_Framework_TestCase
{
    private function getDir($name = null)
    {
        $dir = realpath(__DIR__."/../../../data");
        return ($name === null) ? $dir : "$dir/$name";
    }

    private function getParameterDetails()
    {
        $path = $this->getDir()."/parameters.json";
        $json = file_get_contents($path);
        $data = json_decode($data, true);
        exit;
    }

    /**
     * @dataProvider providerDecodeVarious
     */
    public function testDecodeVarious($path, $type, $length, $options)
    {
        $decoder = new MultipartDataDecoder($path, $type, $length, $options);
        $values = $decoder->decode();

        # verify the decoded files match those that were uploaded
        $filesDir = $this->getDir("files");
        foreach ($values["file"] as $file) {
            $sourceHash = md5_file("$filesDir/".$file->getClientFilename());
            $decodedHash = md5_file($file->getTempPath());
            $this->assertSame($sourceHash, $decodedHash);
        }
    }

    public function providerDecodeVarious()
    {
        $ret = [];
        $dir = $this->getDir("multipart/various");
        $files = glob("$dir/*.content");

        foreach ($files as $contentFile) {
            $typeFile = substr($contentFile, 0, -7)."type";
            $ret[] = [
                $contentFile,
                file_get_contents($typeFile),
                filesize($contentFile),
                null,
            ];
        }
        return $ret;
    }

    /**
     * @expectedException Exception
     */
    public function testDecodeReadException()
    {
        $path = __FILE__."/does/not/exists.obviously";
        $type = "multipart/form-data; boundary=3ce079ead76547fa9261";
        (new MultipartDataDecoder($path, $type, 100))->decode();
    }

    /**
     * @expectedException \sndsgd\http\data\DecodeException
     */
    public function testgetBoundaryException()
    {
        $path = __FILE__;
        $type = "multipart/form-data";
        (new MultipartDataDecoder($path, $type, 100))->decode();   
    }

    /**
     * @expectedException \sndsgd\http\data\DecodeException
     */
    public function testMalformedContentDispositionException()
    {
        $path = $this->getDir("multipart")."/malformed-content-disposition.content";
        $type = "multipart/form-data; boundary=3ce079ead76547fa9261ae19db4febb3";
        (new MultipartDataDecoder($path, $type, 100))->decode();
    }
}
