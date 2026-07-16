<?php
/**
 * 首页控制器
 */
class HomeController {
    private $articleModel;
    private $categoryModel;
    
    public function __construct() {
        $this->articleModel = new ArticleModel();
        $this->categoryModel = new CategoryModel();
    }
    
    /**
     * 首页
     */
    public function index() {
        // 获取置顶文章（轮播图）
        $rawTopArticles = $this->articleModel->getTopArticles(5);
        $topArticles = $this->mapArticleFields($rawTopArticles);
        
        // 获取推荐文章
        $rawFeaturedArticles = $this->articleModel->getFeaturedArticles(8);
        $featuredArticles = $this->mapArticleFields($rawFeaturedArticles);
        
        // 获取最新文章
        $rawLatestArticles = $this->articleModel->getPublishedArticles(null, 1, 20);
        $latestArticles = [
            'data' => $this->mapArticleFields($rawLatestArticles),
            'total' => count($rawLatestArticles),
            'page' => 1,
            'pageSize' => 20,
            'totalPages' => 1
        ];
        
        // 获取热门文章
        $rawHotArticles = $this->articleModel->getHotArticles(10);
        $hotArticles = $this->mapArticleFields($rawHotArticles);
        
        // 获取栏目统计
        $categoryStats = $this->categoryModel->getCategoryStats();
        
        // 渲染首页
        $this->render('home', [
            'topArticles' => $topArticles,
            'featuredArticles' => $featuredArticles,
            'latestArticles' => $latestArticles,
            'hotArticles' => $hotArticles,
            'categoryStats' => $categoryStats
        ]);
    }
    
    /**
     * 字段名映射（数据库字段 -> 模板字段）
     */
    private function mapArticleFields($rawArticles) {
        return array_map(function($raw) {
            return [
                'id' => $raw['id'],
                'title' => $raw['article_title'],
                'content' => $raw['article_content'],
                'slug' => $raw['article_slug'],
                'cover_image' => $raw['article_cover_image'] ?? null,
                'seo_title' => $raw['article_seo_title'] ?? null,
                'seo_keywords' => $raw['article_seo_keywords'] ?? null,
                'seo_description' => $raw['article_seo_description'] ?? null,
                'read_count' => $raw['article_read_count'] ?? 0,
                'like_count' => $raw['article_like_count'] ?? 0,
                'published_at' => $raw['article_published_at'],
                'updated_at' => $raw['article_updated_at'] ?? $raw['article_published_at'],
                'author_id' => $raw['article_author_id'] ?? null,
                'author_name' => $raw['author_name'] ?? null,
                'category_id' => $raw['article_category_id']
            ];
        }, $rawArticles);
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
}
?>