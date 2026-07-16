<?php
/**
 * 核心函数库
 */

/**
 * 安全过滤输入
 */
function safe_input($input) {
    if (is_array($input)) {
        return array_map('safe_input', $input);
    }
    
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    
    return $input;
}

/**
 * 生成安全的URL别名
 */
function generate_slug($title) {
    $slug = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $title);
    $slug = preg_replace('/[\s-]+/', '-', $slug);
    $slug = trim($slug, '-');
    $slug = mb_strtolower($slug, 'UTF-8');
    
    return $slug;
}

/**
 * 生成分页HTML
 */
function pagination($total, $current_page, $page_size = PAGE_SIZE) {
    $total_pages = ceil($total / $page_size);
    if ($total_pages <= 1) return '';
    
    $html = '<nav aria-label="Page navigation"><ul class="pagination">';
    
    // 上一页
    if ($current_page > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="?page=' . ($current_page - 1) . '">上一页</a></li>';
    }
    
    // 页码
    $start = max(1, $current_page - PAGE_RANGE);
    $end = min($total_pages, $current_page + PAGE_RANGE);
    
    for ($i = $start; $i <= $end; $i++) {
        $active = $i == $current_page ? ' active' : '';
        $html .= '<li class="page-item' . $active . '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
    }
    
    // 下一页
    if ($current_page < $total_pages) {
        $html .= '<li class="page-item"><a class="page-link" href="?page=' . ($current_page + 1) . '">下一页</a></li>';
    }
    
    $html .= '</ul></nav>';
    
    return $html;
}

/**
 * 获取客户端IP
 */
function get_client_ip() {
    $ip = '';
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    
    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
}

/**
 * 生成随机字符串
 */
function generate_random_string($length = 32) {
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $result = '';
    for ($i = 0; $i < $length; $i++) {
        $result .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $result;
}

/**
 * 密码加密
 */
function password_hash_custom($password) {
    return password_hash($password . PASSWORD_SALT, PASSWORD_DEFAULT);
}

/**
 * 验证密码
 */
function password_verify_custom($password, $hash) {
    return password_verify($password . PASSWORD_SALT, $hash);
}

/**
 * 生成CSRF Token
 */
function generate_csrf_token() {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * 验证CSRF Token
 */
function verify_csrf_token($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

/**
 * 记录操作日志
 */
function log_operation($type, $target = null, $detail = null) {
    $db = getDbConnection();
    if (!$db) return false;
    
    $sql = "INSERT INTO " . TABLE_OPERATION_LOGS . " 
            (admin_user_id, operation_type, operation_target, operation_detail, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $admin_id = isset($_SESSION['admin_user_id']) ? $_SESSION['admin_user_id'] : null;
    $ip = get_client_ip();
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute([$admin_id, $type, $target, $detail, $ip, $user_agent]);
        return true;
    } catch (PDOException $e) {
        error_log("操作日志记录失败: " . $e->getMessage());
        return false;
    }
}

/**
 * 获取设置值
 */
function get_setting($key, $default = null) {
    // 这里可以从数据库或缓存中获取设置值
    // 暂时返回默认值
    $settings = [
        'site_name' => SITE_NAME,
        'site_description' => SITE_DESCRIPTION,
        'site_keywords' => SITE_KEYWORDS,
        // 其他设置...
    ];
    
    return isset($settings[$key]) ? $settings[$key] : $default;
}

/**
 * 格式化时间
 */
function format_time($timestamp, $format = 'Y-m-d H:i:s') {
    if (empty($timestamp)) return '';
    return date($format, strtotime($timestamp));
}

/**
 * 截取字符串（支持中文）
 */
function truncate_string($string, $length, $suffix = '...') {
    if (mb_strlen($string, 'UTF-8') <= $length) {
        return $string;
    }
    
    return mb_substr($string, 0, $length, 'UTF-8') . $suffix;
}

/**
 * 生成文章URL
 */
function generate_article_url($article) {
    if (is_numeric($article)) {
        // 这里需要根据文章ID获取文章信息
        // 暂时返回默认URL
        return SITE_URL . '/article/' . $article;
    }
    
    if (is_array($article) && isset($article['article_slug'])) {
        return SITE_URL . '/a/' . $article['article_slug'];
    }
    
    return SITE_URL;
}

/**
 * 自动补充页面标题（确保达到15字）
 */
function auto_complete_title($title, $defaultTitle = null) {
    if (empty($title)) {
        $title = $defaultTitle ?: SITE_NAME;
    }
    
    // 计算当前标题的字数
    $currentLength = mb_strlen($title, 'UTF-8');
    
    // 如果标题已经达到15字，直接返回
    if ($currentLength >= 15) {
        return $title;
    }
    
    // 补充内容库
    $supplements = [
        ' - 最新资讯、热门推荐、精选文章阅读平台',
        ' - 专业新闻资讯平台，每日更新最新内容',
        ' - 深度报道、时事评论、热点新闻一网打尽',
        ' - 权威新闻媒体，提供高质量资讯服务',
        ' - 全面覆盖国内外新闻，满足您的信息需求'
    ];
    
    // 选择最合适的补充内容
    $bestSupplement = '';
    $minRemaining = 15;
    
    foreach ($supplements as $supplement) {
        $supplementLength = mb_strlen($supplement, 'UTF-8');
        $totalLength = $currentLength + $supplementLength;
        
        if ($totalLength >= 15 && $totalLength <= 30) {
            $remaining = $totalLength - 15;
            if ($remaining < $minRemaining) {
                $minRemaining = $remaining;
                $bestSupplement = $supplement;
            }
        }
    }
    
    // 如果没有找到合适的补充，使用默认补充
    if (empty($bestSupplement)) {
        $bestSupplement = ' - 专业新闻资讯平台';
    }
    
    return $title . $bestSupplement;
}

/**
 * 自动补充页面描述（确保达到150字）
 */
function auto_complete_description($description, $defaultDescription = null) {
    if (empty($description)) {
        $description = $defaultDescription ?: SITE_DESCRIPTION;
    }
    
    // 计算当前描述的字数
    $currentLength = mb_strlen($description, 'UTF-8');
    
    // 如果描述已经达到150字，直接返回
    if ($currentLength >= 150) {
        return $description;
    }
    
    // 补充内容库
    $supplements = [
        ' 邦国新闻致力于为用户提供最新、最全面的新闻内容，涵盖国内外时事、财经资讯、科技动态、娱乐八卦、体育赛事等多个领域。我们拥有专业的编辑团队，确保新闻内容的准确性和时效性。平台提供个性化推荐和智能搜索功能，支持多种阅读模式，让用户能够轻松找到感兴趣的新闻内容。邦国新闻注重用户体验，界面简洁易用，内容质量高，是您获取新闻资讯的首选平台。',
        ' 我们每日更新时事新闻、热点资讯、深度报道，确保您能够及时了解最新动态。平台提供全面的新闻分类体系，帮助您快速找到感兴趣的新闻内容。无论是时事评论、深度分析还是最新动态，我们都能满足您的信息需求。欢迎持续关注邦国新闻，获取更多精彩内容。',
        ' 邦国新闻是一个专业的中文新闻资讯平台，专注于为用户提供高质量的新闻阅读体验。我们重视内容的准确性和时效性，每个栏目都有专业编辑团队精心挑选的内容。平台支持多种功能，包括个性化推荐、智能搜索、阅读历史记录等，让您能够更好地管理和使用新闻平台。',
        ' 我们的平台拥有庞大的新闻数据库，涵盖多个领域的内容。无论是国内新闻、国际新闻、财经资讯、科技动态、娱乐八卦还是体育赛事，都能在这里找到。我们致力于为用户提供最有价值的新闻内容，帮助您掌握最新动态。欢迎使用邦国新闻，发现更多精彩内容。'
    ];
    
    // 选择最合适的补充内容
    $bestSupplement = '';
    $minRemaining = 150;
    
    foreach ($supplements as $supplement) {
        $supplementLength = mb_strlen($supplement, 'UTF-8');
        $totalLength = $currentLength + $supplementLength;
        
        if ($totalLength >= 150 && $totalLength <= 200) {
            $remaining = $totalLength - 150;
            if ($remaining < $minRemaining) {
                $minRemaining = $remaining;
                $bestSupplement = $supplement;
            }
        }
    }
    
    // 如果没有找到合适的补充，使用默认补充
    if (empty($bestSupplement)) {
        $bestSupplement = ' 邦国新闻是一个专业的中文新闻资讯平台，致力于为用户提供最新、最全面的新闻内容。我们每日更新时事新闻、热点资讯、深度报道，涵盖多个领域的内容。平台拥有专业的编辑团队，确保新闻内容的准确性和时效性。我们提供个性化推荐、智能搜索功能，支持多种阅读模式，让用户能够轻松找到感兴趣的新闻内容。';
    }
    
    return $description . $bestSupplement;
}

/**
 * 智能生成页面标题和描述
 */
function generate_seo_meta($pageTitle = null, $pageDescription = null, $context = []) {
    // 自动补充标题
    $finalTitle = auto_complete_title($pageTitle, SITE_NAME);
    
    // 自动补充描述
    $finalDescription = auto_complete_description($pageDescription, SITE_DESCRIPTION);
    
    return [
        'title' => $finalTitle,
        'description' => $finalDescription
    ];
}

/**
 * 生成栏目URL
 */
function generate_category_url($category) {
    if (is_numeric($category)) {
        $category_model = new CategoryModel();
        $category = $category_model->getById($category);
        if (!$category) return '';
    }
    
    $slug = !empty($category['slug']) ? $category['slug'] : 'category-' . $category['id'];
    return SITE_URL . '/category/' . $slug . '/';
}

/**
 * 检查文件类型
 */
function check_file_type($filename, $allowed_types) {
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $allowed = explode(',', $allowed_types);
    return in_array($extension, $allowed);
}

/**
 * 文件上传处理
 */
function handle_file_upload($file, $target_dir, $max_size = UPLOAD_MAX_SIZE) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => '文件上传错误'];
    }
    
    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => '文件大小超过限制'];
    }
    
    if (!check_file_type($file['name'], UPLOAD_ALLOW_TYPES)) {
        return ['success' => false, 'message' => '文件类型不允许'];
    }
    
    // 生成唯一文件名
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $target_path = $target_dir . '/' . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return ['success' => true, 'filename' => $filename, 'path' => $target_path];
    } else {
        return ['success' => false, 'message' => '文件移动失败'];
    }
}
?>