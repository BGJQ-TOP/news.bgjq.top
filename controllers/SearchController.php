<?php
/**
 * 搜索控制器
 */
require_once CORE_PATH . '/BaseController.php';

class SearchController extends BaseController {
    private $articleModel;

    public function __construct() {
        $this->articleModel = new ArticleModel();
    }

    /**
     * 搜索页面
     */
    public function index() {
        $keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

        $rawHotArticles = $this->articleModel->getHotArticles(10);
        $hotArticles = $this->mapArticleFields($rawHotArticles);

        $results = null;
        if (!empty($keyword)) {
            $rawResults = $this->articleModel->searchArticles($keyword, $page, PAGE_SIZE);
            $results = [
                'data' => $this->mapArticleFields($rawResults['data']),
                'total' => $rawResults['total'],
                'page' => $rawResults['page'],
                'pageSize' => $rawResults['pageSize'],
                'totalPages' => $rawResults['totalPages']
            ];
        }

        $this->render('search', [
            'keyword' => $keyword,
            'results' => $results,
            'hotArticles' => $hotArticles,
            'pageTitle' => empty($keyword) ? '搜索 - 智能新闻搜索、精准资讯查找' . SEO_TITLE_SUFFIX : htmlspecialchars($keyword) . ' 的搜索结果 - 相关新闻、深度报道、最新资讯' . SEO_TITLE_SUFFIX,
            'pageDescription' => empty($keyword) ? '使用邦国新闻的智能搜索功能，快速查找您感兴趣的新闻内容。我们提供精准的关键词搜索，支持全文检索，帮助您快速找到相关新闻、深度报道和最新资讯。邦国新闻拥有庞大的新闻数据库，涵盖国内新闻、国际新闻、财经资讯、科技动态、娱乐八卦、体育赛事等多个领域。我们的搜索系统能够智能匹配相关文章，提供最准确的搜索结果。无论您想了解什么话题，都能在这里找到满意的答案。' : '搜索关键词"' . htmlspecialchars($keyword) . '"的相关新闻和资讯。邦国新闻为您提供最新、最全面的搜索结果，包括相关文章、深度报道和热点资讯。我们的搜索系统能够智能匹配相关内容，帮助您快速找到所需信息。无论是时事评论、深度分析还是最新动态，我们都能为您提供满意的搜索结果。欢迎使用邦国新闻的搜索功能，发现更多精彩内容。',
            'pageKeywords' => empty($keyword) ? '新闻搜索,资讯查找,关键词搜索,智能搜索,' . SITE_KEYWORDS : htmlspecialchars($keyword) . ',相关新闻,搜索结果,资讯查找,' . SITE_KEYWORDS
        ]);
    }
}
