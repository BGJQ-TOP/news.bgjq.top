<?php
/**
 * 个人中心控制器
 */
class ProfileController {
    /**
     * 个人中心页面
     */
    public function index() {
        if (!isset($_SESSION['bgjq_user_id'])) {
            header('Location: /admin/login');
            exit;
        }

        $pageTitle = '个人中心 - 用户信息管理、阅读历史记录' . SEO_TITLE_SUFFIX;
        $pageDescription = '邦国新闻个人中心，管理您的用户信息、查看阅读历史、收藏文章等。我们提供个性化的阅读体验，帮助您更好地管理和使用新闻平台。在个人中心，您可以查看自己的阅读记录、管理收藏的文章、设置个性化偏好。我们致力于为用户提供便捷的个人信息管理功能，让您能够更好地享受新闻阅读的乐趣。无论是查看历史记录还是管理个人资料，都能在这里轻松完成。欢迎使用邦国新闻的个人中心功能，享受更优质的新闻阅读体验。';
        $pageKeywords = '个人中心,用户信息,阅读历史,收藏文章,账户管理,' . SITE_KEYWORDS;
        
        require_once VIEW_PATH . '/layouts/header.php';
        require_once VIEW_PATH . '/profile.php';
        require_once VIEW_PATH . '/layouts/footer.php';
    }
}
?>
