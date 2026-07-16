<?php
/**
 * 系统核心配置文件
 * 敏感信息从 .env 文件读取
 */

// 加载 .env 环境变量
require_once __DIR__ . '/../core/EnvLoader.php';
EnvLoader::load(dirname(__DIR__));

// 基础配置
define('SITE_NAME', EnvLoader::get('SITE_NAME', '邦国新闻'));
define('SITE_URL', EnvLoader::get('SITE_URL', 'https://news.bgjq.top'));
define('SITE_DESCRIPTION', '简洁、专注的新闻阅读平台，提供最新资讯和热门文章');
define('SITE_KEYWORDS', '新闻，资讯，阅读，简洁，平台');

// 路径配置
define('ROOT_PATH', dirname(__DIR__));
define('CORE_PATH', ROOT_PATH . '/core');
define('MODEL_PATH', ROOT_PATH . '/models');
define('CONTROLLER_PATH', ROOT_PATH . '/controllers');
define('VIEW_PATH', ROOT_PATH . '/views');
define('ASSET_PATH', ROOT_PATH . '/assets');
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('LOG_PATH', ROOT_PATH . '/logs');

// URL 配置
define('ASSET_URL', SITE_URL . '/assets');
define('UPLOAD_URL', SITE_URL . '/uploads');

// 系统配置
define('DEBUG_MODE', EnvLoader::get('DEBUG_MODE', 'false') === 'true');
define('TIMEZONE', 'Asia/Shanghai');
define('CHARSET', 'UTF-8');
define('LANGUAGE', 'zh-CN');

// 分页配置
define('PAGE_SIZE', 20);
define('PAGE_RANGE', 5);

// 上传配置
define('UPLOAD_MAX_SIZE', 10 * 1024 * 1024);
define('UPLOAD_ALLOW_TYPES', 'jpg,jpeg,png,gif,bmp,webp');
define('UPLOAD_IMAGE_MAX_WIDTH', 1920);
define('UPLOAD_IMAGE_MAX_HEIGHT', 1080);

// SEO 配置
define('SEO_TITLE_SUFFIX', ' - 邦国新闻');
define('META_DESCRIPTION_LENGTH', 160);
define('META_KEYWORDS_LENGTH', 300);

// IndexNow 配置 — 从环境变量读取
define('INDEXNOW_ENABLED', EnvLoader::get('INDEXNOW_ENABLED', 'true') === 'true');
define('INDEXNOW_API_KEY', EnvLoader::get('INDEXNOW_API_KEY', ''));
define('INDEXNOW_BING_URL', 'https://www.bing.com/indexnow');
define('INDEXNOW_YANDEX_URL', 'https://yandex.com/indexnow');

// 自动加载器
spl_autoload_register(function ($class) {
    $class = str_replace('\\', '/', $class);
    
    if (file_exists(MODEL_PATH . '/' . $class . '.php')) {
        require_once MODEL_PATH . '/' . $class . '.php';
        return;
    }
    
    if (file_exists(CONTROLLER_PATH . '/' . $class . '.php')) {
        require_once CONTROLLER_PATH . '/' . $class . '.php';
        return;
    }
    
    if (file_exists(CORE_PATH . '/' . $class . '.php')) {
        require_once CORE_PATH . '/' . $class . '.php';
        return;
    }
});

// 缓存配置
define('CACHE_ENABLED', true);
define('CACHE_TTL', 3600);

// 安全配置 — 从环境变量读取
define('SESSION_TIMEOUT', intval(EnvLoader::get('SESSION_TIMEOUT', '7200')));
define('CSRF_TOKEN_NAME', EnvLoader::get('CSRF_TOKEN_NAME', 'csrf_token'));
define('PASSWORD_SALT', EnvLoader::get('PASSWORD_SALT', ''));

// API 配置
define('API_RATE_LIMIT', 60);
define('API_DAILY_LIMIT', 1000);

// 邮件配置 — 从环境变量读取
define('SMTP_HOST', EnvLoader::get('SMTP_HOST', 'smtp.example.com'));
define('SMTP_PORT', EnvLoader::get('SMTP_PORT', '587'));
define('SMTP_USER', EnvLoader::get('SMTP_USER', ''));
define('SMTP_PASS', EnvLoader::get('SMTP_PASS', ''));
define('SMTP_SECURE', EnvLoader::get('SMTP_SECURE', 'tls'));

// 初始化设置
date_default_timezone_set(TIMEZONE);
mb_internal_encoding(CHARSET);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// 自动加载类
spl_autoload_register(function ($className) {
    $classFile = ROOT_PATH . '/' . str_replace('\\', '/', $className) . '.php';
    if (file_exists($classFile)) {
        require_once $classFile;
    }
});

// 加载数据库配置
require_once ROOT_PATH . '/config/database.php';
?>