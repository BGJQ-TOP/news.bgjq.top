<?php
/**
 * 控制器基类
 * 提供公共方法和属性
 */
abstract class BaseController {

    /**
     * 字段名映射（数据库字段 -> 模板字段）
     * 将数据库返回的原始数据转换为模板所需的格式
     *
     * @param array $rawArticles 数据库返回的原始文章数据
     * @return array 格式化后的文章数据
     */
    protected function mapArticleFields($rawArticles) {
        return array_map(function($raw) {
            return [
                'id' => $raw['id'],
                'title' => $raw['article_title'],
                'content' => $raw['article_content'],
                'slug' => $raw['article_slug'],
                'cover_image' => $raw['article_cover_image'] ?? null,
                'read_count' => $raw['article_read_count'] ?? 0,
                'like_count' => $raw['article_like_count'] ?? 0,
                'published_at' => $raw['article_published_at'],
                'author_name' => $raw['author_name'] ?? null
            ];
        }, $rawArticles);
    }

    /**
     * 渲染视图
     *
     * @param string $view 视图名称
     * @param array $data 传递给视图的数据
     */
    protected function render($view, $data = []) {
        extract($data);

        require_once VIEW_PATH . '/layouts/header.php';

        $viewFile = VIEW_PATH . '/' . $view . '.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            echo "<h3>视图文件不存在：{$viewFile}</h3>";
            echo "<p>请创建该视图文件。</p>";
        }

        require_once VIEW_PATH . '/layouts/footer.php';
    }
}
