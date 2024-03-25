<?php
// 创建一个socket服务器

// 设置 IP 和端口
$host = '127.0.0.1';
$port = 9090;

// 创建 socket
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false) {
    echo "socket_create() 失败: " . socket_strerror(socket_last_error()) . "\n";
    exit;
}

// 绑定到 IP 和端口
$result = socket_bind($socket, $host, $port);
if ($result === false) {
    echo "socket_bind() 失败: " . socket_strerror(socket_last_error($socket)) . "\n";
    exit;
}

// 监听端口
$result = socket_listen($socket, 5);
if ($result === false) {
    echo "socket_listen() 失败: " . socket_strerror(socket_last_error($socket)) . "\n";
    exit;
}

// 接受客户端连接
do {
    $client_socket = socket_accept($socket);
    if ($client_socket !== false) {
        $msg = "Welcome PHP Socket \n";
        socket_write($client_socket, $msg, strlen($msg));
        socket_close($client_socket);
    } else {
        echo "socket_accept() 失败: " . socket_strerror(socket_last_error($socket)) . "\n";
        break;
    }
} while (true);

// 关闭 socket
socket_close($socket);

