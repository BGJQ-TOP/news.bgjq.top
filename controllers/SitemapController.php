<?php
/**
 * Sitemap 控制器
 */
class SitemapController {
    private $articleModel;
    private $categoryModel;
    
    public function __construct() {
        $this->articleModel = new ArticleModel();
        $this->categoryModel = new CategoryModel();
    }
    
    /**
     * 生成 XML sitemap
     */
    public function index() {
        // 设置 XML 内容类型
        header('Content-Type: application/xml; charset=utf-8');
        header('Cache-Control: max-age=3600, public');
        
        // 获取基础 URL
        $baseUrl = 'https://news.bgjq.top';
        
        // 开始构建 XML
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        // 1. 首页
        $xml .= '<url>';
        $xml .= '<loc>' . htmlspecialchars($baseUrl, ENT_XML1, 'UTF-8') . '</loc>';
        $xml .= '<lastmod>' . date('Y-m-d') . '</lastmod>';
        $xml .= '<changefreq>always</changefreq>';
        $xml .= '<priority>1.0</priority>';
        $xml .= '</url>';
        
        // 2. 栏目列表页
        $categories = $this->categoryModel->getAllCategories();
        foreach ($categories as $category) {
            $categoryUrl = $baseUrl . '/category/' . htmlspecialchars($category['category_slug'], ENT_XML1, 'UTF-8');
            $xml .= '<url>';
            $xml .= '<loc>' . $categoryUrl . '</loc>';
            $xml .= '<lastmod>' . date('Y-m-d') . '</lastmod>';
            $xml .= '<changefreq>daily</changefreq>';
            $xml .= '<priority>0.8</priority>';
            $xml .= '</url>';
        }
        
        // 3. 所有已发布文章
        $page = 1;
        $pageSize = 1000;
        
        while (true) {
            $articles = $this->articleModel->getAll(
                ['article_status' => 'published'],
                'article_published_at DESC',
                null
            );
            
            // 如果文章数量少于 pageSize，说明已经是最后一页
            if (empty($articles)) {
                break;
            }
            
            foreach ($articles as $article) {
                $articleUrl = $baseUrl . '/a/' . htmlspecialchars($article['article_slug'], ENT_XML1, 'UTF-8');
                $lastmod = !empty($article['article_updated_at']) 
                    ? date('Y-m-d', strtotime($article['article_updated_at']))
                    : date('Y-m-d', strtotime($article['article_published_at']));
                
                $xml .= '<url>';
                $xml .= '<loc>' . $articleUrl . '</loc>';
                $xml .= '<lastmod>' . $lastmod . '</lastmod>';
                $xml .= '<changefreq>weekly</changefreq>';
                $xml .= '<priority>0.6</priority>';
                $xml .= '</url>';
            }
            
            // 如果文章数量少于 pageSize，说明已经是最后一页，退出循环
            if (count($articles) < $pageSize) {
                break;
            }
            
            $page++;
        }
        
        // 4. 其他静态页面
        $staticPages = [
            '/contribute' => 0.5,
            '/rankings' => 0.5,
            '/search' => 0.3,
        ];
        
        foreach ($staticPages as $path => $priority) {
            $xml .= '<url>';
            $xml .= '<loc>' . $baseUrl . $path . '</loc>';
            $xml .= '<lastmod>' . date('Y-m-d') . '</lastmod>';
            $xml .= '<changefreq>monthly</changefreq>';
            $xml .= '<priority>' . $priority . '</priority>';
            $xml .= '</url>';
        }
        
        $xml .= '</urlset>';
        
        echo $xml;
    }
}
