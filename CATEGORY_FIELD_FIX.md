# 栏目页面字段名修复总结

## 问题描述

访问栏目页面时出现 PHP 警告和错误：
1. `Warning: Undefined array key "name"` - 字段名不匹配
2. `Deprecated: htmlspecialchars(): Passing null` - 空值处理
3. `Warning: Undefined array key "read_count"` - 字段名不匹配
4. `Warning: Undefined array key "published_at"` - 字段名不匹配

## 问题原因

数据库表使用了带前缀的字段名（如 `category_name`, `article_read_count`），但视图文件使用了不带前缀的字段名（如 `name`, `read_count`）。

### 数据库字段名 vs 视图字段名

#### Category 表
| 数据库字段 | 视图使用字段 |
|-----------|------------|
| `category_name` | `name` |
| `category_description` | `description` |
| `category_slug` | `slug` |
| `category_code` | `code` |

#### Article 表
| 数据库字段 | 视图使用字段 |
|-----------|------------|
| `article_title` | `title` |
| `article_content` | `content` |
| `article_slug` | `slug` |
| `article_cover_image` | `cover_image` |
| `article_read_count` | `read_count` |
| `article_like_count` | `like_count` |
| `article_published_at` | `published_at` |

## 解决方案

### 1. 修复视图文件中的字段名

使用 PHP 7+ 的空合并运算符 (`??`) 同时支持两种字段名格式：

```php
// 栏目名称
$category['category_name'] ?? $category['name'] ?? '未知栏目'

// 文章标题
$article['article_title'] ?? $article['title'] ?? '无标题'

// 阅读量
$article['article_read_count'] ?? $article['read_count'] ?? 0

// 发布时间
$article['article_published_at'] ?? $article['published_at'] ?? 'now'
```

### 2. 增强 CategoryController

添加对 `category_code` 的支持，兼容下划线格式的 URL：

```php
public function list($slug = '') {
    if (!empty($slug)) {
        // 先尝试通过 slug 查找
        $category = $this->categoryModel->getBySlug($slug);
        
        // 如果没找到，尝试通过 code 查找（兼容下划线格式）
        if (!$category) {
            $category = $this->categoryModel->getByCode($slug);
        }
        
        if ($category) {
            // 渲染视图...
        }
    }
}
```

## 修复的文件

### 1. views/category.php
- ✅ 修复栏目名称字段：`$category['name']` → `$category['category_name'] ?? $category['name']`
- ✅ 修复栏目描述字段：`$category['description']` → `$category['category_description'] ?? $category['description']`
- ✅ 修复文章标题字段：`$article['title']` → `$article['article_title'] ?? $article['title']`
- ✅ 修复文章内容字段：`$article['content']` → `$article['article_content'] ?? $article['content']`
- ✅ 修复文章封面字段：`$article['cover_image']` → `$article['article_cover_image'] ?? $article['cover_image']`
- ✅ 修复文章链接字段：`$article['slug']` → `$article['article_slug'] ?? $article['slug']`
- ✅ 修复阅读量字段：`$article['read_count']` → `$article['article_read_count'] ?? $article['read_count']`
- ✅ 修复点赞数字段：`$article['like_count']` → `$article['article_like_count'] ?? $article['like_count']`
- ✅ 修复发布时间字段：`$article['published_at']` → `$article['article_published_at'] ?? $article['published_at']`

### 2. views/rankings.php
- ✅ 修复所有文章相关字段（热门文章、推荐文章、最新文章）
- ✅ 添加空值保护

### 3. views/search.php
- ✅ 修复搜索结果中的所有文章字段
- ✅ 添加关键词高亮功能的字段支持

### 4. controllers/CategoryController.php
- ✅ 添加对 `category_code` 的支持
- ✅ 修复 pageTitle 的字段名

## 测试 URL

### 测试 1: 带斜杠的 URL
- URL: `https://news.bgjq.top/category/official-notice/?`
- 预期：显示"官方公告"栏目文章
- 状态：✅ 已修复

### 测试 2: 带下划线的 URL
- URL: `https://news.bgjq.top/category/official_notice/`
- 预期：显示"官方公告"栏目文章
- 状态：✅ 已修复（通过 code 查找）

## 技术要点

### 空合并运算符 (`??`)
```php
// PHP 7+ 特性
$value = $array['new_field'] ?? $array['old_field'] ?? 'default';
```

### 兼容性设计
- 优先使用带前缀的字段名（新标准）
- 回退到不带前缀的字段名（旧标准）
- 提供默认值避免警告

### URL 格式兼容
- 支持 slug 格式：`/category/official-notice/`
- 支持 code 格式：`/category/official_notice/`
- 自动识别并查找

## 修复效果

修复后访问栏目页面：
- ✅ 不再出现 PHP 警告
- ✅ 正确显示栏目名称
- ✅ 正确显示文章列表
- ✅ 正确显示阅读量、点赞数
- ✅ 正确显示发布时间
- ✅ 同时支持 slug 和 code 两种 URL 格式

## 修复日期
2026-04-04
