<?php
/**
 * 新闻系统主入口文件
 */

// 开启错误报告（生产环境可以关闭）
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 加载配置文件
require_once 'config/config.php';
require_once 'core/functions.php';

// 启动会话（使用 @ 抑制警告）
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// 路由处理
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);
$path = trim($path, '/');
$path = rtrim($path, '/');

// 默认路由
if (empty($path) || $path === 'index.php') {
    $path = 'home';
}

// 路由映射
$routes = [
    'home' => 'HomeController@index',
    'article' => 'ArticleController@detail',
    'a' => 'ArticleController@detail',
    'category' => 'CategoryController@list',
    'search' => 'SearchController@index',
    'contribute' => 'ContributionController@index',
    'rankings' => 'RankingController@index',
    'profile' => 'ProfileController@index',
    'sitemap.xml' => 'SitemapController@index',
    'indexnow.txt' => 'IndexNowController@verify',
    'login' => 'UserController@login',
    'logout' => 'UserController@logout',
    'api/login' => 'UserController@doLogin',
    'api/check-login' => 'UserController@checkLogin',
    'api/is-diplomat' => 'UserController@isDiplomat',
    'api/can-publish-diplomat' => 'UserController@canPublishDiplomatAnnouncement',
    'diplomat' => 'ContributionController@diplomat',
    'api/diplomat-publish' => 'ContributionController@publishDiplomatAnnouncement',
    'api/contribute' => 'ContributionController@submit',
];

// 解析路由
$controller = 'HomeController';
$action = 'index';
$params = [];

if (strpos($path, 'admin/') === 0) {
    // 后台路由
    $adminPath = substr($path, 6); // 移除 'admin/'
    $adminRoutes = [
        'login' => 'AdminAuthController@login',
        'dashboard' => 'AdminController@dashboard',
        'articles' => 'AdminArticleController@index',
        'categories' => 'AdminCategoryController@index',
        'contributions' => 'AdminContributionController@index',
        'users' => 'AdminUserController@index',
        'push' => 'AdminPushController@index',
        'statistics' => 'AdminStatisticsController@index',
        'settings' => 'AdminSettingsController@index',
        'preview' => 'AdminPreviewController@preview'
    ];
    
    // 支持子路由：admin/contributions/approve -> AdminContributionController@approve
    $adminSubRoutes = [
        'articles/edit' => 'AdminArticleController@edit',
        'articles/delete' => 'AdminArticleController@delete',
        'categories/create' => 'AdminCategoryController@create',
        'categories/edit' => 'AdminCategoryController@edit',
        'categories/delete' => 'AdminCategoryController@delete',
        'contributions/approve' => 'AdminContributionController@approve',
        'contributions/reject' => 'AdminContributionController@reject',
        'contributions/delete' => 'AdminContributionController@delete',
        'users/edit' => 'AdminUserController@edit',
        'users/delete' => 'AdminUserController@delete',
        'push/create' => 'AdminPushController@create',
        'push/delete' => 'AdminPushController@delete',
        'settings/update' => 'AdminSettingsController@update'
    ];
    
    if (empty($adminPath)) {
        $adminPath = 'dashboard';
    }
    
    if (isset($adminRoutes[$adminPath])) {
        list($controller, $action) = explode('@', $adminRoutes[$adminPath]);
    } elseif (isset($adminSubRoutes[$adminPath])) {
        list($controller, $action) = explode('@', $adminSubRoutes[$adminPath]);
    }
} else {
    // 前台路由 - 支持多级路径匹配
    // 先尝试精确匹配完整路径
    if (isset($routes[$path])) {
        list($controller, $action) = explode('@', $routes[$path]);
    } else {
        // 精确匹配失败，尝试前缀匹配
        $pathParts = explode('/', $path);
        $routeKey = $pathParts[0];
        
        if (isset($routes[$routeKey])) {
            list($controller, $action) = explode('@', $routes[$routeKey]);
            // 提取参数 (移除第一个元素 routeKey)
            $params = array_slice($pathParts, 1);
            // 过滤空值 (因为 URL 末尾的 / 会产生空字符串)
            $params = array_filter($params, function($v) { return $v !== ''; });
            // 重新索引数组
            $params = array_values($params);
        }
    }
}

// 加载控制器
$controllerFile = CONTROLLER_PATH . '/' . $controller . '.php';
if (file_exists($controllerFile)) {
    require_once $controllerFile;
    
    if (class_exists($controller)) {
        $controllerInstance = new $controller();
        
        if (method_exists($controllerInstance, $action)) {
            // 调用控制器方法，传递参数
            call_user_func_array([$controllerInstance, $action], $params);
        } else {
            // 方法不存在，显示 404
            show_404();
        }
    } else {
        // 控制器不存在，显示 404
        show_404();
    }
} else {
    // 控制器文件不存在，显示 404
    show_404();
}

/**
 * 显示404页面
 */
function show_404() {
    http_response_code(404);
    
    // 可以加载一个专门的404模板
    echo '<!DOCTYPE html>
    <html lang="zh-CN">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>页面未找到 - ' . SITE_NAME . '</title>
        <style>
            body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
            h1 { color: #333; }
            p { color: #666; }
            a { color: #007bff; text-decoration: none; }
        </style>
    </head>
    <body>
        <h1>404 - 页面未找到</h1>
        <p>抱歉，您访问的页面不存在。</p>
        <p><a href="/">返回首页</a></p>
    </body>
    </html>';
    exit;
}

?>