<?php
/**
 * 投稿控制器
 */
require_once CORE_PATH . '/BaseController.php';

class ContributionController extends BaseController {
    private $articleModel;
    private $userModel;

    public function __construct() {
        $this->articleModel = new ArticleModel();
        $this->userModel = new UserModel();
    }

    /**
     * 投稿页面 - 需要登录
     */
    public function index() {
        if (!UserController::isLoggedIn()) {
            header('Location: /login?redirect=/contribute');
            exit;
        }

        $rawHotArticles = $this->articleModel->getHotArticles(10);
        $hotArticles = $this->mapArticleFields($rawHotArticles);

        $user = $this->userModel->getById($_SESSION['bgjq_user_id']);
        $country = $this->userModel->getUserCountry($_SESSION['bgjq_user_id']);
        $isDiplomat = UserController::isUserDiplomatOrAbove();
        $canPublishDiplomat = false;

        if ($isDiplomat) {
            $canPublishDiplomat = !$this->userModel->hasPublishedDiplomatAnnouncementToday($_SESSION['bgjq_user_id']);
        }

        $this->render('contribute', [
            'hotArticles' => $hotArticles,
            'isLoggedIn' => true,
            'currentUser' => $user,
            'userCountry' => $country,
            'isDiplomat' => $isDiplomat,
            'canPublishDiplomat' => $canPublishDiplomat,
            'pageTitle' => '投稿中心 - 原创内容投稿、新闻稿件提交' . SEO_TITLE_SUFFIX,
            'pageDescription' => '欢迎向邦国新闻投稿您的原创内容！我们接受各类新闻稿件、深度报道、评论文章等。投稿流程简单快捷，专业编辑团队审核，优质内容将在网站发布。外交官可直接发布外交公告。' . SITE_DESCRIPTION,
            'pageKeywords' => '投稿中心,原创投稿,新闻稿件,内容投稿,文章提交,媒体投稿,外交公告,' . SITE_KEYWORDS
        ]);
    }

    /**
     * 外交公告页面 - 仅外交官可访问
     */
    public function diplomat() {
        if (!UserController::isLoggedIn()) {
            header('Location: /login?redirect=/diplomat');
            exit;
        }

        if (!UserController::isUserDiplomatOrAbove()) {
            echo '<!DOCTYPE html>
            <html lang="zh-CN">
            <head>
                <meta charset="UTF-8">
                <title>权限不足 - ' . SITE_NAME . '</title>
                <link rel="stylesheet" href="' . ASSET_URL . '/css/nes.min.css">
                <link rel="stylesheet" href="' . ASSET_URL . '/css/style.css">
            </head>
            <body>
                <div class="container" style="padding: 50px; text-align: center;">
                    <div class="nes-container is-rounded">
                        <h2 class="nes-text is-error"><i class="nes-icon close is-small"></i> 权限不足</h2>
                        <p>您没有外交官权限，无法发布外交公告。</p>
                        <p>如需获取外交官权限，请联系您所属邦国的管理员。</p>
                        <a href="/" class="nes-btn is-primary">返回首页</a>
                    </div>
                </div>
            </body>
            </html>';
            exit;
        }

        if ($this->userModel->hasPublishedDiplomatAnnouncementToday($_SESSION['bgjq_user_id'])) {
            echo '<!DOCTYPE html>
            <html lang="zh-CN">
            <head>
                <meta charset="UTF-8">
                <title>今日已发布 - ' . SITE_NAME . '</title>
                <link rel="stylesheet" href="' . ASSET_URL . '/css/nes.min.css">
                <link rel="stylesheet" href="' . ASSET_URL . '/css/style.css">
            </head>
            <body>
                <div class="container" style="padding: 50px; text-align: center;">
                    <div class="nes-container is-rounded">
                        <h2 class="nes-text is-warning"><i class="nes-icon trophy is-small"></i> 今日已发布</h2>
                        <p>您今日已发布一篇外交公告，每日仅可发布一篇。</p>
                        <p>请明日再来发布新的外交公告。</p>
                        <a href="/" class="nes-btn is-primary">返回首页</a>
                    </div>
                </div>
            </body>
            </html>';
            exit;
        }

        $rawHotArticles = $this->articleModel->getHotArticles(10);
        $hotArticles = $this->mapArticleFields($rawHotArticles);

        $user = $this->userModel->getById($_SESSION['bgjq_user_id']);
        $country = $this->userModel->getUserCountry($_SESSION['bgjq_user_id']);

        $this->render('diplomat', [
            'hotArticles' => $hotArticles,
            'currentUser' => $user,
            'userCountry' => $country,
            'pageTitle' => '发布外交公告 - 外交官专属' . SEO_TITLE_SUFFIX,
            'pageDescription' => '外交官专属公告发布页面，可直接发布外交公告到网站。' . SITE_DESCRIPTION,
            'pageKeywords' => '外交公告,外交官,邦国公告,' . SITE_KEYWORDS
        ]);
    }

    /**
     * 处理投稿
     */
    public function submit() {
        header('Content-Type: application/json');

        if (!UserController::isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => '请先登录']);
            return;
        }

        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $content = isset($_POST['content']) ? trim($_POST['content']) : '';
        $category = isset($_POST['category']) ? intval($_POST['category']) : 0;
        $coverImage = isset($_POST['cover_image']) ? trim($_POST['cover_image']) : '';

        if (empty($title) || empty($content) || empty($category)) {
            echo json_encode(['success' => false, 'message' => '请填写所有必填项']);
            return;
        }

        $slug = generate_slug($title) . '-' . time();

        $excerpt = mb_substr(strip_tags($content), 0, 200, 'UTF-8');

        $sql = "INSERT INTO news_articles 
                (article_title, article_content, article_excerpt, article_category_id, 
                 article_slug, article_cover_image, article_status, article_source_type,
                 article_author_id, article_author_name, article_published_at, article_created_at)
                VALUES (?, ?, ?, ?, ?, ?, 'pending', 'user_contribution', ?, ?, NOW(), NOW())";

        $stmt = getDbConnection()->prepare($sql);
        $result = $stmt->execute([
            $title,
            $content,
            $excerpt,
            $category,
            $slug,
            $coverImage,
            $_SESSION['bgjq_user_id'],
            $_SESSION['bgjq_username']
        ]);

        if ($result) {
            echo json_encode(['success' => true, 'message' => '投稿成功，等待审核']);
        } else {
            echo json_encode(['success' => false, 'message' => '投稿失败，请稍后重试']);
        }
    }

    /**
     * 处理外交公告发布
     */
    public function publishDiplomatAnnouncement() {
        header('Content-Type: application/json');

        if (!UserController::isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => '请先登录']);
            return;
        }

        if (!UserController::isUserDiplomatOrAbove()) {
            echo json_encode(['success' => false, 'message' => '您没有外交官权限，无法发布外交公告']);
            return;
        }

        if ($this->userModel->hasPublishedDiplomatAnnouncementToday($_SESSION['bgjq_user_id'])) {
            echo json_encode(['success' => false, 'message' => '您今日已发布外交公告，每日仅可发布一篇']);
            return;
        }

        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $content = isset($_POST['content']) ? trim($_POST['content']) : '';

        if (empty($title) || empty($content)) {
            echo json_encode(['success' => false, 'message' => '请填写标题和内容']);
            return;
        }

        $slug = generate_slug($title) . '-' . time();

        $excerpt = mb_substr(strip_tags($content), 0, 200, 'UTF-8');

        $sql = "INSERT INTO news_articles 
                (article_title, article_content, article_excerpt, article_category_id, 
                 article_slug, article_cover_image, article_status, article_source_type,
                 article_author_id, article_author_name, article_published_at, article_created_at,
                 article_is_headline)
                VALUES (?, ?, ?, 1, ?, '', 'published', 'diplomat_announcement', ?, ?, NOW(), NOW(), 1)";

        $stmt = getDbConnection()->prepare($sql);
        $result = $stmt->execute([
            $title,
            $content,
            $excerpt,
            $slug,
            $_SESSION['bgjq_user_id'],
            $_SESSION['bgjq_username']
        ]);

        if ($result) {
            // 提交 IndexNow
            $indexNow = new IndexNowService();
            $indexNow->submitUrl(SITE_URL . '/a/' . $slug, 'diplomat_publish');
            
            echo json_encode(['success' => true, 'message' => '外交公告发布成功']);
        } else {
            echo json_encode(['success' => false, 'message' => '发布失败，请稍后重试']);
        }
    }
}
