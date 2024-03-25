<?php

class RedisCacheManager {
    private static $instance;
    private $redis;

    public function __construct($config) {
        $this->redis = new \Redis();
        // 连接 Redis
        $this->redis->connect($config['host'], $config['port']);
        // 如果有密码，进行认证
        if (!empty($config['password'])) {
            $this->redis->auth($config['password']);
        }
    }

    public static function getInstance($config) {
        if (!isset(self::$instance)) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    public function getRedis() {
        return $this->redis;
    }

    public function set($key, $value, $expiration = null) {
        $serializedValue = serialize($value);
        if ($expiration !== null) {
            $this->redis->setex($key, $expiration, $serializedValue);
        } else {
            $this->redis->set($key, $serializedValue);
        }
    }

    public function get($key) {
        $serializedValue = $this->redis->get($key);
        if ($serializedValue !== false) {
            return unserialize($serializedValue);
        }
        return null;
    }

    public function delete($key) {
        $this->redis->del($key);
    }

    // 其他可能用到的方法，比如清空所有缓存
    public function flushAll() {
        $this->redis->flushAll();
    }
}

?>
