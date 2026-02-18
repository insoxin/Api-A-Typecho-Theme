<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * 简单的文件缓存系统
 * 用于缓存数据库查询结果和API响应
 */
class API_Cache {
    /**
     * 缓存目录
     */
    private static $cache_dir = '/usr/uploads/cache/';
    
    /**
     * 默认缓存时间（秒）
     */
    private static $default_expire = 3600; // 1小时
    
    /**
     * 初始化缓存目录
     */
    public static function init() {
        $cache_path = __TYPECHO_ROOT_DIR__ . self::$cache_dir;
        if (!is_dir($cache_path)) {
            @mkdir($cache_path, 0755, true);
        }
    }
    
    /**
     * 获取缓存
     * @param string $key 缓存键名
     * @return mixed 缓存数据，不存在或过期返回 false
     */
    public static function get($key) {
        self::init();
        $cache_file = self::getCacheFile($key);
        
        if (!file_exists($cache_file)) {
            return false;
        }
        
        $data = @file_get_contents($cache_file);
        if ($data === false) {
            return false;
        }
        
        $cache = @unserialize($data);
        if ($cache === false) {
            return false;
        }
        
        // 检查是否过期
        if (isset($cache['expire']) && $cache['expire'] > 0 && $cache['expire'] < time()) {
            self::delete($key);
            return false;
        }
        
        return isset($cache['data']) ? $cache['data'] : false;
    }
    
    /**
     * 设置缓存
     * @param string $key 缓存键名
     * @param mixed $data 要缓存的数据
     * @param int $expire 过期时间（秒），0 表示永不过期
     * @return bool 是否成功
     */
    public static function set($key, $data, $expire = null) {
        self::init();
        
        if ($expire === null) {
            $expire = self::$default_expire;
        }
        
        $cache = array(
            'data' => $data,
            'expire' => $expire > 0 ? time() + $expire : 0,
            'created' => time()
        );
        
        $cache_file = self::getCacheFile($key);
        $result = @file_put_contents($cache_file, serialize($cache), LOCK_EX);
        
        return $result !== false;
    }
    
    /**
     * 删除缓存
     * @param string $key 缓存键名
     * @return bool 是否成功
     */
    public static function delete($key) {
        $cache_file = self::getCacheFile($key);
        if (file_exists($cache_file)) {
            return @unlink($cache_file);
        }
        return true;
    }
    
    /**
     * 清空所有缓存
     * @return bool 是否成功
     */
    public static function clear() {
        self::init();
        $cache_path = __TYPECHO_ROOT_DIR__ . self::$cache_dir;
        
        if (!is_dir($cache_path)) {
            return true;
        }
        
        $files = glob($cache_path . '*.cache');
        if ($files === false) {
            return false;
        }
        
        foreach ($files as $file) {
            @unlink($file);
        }
        
        return true;
    }
    
    /**
     * 获取缓存文件路径
     * @param string $key 缓存键名
     * @return string 缓存文件完整路径
     */
    private static function getCacheFile($key) {
        $hash = md5($key);
        return __TYPECHO_ROOT_DIR__ . self::$cache_dir . $hash . '.cache';
    }
    
    /**
     * 检查缓存是否存在且未过期
     * @param string $key 缓存键名
     * @return bool
     */
    public static function has($key) {
        return self::get($key) !== false;
    }
}
