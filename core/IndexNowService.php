<?php
/**
 * IndexNow自动提交服务
 */
class IndexNowService {
    private $db;
    
    public function __construct() {
        $this->db = getDbConnection();
    }
    
    /**
     * 提交URL到IndexNow
     */
    public function submitUrl($url, $submitType = 'article_publish') {
        if (!INDEXNOW_ENABLED) {
            return ['success' => false, 'message' => 'IndexNow功能未启用'];
        }
        
        // 验证URL格式
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return ['success' => false, 'message' => '无效的URL格式'];
        }
        
        // 检查是否已提交过（避免重复提交）
        if ($this->isUrlAlreadySubmitted($url)) {
            return ['success' => true, 'message' => 'URL已提交过，跳过重复提交'];
        }
        
        try {
            // 记录提交日志
            $logId = $this->logSubmission($url, $submitType, 'pending');
            
            // 向Bing提交
            $bingResult = $this->submitToBing($url);
            
            // 向Yandex提交
            $yandexResult = $this->submitToYandex($url);
            
            // 更新提交状态
            $status = $bingResult['success'] || $yandexResult['success'] ? 'success' : 'failed';
            $errorMessage = '';
            
            if (!$bingResult['success']) {
                $errorMessage .= 'Bing: ' . $bingResult['message'] . '; ';
            }
            if (!$yandexResult['success']) {
                $errorMessage .= 'Yandex: ' . $yandexResult['message'] . '; ';
            }
            
            $this->updateSubmissionLog($logId, $status, trim($errorMessage, '; '));
            
            return [
                'success' => $status === 'success',
                'message' => $status === 'success' ? 'IndexNow提交成功' : 'IndexNow提交失败',
                'details' => [
                    'bing' => $bingResult,
                    'yandex' => $yandexResult
                ]
            ];
            
        } catch (Exception $e) {
            $this->updateSubmissionLog($logId, 'failed', $e->getMessage());
            return ['success' => false, 'message' => 'IndexNow提交异常: ' . $e->getMessage()];
        }
    }
    
    /**
     * 向Bing提交URL
     */
    private function submitToBing($url) {
        $apiUrl = INDEXNOW_BING_URL;
        $data = [
            'host' => parse_url(SITE_URL, PHP_URL_HOST),
            'key' => INDEXNOW_API_KEY,
            'keyLocation' => SITE_URL . '/indexnow.txt',
            'urlList' => [$url]
        ];
        
        return $this->makeApiRequest($apiUrl, $data);
    }
    
    /**
     * 向Yandex提交URL
     */
    private function submitToYandex($url) {
        $apiUrl = INDEXNOW_YANDEX_URL;
        $data = [
            'host' => parse_url(SITE_URL, PHP_URL_HOST),
            'key' => INDEXNOW_API_KEY,
            'keyLocation' => SITE_URL . '/indexnow.txt',
            'urlList' => [$url]
        ];
        
        return $this->makeApiRequest($apiUrl, $data);
    }
    
    /**
     * 发送API请求
     */
    private function makeApiRequest($apiUrl, $data) {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json; charset=utf-8',
                'User-Agent: ' . SITE_NAME . ' IndexNow Client/1.0'
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        if ($error) {
            return ['success' => false, 'message' => '请求失败: ' . $error];
        }
        
        // IndexNow API返回200表示成功
        if ($httpCode === 200) {
            return ['success' => true, 'message' => '提交成功'];
        } else {
            return ['success' => false, 'message' => 'HTTP ' . $httpCode . ': ' . $response];
        }
    }
    
    /**
     * 检查URL是否已提交过
     */
    private function isUrlAlreadySubmitted($url) {
        $sql = "SELECT COUNT(*) as count FROM " . TABLE_INDEXNOW_LOGS . " 
                WHERE url = ? AND submit_status = 'success' 
                AND created_at > DATE_SUB(NOW(), INTERVAL 1 DAY)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$url]);
        $result = $stmt->fetch();
        
        return $result['count'] > 0;
    }
    
    /**
     * 记录提交日志
     */
    private function logSubmission($url, $submitType, $status) {
        $sql = "INSERT INTO " . TABLE_INDEXNOW_LOGS . " 
                (url, submit_type, submit_status, created_at) 
                VALUES (?, ?, ?, NOW())";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$url, $submitType, $status]);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * 更新提交日志
     */
    private function updateSubmissionLog($logId, $status, $errorMessage = null) {
        $sql = "UPDATE " . TABLE_INDEXNOW_LOGS . " 
                SET submit_status = ?, error_message = ? 
                WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$status, $errorMessage, $logId]);
    }
    
    /**
     * 批量提交URL
     */
    public function batchSubmit($urls, $submitType = 'batch_submit') {
        $results = [];
        $successCount = 0;
        
        foreach ($urls as $url) {
            $result = $this->submitUrl($url, $submitType);
            $results[] = [
                'url' => $url,
                'result' => $result
            ];
            
            if ($result['success']) {
                $successCount++;
            }
            
            // 避免请求过于频繁
            usleep(100000); // 100ms延迟
        }
        
        return [
            'total' => count($urls),
            'success' => $successCount,
            'failed' => count($urls) - $successCount,
            'results' => $results
        ];
    }
    
    /**
     * 生成IndexNow验证文件
     */
    public function generateVerificationFile() {
        $content = INDEXNOW_API_KEY;
        $filePath = ROOT_PATH . '/indexnow.txt';
        
        if (file_put_contents($filePath, $content)) {
            return ['success' => true, 'message' => '验证文件生成成功'];
        } else {
            return ['success' => false, 'message' => '验证文件生成失败'];
        }
    }
    
    /**
     * 获取提交统计
     */
    public function getSubmissionStats($days = 30) {
        $sql = "SELECT 
                    submit_status,
                    COUNT(*) as count,
                    DATE(created_at) as date
                FROM " . TABLE_INDEXNOW_LOGS . " 
                WHERE created_at > DATE_SUB(NOW(), INTERVAL ? DAY)
                GROUP BY submit_status, DATE(created_at)
                ORDER BY date DESC, submit_status";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$days]);
        $rawData = $stmt->fetchAll();
        
        // 处理统计数据
        $stats = [
            'total_submissions' => 0,
            'success_submissions' => 0,
            'failed_submissions' => 0,
            'daily_stats' => []
        ];
        
        foreach ($rawData as $row) {
            $stats['total_submissions'] += $row['count'];
            
            if ($row['submit_status'] === 'success') {
                $stats['success_submissions'] += $row['count'];
            } else {
                $stats['failed_submissions'] += $row['count'];
            }
            
            if (!isset($stats['daily_stats'][$row['date']])) {
                $stats['daily_stats'][$row['date']] = [
                    'success' => 0,
                    'failed' => 0,
                    'total' => 0
                ];
            }
            
            if ($row['submit_status'] === 'success') {
                $stats['daily_stats'][$row['date']]['success'] += $row['count'];
            } else {
                $stats['daily_stats'][$row['date']]['failed'] += $row['count'];
            }
            $stats['daily_stats'][$row['date']]['total'] += $row['count'];
        }
        
        $stats['success_rate'] = $stats['total_submissions'] > 0 ? 
            round($stats['success_submissions'] / $stats['total_submissions'] * 100, 2) : 0;
        
        return $stats;
    }
}
?>