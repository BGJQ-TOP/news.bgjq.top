<?php
/**
 * IndexNow 验证控制器
 */
class IndexNowController {
    /**
     * 输出 IndexNow 验证文件
     */
    public function verify() {
        header('Content-Type: text/plain; charset=utf-8');
        header('Cache-Control: public, max-age=86400');
        echo INDEXNOW_API_KEY;
        exit;
    }
}
