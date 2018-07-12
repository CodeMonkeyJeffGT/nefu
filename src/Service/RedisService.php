<?php
namespace App\Service;


class RedisService
{
    private $redis;
    public function __construct()
    {
        $this->redis = new \Redis();
        $this->redis->connect('127.0.0.1', 6379);
    }

    public function getRedis()
    {
        return $this->redis;
    }
    
    public function set($name, $value, $expire = false)
    {
        $this->redis->set($name, $value);
        if (false !== $expire) {
            $this->redis->expire($name, $expire);
        }
    }

    public function get($name, $default = null)
    {
        if ($this->redis->exists($name)) {
            return $this->redis->get($name);
        } else {
            return $default;
        }
    }

    public function getOrNew($name, $valueFun, $expire = false)
    {
        $value = $this->get($name);
        if (is_null($value)) {
            $value = $valueFun();
            $this->set($name, $value, $expire);
        }
        return $value;
    }

    public function push($name, $value)
    {
        if (is_array($value)) {
            foreach ($value as $val) {
                $this->push($name, $val);
            }
        }
        $this->redis->rpush($name, $value);
    }

    public function size($name)
    {
        return $this->redis->lsize($name);
    }

    public function pop($name, $default = null)
    {
        $value = $this->redis->lpop($name);
        if (false === $value) {
            return $default;
        }
        return $value;
    }

    public function subscribe($name, $function)
    {
        $this->redis->subscribe($name, $function);
    }

    public function publish($name, $data)
    {
        $this->redis->publish($name, $data);
    }
}