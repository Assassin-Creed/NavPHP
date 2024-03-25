<?php

class Database
{

    private $connection;

    public function __construct($config) {
        // 创建数据库连接
        $this->connection = new PDO(
            "mysql:host={$config['host']};dbname={$config['dbname']};port={$config['port']}",
            $config['user'],
            $config['password']
        );

        // 设置错误处理模式
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // 开始事务
    public function beginTransaction() {
        $this->connection->beginTransaction();
    }

    // 提交事务
    public function commit() {
        $this->connection->commit();
    }

    // 回滚事务
    public function rollback() {
        $this->connection->rollback();
    }

    public function query($sql, $params = []) {
        // 使用预处理语句防止SQL注入
        $stmt = $this->connection->prepare($sql);

        // 执行查询
        $stmt->execute($params);

        // 返回结果集
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function close() {
        // 关闭数据库连接
        $this->connection = null;
    }
}
