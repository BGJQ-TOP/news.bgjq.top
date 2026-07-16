<?php
/**
 * 简单的 .env 文件加载器
 * 从项目根目录的 .env 文件加载环境变量
 */
class EnvLoader {
    /**
     * 加载 .env 文件并将变量设置为环境变量和常量
     */
    public static function load($rootPath = null) {
        if ($rootPath === null) {
            $rootPath = dirname(__DIR__);
        }
        
        $envFile = $rootPath . '/.env';
        
        if (!file_exists($envFile)) {
            return;
        }
        
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // 跳过注释行
            if ($line === '' || strpos($line, '#') === 0) {
                continue;
            }
            
            // 解析键值对
            $parts = explode('=', $line, 2);
            if (count($parts) !== 2) {
                continue;
            }
            
            $key = trim($parts[0]);
            $value = trim($parts[1]);
            
            // 去除引号
            if (
                (strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) ||
                (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1)
            ) {
                $value = substr($value, 1, -1);
            }
            
            // 设置环境变量
            putenv("{$key}={$value}");
            $_ENV[$key] = $value;
        }
    }
    
    /**
     * 获取环境变量，优先从 .env 读取，其次从系统环境变量
     */
    public static function get($key, $default = null) {
        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }
        return $default;
    }
}