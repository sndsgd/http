[
    {
        "url": "http://google.com/?",
        "content_type": "text/html; charset=UTF-8",
        "http_code": 301,
        "header_size": 354,
        "request_size": 82,
        "filetime": -1,
        "ssl_verify_result": 0,
        "redirect_count": 0,
        "total_time": 0.060751,
        "namelookup_time": 0.005112,
        "connect_time": 0.022593,
        "pretransfer_time": 0.022662,
        "size_upload": 0,
        "size_download": 219,
        "speed_download": 3604,
        "speed_upload": 0,
        "download_content_length": 219,
        "upload_content_length": 0,
        "starttransfer_time": 0.06071,
        "redirect_time": 0,
        "redirect_url": "http://www.google.com/",
        "primary_ip": "167.206.252.237",
        "certinfo": [],
        "primary_port": 80,
        "local_ip": "10.0.1.206",
        "local_port": 63414
    },
    "HTTP/1.1 301 Moved Permanently\r\nLocation: http://www.google.com/\r\nContent-Type: text/html; charset=UTF-8\r\nDate: Mon, 20 Jul 2015 01:33:43 GMT\r\nExpires: Wed, 19 Aug 2015 01:33:43 GMT\r\nCache-Control: public, max-age=2592000\r\nServer: gws\r\nContent-Length: 219\r\nX-XSS-Protection: 1; mode=block\r\nX-Frame-Options: SAMEORIGIN\r\nAlternate-Protocol: 80:quic,p=0\r\n\r\n<HTML><HEAD><meta http-equiv=\"content-type\" content=\"text/html;charset=utf-8\">\n<TITLE>301 Moved</TITLE></HEAD><BODY>\n<H1>301 Moved</H1>\nThe document has moved\n<A HREF=\"http://www.google.com/\">here</A>.\r\n</BODY></HTML>\r\n"
]