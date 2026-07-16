# 栏目分类路由修复总结

## 问题描述
在首页点击栏目分类时提示"404 - 栏目未找到",所有栏目都一样。

## 问题原因
路由系统不支持参数传递。当访问 `/category/jinrishici/` 时:
- 路由系统能够正确匹配到 `CategoryController@list`
- 但是无法将 `jinrishici` 这个 slug 参数传递给控制器方法
- CategoryController 的 `getSlugFromUrl()` 方法也无法正确获取 slug

## 解决方案

### 1. 修改路由系统 (index.php)
- ✅ 添加参数提取逻辑
- ✅ 使用 `call_user_func_array` 传递参数给控制器方法
- ✅ 过滤 URL 末尾 `/` 产生的空字符串

### 2. 修改 CategoryController
- ✅ 修改 `list()` 方法接受 slug 参数
- ✅ 简化逻辑，直接使用传入的 slug
- ✅ 添加 `showError()` 方法处理错误
- ✅ 删除不再需要的 `getSlugFromUrl()` 方法

### 3. 创建视图文件
- ✅ 创建 `views/categories.php` - 栏目列表页面
- ✅ 创建 `views/category.php` - 栏目文章列表页面

### 4. 完善 Model 层
- ✅ CategoryModel 添加 `getAllCategories()` 方法
- ✅ ArticleModel 添加 `getByCategory()` 方法
- ✅ 修复 ArticleModel 中的字段名 (添加 `article_` 前缀)

## 修复后的路由逻辑

### URL 格式
```
/category/              - 显示所有栏目列表
/category/{slug}/       - 显示指定栏目的文章列表
```

### 路由处理流程
1. 用户访问 `/category/jinrishici/`
2. 路由系统解析:
   - `$routeKey = 'category'`
   - `$params = ['jinrishici']`
3. 调用 `CategoryController->list('jinrishici')`
4. 控制器获取栏目信息并渲染视图

## 代码变更

### index.php
```php
// 添加参数提取
$params = [];
if (isset($routes[$routeKey])) {
    list($controller, $action) = explode('@', $routes[$routeKey]);
    // 提取参数
    $params = array_slice($pathParts, 1);
    $params = array_filter($params, function($v) { return $v !== ''; });
    $params = array_values($params);
}

// 传递参数给控制器方法
call_user_func_array([$controllerInstance, $action], $params);
```

### CategoryController.php
```php
public function list($slug = '') {
    if (!empty($slug)) {
        $category = $this->categoryModel->getBySlug($slug);
        if ($category) {
            $articles = $this->articleModel->getByCategory($category['id']);
            $this->render('category', [
                'category' => $category,
                'articles' => $articles,
                'pageTitle' => $category['name'] . ' - ' . SITE_NAME
            ]);
            return;
        } else {
            $this->showError('栏目未找到');
            return;
        }
    }
    
    // 显示所有栏目
    $categories = $this->categoryModel->getAllCategories();
    $this->render('categories', [
        'categories' => $categories,
        'pageTitle' => '栏目大全 - ' . SITE_NAME
    ]);
}
```

### CategoryModel.php
```php
public function getAllCategories() {
    $sql = "SELECT * FROM {$this->table} WHERE category_is_active = 1 ORDER BY category_sort_order ASC, id ASC";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}
```

### ArticleModel.php
```php
public function getByCategory($categoryId) {
    $sql = "SELECT * FROM {$this->table} 
            WHERE article_category_id = ? AND article_status = 'published' 
            ORDER BY article_published_at DESC";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$categoryId]);
    return $stmt->fetchAll();
}
```

## 测试方法

1. 访问首页
2. 点击任意栏目分类卡片
3. 应该能够正确显示该栏目的文章列表
4. 如果栏目不存在，显示错误提示

## 相关文件

- `index.php` - 主入口文件 (已修改)
- `controllers/CategoryController.php` - 栏目控制器 (已修改)
- `models/CategoryModel.php` - 栏目模型 (已修改)
- `models/ArticleModel.php` - 文章模型 (已修改)
- `views/categories.php` - 栏目列表视图 (新建)
- `views/category.php` - 栏目文章列表视图 (新建)

## 修复日期
2026-04-04
