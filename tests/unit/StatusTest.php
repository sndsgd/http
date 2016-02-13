<?php

namespace sndsgd\http;

/**
 * @coversDefaultClass \sndsgd\http\Status
 */
class CodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::getText
     * @dataProvider providerGetText
     */
    public function testGetText($code, $expect, $exception = null)
    {
        if ($exception === null) {
            $this->assertSame($expect, Status::getText($code));
        }
        else {
            $this->setExpectedException($exception);
            Status::getText($code);
        }
    }

    public function providerGetText()
    {
        return [
            [100, "Continue"],
            [101, "Switching Protocols"],
            [102, "Processing"],
            [200, "OK"],
            [201, "Created"],
            [202, "Accepted"],
            [203, "Non-Authoritative Information"],
            [204, "No Content"],
            [205, "Reset Content"],
            [206, "Partial Content"],
            [300, "Multiple Choices"],
            [301, "Moved Permanently"],
            [302, "Found"],
            [303, "See Other"],
            [304, "Not Modified"],
            [305, "Use Proxy"],
            [307, "Temporary Redirect"],
            [400, "Bad Request"],
            [401, "Unauthorized"],
            [402, "Payment Required"],
            [403, "Forbidden"],
            [404, "Not Found"],
            [405, "Method Not Allowed"],
            [406, "Not Acceptable"],
            [407, "Proxy Authentication Required"],
            [408, "Request Timeout"],
            [409, "Conflict"],
            [410, "Gone"],
            [411, "Length Required"],
            [412, "Precondition Failed"],
            [413, "Request Entity Too Large"],
            [414, "Request-URI Too Long"],
            [415, "Unsupported Media Type"],
            [416, "Requested Range Not Satisfiable"],
            [417, "Expectation Failed"],
            [428, "Precondition Required"],
            [429, "Too Many Requests"],
            [431, "Request Header Fields Too Large"],
            [500, "Internal Server Error"],
            [501, "Not Implemented"],
            [502, "Bad Gateway"],
            [503, "Service Unavailable"],
            [504, "Gateway Timeout"],
            [505, "HTTP Version Not Supported"],
            [507, "Insufficient Storage"],
            [508, "Loop Detected"],
            [509, "Bandwidth Limit Exceeded"],
            [511, "Network Authentication Required"],

            [1, null, "InvalidArgumentException"],
            [600, null, "InvalidArgumentException"],
        ];
    }

    /**
     * @covers ::getGroup
     * @dataProvider providerGetGroup
     */
    public function testGetGroup($code, $expect, $exception = null)
    {
        if ($exception === null) {
            $this->assertSame($expect, Status::getGroup($code));
        }
        else {
            $this->setExpectedException($exception);
            Status::getGroup($code);
        }
    }

    public function providerGetGroup()
    {
        return [
            [100, "1xx"],
            [200, "2xx"],
            [300, "3xx"],
            [400, "4xx"],
            [500, "5xx"],

            [10, null, "InvalidArgumentException"],
            [600, null, "InvalidArgumentException"],
        ];
    }
}
