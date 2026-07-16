<?php
/**
 * 文章控制器
 */
class ArticleController {
    private $articleModel;
    private $categoryModel;
    
    public function __construct() {
        $this->articleModel = new ArticleModel();
        $this->categoryModel = new CategoryModel();
    }
    
    /**
     * 文章详情页
     */
    public function detail() {
        // 获取文章别名
        $slug = $this->getSlugFromUrl();
        
        if (empty($slug)) {
            $this->show404();
            return;
        }
        
        // 获取文章详情
        $article = $this->articleModel->getBySlug($slug);
        
        if (!$article) {
            $this->show404();
            return;
        }
        
        // 字段名映射（数据库字段 -> 模板字段）
        $article = [
            'id' => $article['id'],
            'title' => $article['article_title'],
            'content' => $article['article_content'],
            'slug' => $article['article_slug'],
            'cover_image' => $article['article_cover_image'] ?? null,
            'seo_title' => $article['article_seo_title'] ?? null,
            'seo_keywords' => $article['article_seo_keywords'] ?? null,
            'seo_description' => $article['article_seo_description'] ?? null,
            'read_count' => $article['article_read_count'] ?? 0,
            'like_count' => $article['article_like_count'] ?? 0,
            'published_at' => $article['article_published_at'],
            'updated_at' => $article['article_updated_at'] ?? $article['article_published_at'],
            'author_id' => $article['article_author_id'] ?? null,
            'category_id' => $article['article_category_id']
        ];
        
        // 增加阅读量
        $this->articleModel->incrementReadCount($article['id']);
        
        // 获取栏目 ID
        $categoryId = $article['category_id'];
        
        // 获取相关文章
        $rawRelatedArticles = $this->articleModel->getRelatedArticles($article['id'], $categoryId, 6);
        $relatedArticles = array_map(function($raw) {
            return [
                'id' => $raw['id'],
                'title' => $raw['article_title'],
                'slug' => $raw['article_slug'],
                'cover_image' => $raw['article_cover_image'] ?? null,
                'published_at' => $raw['article_published_at']
            ];
        }, $rawRelatedArticles);
        
        // 获取栏目信息
        $category = $this->categoryModel->getById($categoryId);
        
        if (!$category) {
            $this->show404();
            return;
        }
        
        // 设置页面 SEO 信息
        $pageTitle = !empty($article['seo_title']) ? $article['seo_title'] : $article['title'];
        $pageDescription = !empty($article['seo_description']) ? $article['seo_description'] : truncate_string(strip_tags($article['content']), 150);
        $pageKeywords = !empty($article['seo_keywords']) ? $article['seo_keywords'] : $category['category_name'] . ',' . SITE_KEYWORDS;
        
        // 渲染文章详情页
        $this->render('article/detail', [
            'article' => $article,
            'category' => $category,
            'relatedArticles' => $relatedArticles,
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'pageKeywords' => $pageKeywords
        ]);
    }
    
    /**
     * 从 URL 中获取文章别名
     */
    private function getSlugFromUrl() {
        $path = $_SERVER['REQUEST_URI'];
        
        // 匹配 /article/slug.html 或 /a/slug.html 格式
        if (preg_match('/\/(?:article|a)\/([^\/]+)(?:\.html)?/', $path, $matches)) {
            // URL解码，支持中文slug
            return urldecode($matches[1]);
        }
        
        return null;
    }
    
    /**
     * 渲染视图
     */
    private function render($view, $data = []) {
        extract($data);
        
        // 加载头部
        require_once VIEW_PATH . '/layouts/header.php';
        
        // 加载主体内容
        $viewFile = VIEW_PATH . '/' . $view . '.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            echo "<p>视图文件不存在: {$viewFile}</p>";
        }
        
        // 加载底部
        require_once VIEW_PATH . '/layouts/footer.php';
    }
    
    /**
     * 显示404页面
     */
    private function show404() {
        http_response_code(404);
        
        echo '<!DOCTYPE html>
        <html lang="zh-CN">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>文章未找到 - ' . SITE_NAME . '</title>
            <style>
                body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
                h1 { color: #333; }
                p { color: #666; }
                a { color: #007bff; text-decoration: none; }
            </style>
        </head>
        <body>
            <h1>404 - 文章未找到</h1>
            <p>抱歉，您访问的文章不存在或已被删除。</p>
            <p><a href="/">返回首页</a></p>
        </body>
        </html>';
        exit;
    }
}
?>