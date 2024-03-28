<?php

namespace api\controllers;

class Socket
{
    /**
     * 作为客户端  原生请求
     */
    public function connect() {
        // 建立socket连接
        $sock = fsockopen('localhost', 9090, $errno, $errstr, 2);

        if (!$sock) {
            echo "$errstr ($errno)<br />\n";
        } else {
            // WebSocket握手
            $key     = base64_encode(openssl_random_pseudo_bytes(16));
            $headers = "GET / HTTP/1.1\r\n";
            $headers .= "Host: localhost:8080\r\n";
            $headers .= "Upgrade: websocket\r\n";
            $headers .= "Connection: Upgrade\r\n";
            $headers .= "Sec-WebSocket-Key: $key\r\n";
            $headers .= "Sec-WebSocket-Version: 13\r\n";
            $headers .= "\r\n";
            fwrite($sock, $headers);

            // 读取响应
            while (!feof($sock)) {
                $line = fgets($sock, 128);
                echo $line;
            }

            fclose($sock);
        }
    }

    public function create() {




    }
}