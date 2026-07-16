<?php
/**
 * 数据库配置文件
 * 敏感信息从 .env 文件读取
 */

// 数据库配置 — 从环境变量读取，带默认值
define('DB_HOST', EnvLoader::get('DB_HOST', 'localhost'));
define('DB_PORT', EnvLoader::get('DB_PORT', '3306'));
define('DB_NAME', EnvLoader::get('DB_NAME', 'bgjq'));
define('DB_USER', EnvLoader::get('DB_USER', 'bgjq'));
define('DB_PASS', EnvLoader::get('DB_PASS', ''));
define('DB_CHARSET', EnvLoader::get('DB_CHARSET', 'utf8mb4'));
define('DB_PREFIX', '');

// 数据库连接选项
$db_options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
];

// 直接在DSN中设置字符集，避免使用弃用的常量
$dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

// 创建数据库连接
function getDbConnection() {
    global $dsn, $db_options;
    
    // 检查 .env 文件是否存在
    $envFile = dirname(__DIR__) . '/.env';
    if (!file_exists($envFile)) {
        $msg = '数据库连接失败：缺少 .env 配置文件。请在服务器项目根目录创建 .env 文件，参考 .env.example 模板填写正确的数据库凭据。';
        error_log($msg);
        throw new \RuntimeException($msg);
    }
    
    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $db_options);
        return $pdo;
    } catch (PDOException $e) {
        $msg = "数据库连接失败: " . $e->getMessage() . " [host=" . DB_HOST . ", db=" . DB_NAME . ", user=" . DB_USER . "]";
        error_log($msg);
        throw new \RuntimeException($msg);
    }
}

// 数据库表名常量
define('TABLE_NEWS', 'news_articles');
define('TABLE_CATEGORIES', 'news_categories');
define('TABLE_TAGS', 'news_tags');
define('TABLE_ARTICLE_TAGS', 'news_article_tags');
define('TABLE_ARTICLE_LIKES', 'news_likes');
define('TABLE_ADMIN_USERS', 'news_admin_users');
define('TABLE_SUBSITE_CONFIGS', 'news_subsite_configs');
define('TABLE_PUSH_LOGS', 'news_push_logs');
define('TABLE_INDEXNOW_LOGS', 'news_indexnow_logs');
define('TABLE_OPERATION_LOGS', 'news_operation_logs');
define('TABLE_SEARCH_RECORDS', 'search_records');
define('TABLE_USERS', 'users');
define('TABLE_COMMENTS', 'news_comments');
define('TABLE_CAROUSELS', 'news_carousels');
define('TABLE_PERMISSIONS', 'news_permissions');
?>