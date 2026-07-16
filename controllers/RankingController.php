<?php
/**
 * 榜单控制器
 */
require_once CORE_PATH . '/BaseController.php';

class RankingController extends BaseController {
    private $articleModel;

    public function __construct() {
        $this->articleModel = new ArticleModel();
    }

    /**
     * 榜单页面
     */
    public function index() {
        $hotArticles = $this->mapArticleFields($this->articleModel->getHotArticles(20));
        $latestArticles = $this->mapArticleFields($this->articleModel->getPublishedArticles(null, 1, 20));

        $this->render('rankings', [
            'hotArticles' => $hotArticles,
            'latestArticles' => $latestArticles,
            'pageTitle' => '排行榜单 - 热门文章排行、最新资讯榜单' . SEO_TITLE_SUFFIX,
            'pageDescription' => '查看邦国新闻的热门文章排行榜和最新资讯榜单。我们根据阅读量、点赞数等指标为您推荐最受欢迎的文章，同时展示最新的新闻内容。帮助您快速了解当前热点话题和重要新闻事件。我们的榜单系统实时更新，确保您能够看到最热门、最新的新闻资讯。无论是时事热点、深度分析还是娱乐八卦，都能在这里找到。邦国新闻致力于为用户提供最有价值的新闻内容，帮助您掌握最新动态。欢迎查看我们的排行榜单，发现更多精彩内容。',
            'pageKeywords' => '排行榜单,热门文章,最新资讯,文章排行,热点话题,新闻榜单,' . SITE_KEYWORDS
        ]);
    }
}
