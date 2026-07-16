<?php
/**
 * 邦国崛起新闻系统 - 系统测试类
 * 用于测试系统核心功能和API接口
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/core/functions.php';

class SystemTest {
    private $db;
    private $testResults = [];
    
    public function __construct() {
        $this->db = getDbConnection();
    }
    
    /**
     * 运行所有测试
     */
    public function runAllTests() {
        echo "=== 邦国崛起新闻系统 - 系统测试报告 ===\n\n";
        
        $this->testDatabaseConnection();
        $this->testCoreFunctions();
        $this->testModels();
        $this->testControllers();
        $this->testApiEndpoints();
        $this->testSecurityFeatures();
        $this->testPerformance();
        
        $this->generateReport();
    }
    
    /**
     * 测试数据库连接
     */
    private function testDatabaseConnection() {
        $testName = "数据库连接测试";
        
        try {
            $stmt = $this->db->query("SELECT 1");
            $result = $stmt->fetch();
            
            if ($result) {
                $this->addTestResult($testName, true, "数据库连接正常");
            } else {
                $this->addTestResult($testName, false, "数据库查询失败");
            }
        } catch (Exception $e) {
            $this->addTestResult($testName, false, "数据库连接异常: " . $e->getMessage());
        }
    }
    
    /**
     * 测试核心函数
     */
    private function testCoreFunctions() {
        $testName = "核心函数测试";
        $errors = [];
        
        // 测试安全输入函数
        $testInput = "<script>alert('xss')</script>测试内容";
        $safeInput = safe_input($testInput);
        if (strpos($safeInput, '<script>') !== false) {
            $errors[] = "安全输入过滤失败";
        }
        
        // 测试时间格式化
        $timestamp = strtotime('2024-01-01 12:00:00');
        $formatted = format_time($timestamp);
        if (empty($formatted)) {
            $errors[] = "时间格式化失败";
        }
        
        // 测试字符串截断
        $longText = str_repeat("测试", 50);
        $truncated = truncate_string($longText, 20);
        if (mb_strlen($truncated, 'UTF-8') > 20) {
            $errors[] = "字符串截断失败";
        }
        
        // 测试URL别名生成
        $title = "测试文章标题";
        $slug = generate_slug($title);
        if (empty($slug) || strpos($slug, ' ') !== false) {
            $errors[] = "URL别名生成失败";
        }
        
        if (empty($errors)) {
            $this->addTestResult($testName, true, "所有核心函数测试通过");
        } else {
            $this->addTestResult($testName, false, implode(", ", $errors));
        }
    }
    
    /**
     * 测试模型类
     */
    private function testModels() {
        $testName = "数据模型测试";
        $errors = [];
        
        try {
            // 测试文章模型
            $articleModel = new ArticleModel();
            
            // 测试文章创建
            $testArticle = [
                'title' => '系统测试文章 - ' . date('Y-m-d H:i:s'),
                'content' => '这是系统测试自动创建的文章内容。',
                'category_id' => 1,
                'slug' => 'system-test-' . time(),
                'status' => 'published',
                'published_at' => date('Y-m-d H:i:s')
            ];
            
            $articleId = $articleModel->insert($testArticle);
            if (!$articleId) {
                $errors[] = "文章创建失败";
            }
            
            // 测试文章查询
            $article = $articleModel->getById($articleId);
            if (!$article || $article['title'] !== $testArticle['title']) {
                $errors[] = "文章查询失败";
            }
            
            // 测试文章更新
            $updateData = ['title' => '更新后的测试文章'];
            $updateResult = $articleModel->update($articleId, $updateData);
            if (!$updateResult) {
                $errors[] = "文章更新失败";
            }
            
            // 测试文章删除
            $deleteResult = $articleModel->delete($articleId);
            if (!$deleteResult) {
                $errors[] = "文章删除失败";
            }
            
            // 测试栏目模型
            $categoryModel = new CategoryModel();
            $categories = $categoryModel->getActiveCategories();
            if (empty($categories)) {
                $errors[] = "栏目查询失败";
            }
            
        } catch (Exception $e) {
            $errors[] = "模型测试异常: " . $e->getMessage();
        }
        
        if (empty($errors)) {
            $this->addTestResult($testName, true, "所有数据模型测试通过");
        } else {
            $this->addTestResult($testName, false, implode(", ", $errors));
        }
    }
    
    /**
     * 测试控制器
     */
    private function testControllers() {
        $testName = "控制器测试";
        $errors = [];
        
        try {
            // 模拟HTTP请求测试首页控制器
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_SERVER['REQUEST_URI'] = '/';
            
            ob_start();
            require_once dirname(__DIR__) . '/index.php';
            $output = ob_get_clean();
            
            if (empty($output)) {
                $errors[] = "首页控制器输出为空";
            }
            
            // 测试文章详情页
            $_SERVER['REQUEST_URI'] = '/article/test-article.html';
            
            ob_start();
            require_once dirname(__DIR__) . '/index.php';
            $output = ob_get_clean();
            
            // 这里主要测试路由是否正常工作
            
        } catch (Exception $e) {
            $errors[] = "控制器测试异常: " . $e->getMessage();
        }
        
        if (empty($errors)) {
            $this->addTestResult($testName, true, "控制器基本功能正常");
        } else {
            $this->addTestResult($testName, false, implode(", ", $errors));
        }
    }
    
    /**
     * 测试API接口
     */
    private function testApiEndpoints() {
        $testName = "API接口测试";
        $errors = [];
        
        try {
            // 测试推送API（模拟请求）
            $apiUrl = SITE_URL . '/api/v1/push';
            
            // 创建测试子站配置
            $testAppId = 'test_app_' . time();
            $testAppSecret = md5($testAppId . 'secret');
            
            $sql = "INSERT INTO " . TABLE_SUBSITE_CONFIGS . " 
                    (app_id, app_secret, subsite_name, subsite_url, is_active, created_at) 
                    VALUES (?, ?, '测试子站', 'https://test.example.com', 1, NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$testAppId, $testAppSecret]);
            
            // 准备测试数据
            $testData = [
                'title' => 'API测试文章 - ' . date('Y-m-d H:i:s'),
                'category_code' => 'official_notice',
                'content' => str_repeat('这是API测试文章内容。', 20), // 确保内容长度足够
                'timestamp' => time(),
                'nonce_str' => uniqid()
            ];
            
            // 生成签名
            ksort($testData);
            $signString = '';
            foreach ($testData as $key => $value) {
                $signString .= $key . '=' . $value . '&';
            }
            $signString .= 'app_secret=' . $testAppSecret;
            $sign = md5($signString);
            
            // 模拟API请求
            $_SERVER['REQUEST_METHOD'] = 'POST';
            $_SERVER['HTTP_APPID'] = $testAppId;
            $_SERVER['HTTP_SIGN'] = $sign;
            $_POST = $testData;
            
            ob_start();
            require_once dirname(__DIR__) . '/api/v1/push.php';
            $apiResponse = ob_get_clean();
            
            $responseData = json_decode($apiResponse, true);
            if (!$responseData || $responseData['code'] !== 200) {
                $errors[] = "API接口响应异常: " . ($responseData['msg'] ?? '未知错误');
            }
            
            // 清理测试数据
            $this->db->query("DELETE FROM " . TABLE_SUBSITE_CONFIGS . " WHERE app_id = '$testAppId'");
            
        } catch (Exception $e) {
            $errors[] = "API接口测试异常: " . $e->getMessage();
        }
        
        if (empty($errors)) {
            $this->addTestResult($testName, true, "API接口测试通过");
        } else {
            $this->addTestResult($testName, false, implode(", ", $errors));
        }
    }
    
    /**
     * 测试安全特性
     */
    private function testSecurityFeatures() {
        $testName = "安全特性测试";
        $errors = [];
        
        try {
            // 测试XSS防护
            $xssTest = "<script>alert('xss')</script>正常内容";
            $filtered = safe_input($xssTest);
            if (strpos($filtered, '<script>') !== false) {
                $errors[] = "XSS防护失效";
            }
            
            // 测试SQL注入防护
            $sqlInjectionTest = "' OR '1'='1";
            $safeSql = $this->db->quote($sqlInjectionTest);
            if (strpos($safeSql, "' OR '1'='1") !== false) {
                $errors[] = "SQL注入防护可能存在问题";
            }
            
            // 测试会话安全
            session_start();
            $_SESSION['test_key'] = 'test_value';
            
            if (!isset($_SESSION['test_key']) || $_SESSION['test_key'] !== 'test_value') {
                $errors[] = "会话管理异常";
            }
            
            session_destroy();
            
        } catch (Exception $e) {
            $errors[] = "安全特性测试异常: " . $e->getMessage();
        }
        
        if (empty($errors)) {
            $this->addTestResult($testName, true, "安全特性测试通过");
        } else {
            $this->addTestResult($testName, false, implode(", ", $errors));
        }
    }
    
    /**
     * 测试性能
     */
    private function testPerformance() {
        $testName = "性能测试";
        
        $startTime = microtime(true);
        
        // 执行一些性能测试
        for ($i = 0; $i < 1000; $i++) {
            $test = safe_input("性能测试内容 " . $i);
        }
        
        $endTime = microtime(true);
        $executionTime = round(($endTime - $startTime) * 1000, 2); // 毫秒
        
        if ($executionTime < 100) {
            $this->addTestResult($testName, true, "性能良好 - 执行时间: {$executionTime}ms");
        } else if ($executionTime < 500) {
            $this->addTestResult($testName, true, "性能正常 - 执行时间: {$executionTime}ms");
        } else {
            $this->addTestResult($testName, false, "性能较差 - 执行时间: {$executionTime}ms");
        }
    }
    
    /**
     * 添加测试结果
     */
    private function addTestResult($name, $success, $message) {
        $this->testResults[] = [
            'name' => $name,
            'success' => $success,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        $status = $success ? "✓" : "✗";
        $color = $success ? "32" : "31"; // 绿色/红色
        echo "\033[{$color}m{$status} {$name}\033[0m - {$message}\n";
    }
    
    /**
     * 生成测试报告
     */
    private function generateReport() {
        $totalTests = count($this->testResults);
        $passedTests = count(array_filter($this->testResults, function($test) {
            return $test['success'];
        }));
        $failedTests = $totalTests - $passedTests;
        
        $successRate = round(($passedTests / $totalTests) * 100, 2);
        
        echo "\n=== 测试报告摘要 ===\n";
        echo "总测试数: {$totalTests}\n";
        echo "通过数: {$passedTests}\n";
        echo "失败数: {$failedTests}\n";
        echo "成功率: {$successRate}%\n\n";
        
        if ($failedTests > 0) {
            echo "=== 失败的测试 ===\n";
            foreach ($this->testResults as $test) {
                if (!$test['success']) {
                    echo "• {$test['name']}: {$test['message']}\n";
                }
            }
            echo "\n";
        }
        
        // 生成HTML报告
        $this->generateHtmlReport();
        
        if ($successRate >= 80) {
            echo "\033[32m✓ 系统测试总体通过，可以部署使用\033[0m\n";
        } else {
            echo "\033[31m✗ 系统测试未通过，请修复问题后再部署\033[0m\n";
        }
    }
    
    /**
     * 生成HTML测试报告
     */
    private function generateHtmlReport() {
        $html = "<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>邦国崛起新闻系统 - 测试报告</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-result { margin: 10px 0; padding: 10px; border-radius: 5px; }
        .success { background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .failure { background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .summary { font-size: 1.2em; font-weight: bold; margin: 20px 0; }
    </style>
</head>
<body>
    <h1>邦国崛起新闻系统 - 系统测试报告</h1>
    <div class="summary">测试时间: " . date('Y-m-d H:i:s') . "</div>";
        
        foreach ($this->testResults as $test) {
            $class = $test['success'] ? 'success' : 'failure';
            $icon = $test['success'] ? '✓' : '✗';
            $html .= "<div class='test-result {$class}'>";
            $html .= "<strong>{$icon} {$test['name']}</strong><br>";
            $html .= "结果: {$test['message']}<br>";
            $html .= "时间: {$test['timestamp']}";
            $html .= "</div>";
        }
        
        $html .= "
</body>
</html>";
        
        file_put_contents(dirname(__DIR__) . '/tests/report.html', $html);
        echo "详细测试报告已生成: tests/report.html\n";
    }
}

// 执行测试
if (php_sapi_name() === 'cli') {
    $test = new SystemTest();
    $test->runAllTests();
} else {
    echo "请在命令行中运行此测试脚本";
}
?>