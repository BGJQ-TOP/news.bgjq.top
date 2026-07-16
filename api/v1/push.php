<?php
/**
 * 子站内容推送API接口
 * 接口规范：RESTful API，JSON格式，API密钥签名鉴权
 */

// 设置响应头
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, AppID, Sign');

// 处理预检请求
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 加载配置文件
require_once dirname(__DIR__, 3) . '/config/config.php';
require_once dirname(__DIR__, 3) . '/core/functions.php';

// API响应函数
function apiResponse($code, $message, $data = null) {
    http_response_code($code);
    echo json_encode([
        'code' => $code,
        'msg' => $message,
        'data' => $data
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 获取请求头
$appId = $_SERVER['HTTP_APPID'] ?? '';
$sign = $_SERVER['HTTP_SIGN'] ?? '';

// 验证必要参数
if (empty($appId) || empty($sign)) {
    apiResponse(400, '参数缺失：AppID和Sign为必填项');
}

// 验证请求方法
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    apiResponse(405, '请求方法不允许，仅支持POST方法');
}

// 获取请求体
$input = file_get_contents('php://input');
$requestData = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    apiResponse(400, 'JSON格式错误：' . json_last_error_msg());
}

// 创建推送服务实例
$pushService = new PushService();

// 处理推送请求
try {
    $result = $pushService->handlePushRequest($appId, $sign, $requestData);
    
    if ($result['success']) {
        apiResponse(200, '推送成功', $result['data']);
    } else {
        apiResponse($result['error_code'], $result['message']);
    }
    
} catch (Exception $e) {
    apiResponse(500, '服务器内部错误：' . $e->getMessage());
}

/**
 * 推送服务类
 */
class PushService {
    private $db;
    
    public function __construct() {
        $this->db = getDbConnection();
    }
    
    /**
     * 处理推送请求
     */
    public function handlePushRequest($appId, $sign, $requestData) {
        // 验证签名
        if (!$this->verifySignature($appId, $sign, $requestData)) {
            return [
                'success' => false,
                'error_code' => 401,
                'message' => '签名校验失败'
            ];
        }
        
        // 验证时效性
        if (!$this->verifyTimestamp($requestData['timestamp'] ?? 0)) {
            return [
                'success' => false,
                'error_code' => 403,
                'message' => '请求超时'
            ];
        }
        
        // 获取子站配置
        $subsiteConfig = $this->getSubsiteConfig($appId);
        if (!$subsiteConfig) {
            return [
                'success' => false,
                'error_code' => 401,
                'message' => 'AppID无效或子站已被禁用'
            ];
        }
        
        // 验证请求频率
        if (!$this->checkRateLimit($appId)) {
            return [
                'success' => false,
                'error_code' => 403,
                'message' => '请求频率超限'
            ];
        }
        
        // 验证请求数据
        $validationResult = $this->validatePushData($requestData);
        if (!$validationResult['valid']) {
            return [
                'success' => false,
                'error_code' => 400,
                'message' => $validationResult['message']
            ];
        }
        
        // 处理推送内容
        return $this->processPushContent($subsiteConfig, $requestData);
    }
    
    /**
     * 验证签名
     */
    private function verifySignature($appId, $sign, $data) {
        // 获取子站密钥
        $subsiteConfig = $this->getSubsiteConfig($appId);
        if (!$subsiteConfig) {
            return false;
        }
        
        $appSecret = $subsiteConfig['app_secret'];
        
        // 生成签名
        $params = $data;
        unset($params['sign']);
        
        ksort($params);
        $signString = '';
        foreach ($params as $key => $value) {
            $signString .= $key . '=' . $value . '&';
        }
        $signString .= 'app_secret=' . $appSecret;
        
        $expectedSign = md5($signString);
        
        return hash_equals($expectedSign, $sign);
    }
    
    /**
     * 验证时效性
     */
    private function verifyTimestamp($timestamp) {
        $currentTime = time();
        $timeDiff = abs($currentTime - $timestamp);
        
        // 允许5分钟的时间差
        return $timeDiff <= 300;
    }
    
    /**
     * 获取子站配置
     */
    private function getSubsiteConfig($appId) {
        $sql = "SELECT * FROM " . TABLE_SUBSITE_CONFIGS . " 
                WHERE app_id = ? AND is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$appId]);
        return $stmt->fetch();
    }
    
    /**
     * 检查请求频率
     */
    private function checkRateLimit($appId) {
        $sql = "SELECT COUNT(*) as count FROM " . TABLE_PUSH_LOGS . " 
                WHERE subsite_app_id = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 MINUTE)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$appId]);
        $result = $stmt->fetch();
        
        $minuteLimit = 60; // 每分钟限制
        return $result['count'] < $minuteLimit;
    }
    
    /**
     * 验证推送数据
     */
    private function validatePushData($data) {
        $requiredFields = ['title', 'category_code', 'content', 'timestamp', 'nonce_str'];
        
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return [
                    'valid' => false,
                    'message' => '参数缺失：' . $field . '为必填项'
                ];
            }
        }
        
        // 验证标题长度
        if (mb_strlen($data['title'], 'UTF-8') > 200) {
            return [
                'valid' => false,
                'message' => '标题长度不能超过200个字符'
            ];
        }
        
        // 验证内容长度（SEO要求）
        if (mb_strlen(strip_tags($data['content']), 'UTF-8') < 200) {
            return [
                'valid' => false,
                'message' => '内容长度至少需要200个字符（SEO要求）'
            ];
        }
        
        // 验证栏目编码
        $validCategories = [
            'official_notice', 'un_dynamic', 'server_news', 
            'newbie_guide', 'country_story', 'player_contribution'
        ];
        
        if (!in_array($data['category_code'], $validCategories)) {
            return [
                'valid' => false,
                'message' => '无效的栏目编码'
            ];
        }
        
        return ['valid' => true, 'message' => '验证通过'];
    }
    
    /**
     * 处理推送内容
     */
    private function processPushContent($subsiteConfig, $data) {
        // 检查内容重复
        if ($this->isDuplicateContent($subsiteConfig['app_id'], $data['title'], $data['source_url'] ?? '')) {
            return [
                'success' => false,
                'error_code' => 409,
                'message' => '内容重复推送'
            ];
        }
        
        // 敏感词过滤
        $filteredContent = $this->filterSensitiveWords($data['content']);
        if ($filteredContent['has_sensitive']) {
            // 记录敏感内容
            $this->logSensitiveContent($subsiteConfig['app_id'], $data['title'], $filteredContent['sensitive_words']);
        }
        
        // 创建文章草稿
        $articleData = [
            'title' => $data['title'],
            'content' => $filteredContent['content'],
            'category_id' => $this->getCategoryIdByCode($data['category_code']),
            'slug' => generate_slug($data['title']),
            'cover_image' => $data['cover_image'] ?? null,
            'seo_title' => $data['seo_title'] ?? null,
            'seo_keywords' => $data['seo_keywords'] ?? null,
            'seo_description' => $data['seo_description'] ?? null,
            'author' => $data['author'] ?? $subsiteConfig['subsite_name'],
            'source_type' => 'subsite_push',
            'source_url' => $data['source_url'] ?? null,
            'status' => 'pending', // 等待审核
            'published_at' => date('Y-m-d H:i:s')
        ];
        
        // 插入文章
        $articleModel = new ArticleModel();
        $articleId = $articleModel->insert($articleData);
        
        if ($articleId) {
            // 记录推送日志
            $this->logPushSuccess($subsiteConfig['app_id'], $articleId, $data);
            
            return [
                'success' => true,
                'data' => [
                    'article_id' => $articleId,
                    'push_time' => date('Y-m-d H:i:s')
                ]
            ];
        } else {
            // 记录推送失败
            $this->logPushError($subsiteConfig['app_id'], '文章创建失败', $data);
            
            return [
                'success' => false,
                'error_code' => 500,
                'message' => '文章创建失败'
            ];
        }
    }
    
    /**
     * 检查内容重复
     */
    private function isDuplicateContent($appId, $title, $sourceUrl) {
        $sql = "SELECT COUNT(*) as count FROM " . TABLE_PUSH_LOGS . " 
                WHERE subsite_app_id = ? AND title = ? AND push_status = 'success'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$appId, $title]);
        $result = $stmt->fetch();
        
        if ($result['count'] > 0) {
            return true;
        }
        
        // 如果提供了源链接，也检查链接重复
        if (!empty($sourceUrl)) {
            $sql = "SELECT COUNT(*) as count FROM " . TABLE_NEWS . " 
                    WHERE source_url = ? AND source_type = 'subsite_push'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$sourceUrl]);
            $result = $stmt->fetch();
            
            return $result['count'] > 0;
        }
        
        return false;
    }
    
    /**
     * 敏感词过滤
     */
    private function filterSensitiveWords($content) {
        // 这里应该从数据库或配置文件中加载敏感词库
        $sensitiveWords = ['违规词1', '违规词2', '敏感词1'];
        
        $foundWords = [];
        $filteredContent = $content;
        
        foreach ($sensitiveWords as $word) {
            if (strpos($content, $word) !== false) {
                $foundWords[] = $word;
                $filteredContent = str_replace($word, '***', $filteredContent);
            }
        }
        
        return [
            'content' => $filteredContent,
            'has_sensitive' => !empty($foundWords),
            'sensitive_words' => $foundWords
        ];
    }
    
    /**
     * 根据栏目编码获取栏目ID
     */
    private function getCategoryIdByCode($categoryCode) {
        $sql = "SELECT id FROM " . TABLE_CATEGORIES . " WHERE code = ? AND is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$categoryCode]);
        $result = $stmt->fetch();
        
        return $result ? $result['id'] : 1; // 默认返回第一个栏目
    }
    
    /**
     * 记录推送成功
     */
    private function logPushSuccess($appId, $articleId, $data) {
        $sql = "INSERT INTO " . TABLE_PUSH_LOGS . " 
                (subsite_app_id, article_id, title, category_code, push_status, push_data, ip_address, created_at) 
                VALUES (?, ?, ?, ?, 'success', ?, ?, NOW())";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $appId, 
            $articleId, 
            $data['title'], 
            $data['category_code'],
            json_encode($data, JSON_UNESCAPED_UNICODE),
            $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
    }
    
    /**
     * 记录推送错误
     */
    private function logPushError($appId, $errorMessage, $data) {
        $sql = "INSERT INTO " . TABLE_PUSH_LOGS . " 
                (subsite_app_id, title, category_code, push_status, error_message, push_data, ip_address, created_at) 
                VALUES (?, ?, ?, 'failed', ?, ?, ?, NOW())";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $appId, 
            $data['title'] ?? '', 
            $data['category_code'] ?? '',
            $errorMessage,
            json_encode($data, JSON_UNESCAPED_UNICODE),
            $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
    }
    
    /**
     * 记录敏感内容
     */
    private function logSensitiveContent($appId, $title, $sensitiveWords) {
        // 这里可以记录到专门的敏感内容日志表
        error_log("敏感内容警告 - 子站: {$appId}, 标题: {$title}, 敏感词: " . implode(',', $sensitiveWords));
    }
}

// 加载文章模型（需要创建这个类）
class ArticleModel {
    // 简化的文章模型，实际应该使用完整的模型类
    private $db;
    
    public function __construct() {
        $this->db = getDbConnection();
    }
    
    public function insert($data) {
        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');
        
        $sql = "INSERT INTO news (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($data));
        
        return $this->db->lastInsertId();
    }
}
?>