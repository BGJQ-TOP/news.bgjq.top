<?php
/**
 * 栏目控制器
 */
require_once CORE_PATH . '/BaseController.php';

class CategoryController extends BaseController {
    private $articleModel;
    private $categoryModel;

    public function __construct() {
        $this->articleModel = new ArticleModel();
        $this->categoryModel = new CategoryModel();
    }

    /**
     * 栏目列表页 (支持 slug 参数)
     * @param string $slug 栏目 slug (可选参数)
     */
    public function list($slug = '') {
        $rawHotArticles = $this->articleModel->getHotArticles(10);
        $hotArticles = $this->mapArticleFields($rawHotArticles);

        if (!empty($slug)) {
            $category = $this->categoryModel->getBySlug($slug);

            if (!$category) {
                $category = $this->categoryModel->getByCode($slug);
            }

            if ($category) {
                $rawArticles = $this->articleModel->getByCategory($category['id']);
                $articles = $this->mapArticleFields($rawArticles);

                $this->render('category', [
                    'category' => $category,
                    'articles' => $articles,
                    'hotArticles' => $hotArticles,
                    'pageTitle' => ($category['category_name'] ?? $category['name'] ?? '栏目') . ' - 最新文章、深度报道、资讯阅读' . SEO_TITLE_SUFFIX,
                    'pageDescription' => '探索' . ($category['category_name'] ?? $category['name'] ?? '栏目') . '栏目的最新文章和深度报道。' . ($category['category_description'] ?? $category['description'] ?? '') . ' 我们提供专业、及时、准确的新闻内容，涵盖各种热点话题和重要事件。邦国新闻致力于为用户提供高质量的新闻阅读体验，每个栏目都有专业编辑团队精心挑选的内容。无论是时事评论、深度分析还是最新动态，我们都能满足您的信息需求。欢迎持续关注' . ($category['category_name'] ?? $category['name'] ?? '栏目') . '栏目，获取最新资讯。',
                    'pageKeywords' => ($category['category_name'] ?? $category['name'] ?? '栏目') . ',新闻,资讯,文章,阅读,' . SITE_KEYWORDS
                ]);
                return;
            } else {
                $this->showError('栏目未找到');
                return;
            }
        }

        $categories = $this->categoryModel->getAllCategories();

        $this->render('categories', [
            'categories' => $categories,
            'hotArticles' => $hotArticles,
            'pageTitle' => '栏目大全 - 全面新闻分类、专业资讯平台' . SEO_TITLE_SUFFIX,
            'pageDescription' => '浏览邦国新闻的所有栏目分类，包括国内新闻、国际新闻、财经资讯、科技动态、娱乐八卦、体育赛事等。我们提供全面的新闻分类体系，帮助您快速找到感兴趣的新闻内容。每个栏目都有专业编辑精心挑选的最新文章和深度报道。邦国新闻致力于为用户提供高质量的新闻阅读体验，涵盖各种热点话题和重要事件。无论是时事评论、深度分析还是最新动态，我们都能满足您的信息需求。欢迎探索我们的栏目大全，发现更多精彩内容。',
            'pageKeywords' => '栏目大全,新闻分类,资讯平台,国内新闻,国际新闻,财经资讯,科技动态,娱乐八卦,体育赛事,' . SITE_KEYWORDS
        ]);
    }

    /**
     * 显示错误页面
     */
    private function showError($message) {
        http_response_code(404);
        $this->render('error', [
            'message' => $message,
            'pageTitle' => '错误 - ' . SITE_NAME
        ]);
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
            <title>栏目未找到 - ' . SITE_NAME . '</title>
            <style>
                body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
                h1 { color: #333; }
                p { color: #666; }
                a { color: #007bff; text-decoration: none; }
            </style>
        </head>
        <body>
            <h1>404 - 栏目未找到</h1>
            <p>抱歉，您访问的栏目不存在。</p>
            <p><a href="/">返回首页</a></p>
        </body>
        </html>';
        exit;
    }
}
?>
