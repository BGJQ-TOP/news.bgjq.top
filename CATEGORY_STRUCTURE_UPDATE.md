# 栏目结构调整说明

## 📋 调整内容

### 1. 删除的栏目
以下栏目已从系统中移除（设置为非激活状态）：

- ❌ **联合国动态** (`un_dynamic`)
- ❌ **萌新攻略** (`newbie_guide`)  
- ❌ **玩家投稿** (`player_contribution`) - 作为独立栏目移除

### 2. 合并的栏目
- ✅ **服务器要闻** + **邦国风云** → **邦国新闻** (`country_news`)
  - 原服务器要闻的编码从 `server_news` 改为 `country_news`
  - 栏目名称改为"邦国新闻"
  - 描述更新为：服务器邦国外交、领土战报、重大事件、邦国故事等综合新闻

### 3. 保留的栏目
- ✅ **官方公告** (`official_notice`) - 排序第 1
- ✅ **邦国新闻** (`country_news`) - 排序第 2

## 🔧 技术改动

### 数据库变更

**重要提示：** 如果数据库中还没有 `news_categories` 表，请先执行以下脚本创建表：

```bash
# 1. 先创建新闻系统表（如果还没有执行过）
mysql -u 用户名 -p bgjq < sql/news_system_standalone.sql

# 2. 再执行栏目结构调整脚本
mysql -u 用户名 -p bgjq < sql/category_structure_update.sql
```

或者在 MySQL 命令行中依次执行：

```sql
-- 先创建表（如果还没有）
source sql/news_system_standalone.sql;

-- 再调整栏目结构
source sql/category_structure_update.sql;
```

栏目结构调整 SQL 脚本内容：
```sql
-- 删除不需要的栏目
UPDATE news_categories SET category_is_active = 0 WHERE category_code = 'un_dynamic';
UPDATE news_categories SET category_is_active = 0 WHERE category_code = 'newbie_guide';
UPDATE news_categories SET category_is_active = 0 WHERE category_code = 'player_contribution';

-- 合并服务器要闻和邦国风云
UPDATE news_categories 
SET category_name = '邦国新闻', category_code = 'country_news', category_slug = 'country-news', ...
WHERE category_code = 'server_news';

UPDATE news_categories SET category_is_active = 0 WHERE category_code = 'country_story';
```

### 模型更新

#### CategoryModel.php
- 使用 `news_categories` 表名
- 使用 `category_*` 前缀的字段名
- `getCategoryStats()` 方法不再区分投稿类型，统一统计文章数量

#### ArticleModel.php
- 所有文章查询方法添加作者名称关联（`LEFT JOIN users`）
- 返回的文章数据包含 `author_name` 字段
- 修改的方法：
  - `getPublishedArticles()`
  - `getFeaturedArticles()`
  - `getTopArticles()`
  - `getHotArticles()`
  - `getBySlug()`
  - `getByCategory()`

### 控制器更新

#### HomeController.php
- 文章字段映射添加 `author_name`

#### CategoryController.php
- 文章字段映射添加 `author_name`

#### RankingController.php
- 文章字段映射添加 `author_name`

### 视图更新

#### views/home.php
- 显示作者名称（如果有）
- 简化布局，专注核心内容

#### views/category.php
- 显示作者名称（如果有）
- 移除返回栏目列表按钮

## 📝 使用说明

### 投稿功能
- ✅ 投稿入口保留在导航栏和用户菜单中
- ✅ 投稿文章审核后发布
- ✅ 发布的文章自动显示作者名称
- ✅ 不再对"玩家投稿"进行特殊标记或分类统计

### 栏目导航
系统现在只有 2 个主要栏目：
1. **官方公告** - 服务器维护、版本更新、规则变更等官方公告
2. **邦国新闻** - 服务器邦国外交、领土战报、重大事件、邦国故事等综合新闻

### 作者显示
- 所有发布的文章都会在适当位置显示作者名称
- 作者名称显示在文章卡片底部，与阅读量、点赞数、发布日期并列
- 如果文章没有关联作者，则不显示作者信息

## 🎯 效果

- ✅ 栏目结构更加简洁清晰
- ✅ 去除了不必要的分类（联合国动态、萌新攻略、玩家投稿栏目）
- ✅ 合并了相似内容（服务器要闻 + 邦国风云 = 邦国新闻）
- ✅ 保留投稿功能，但不做特殊对待
- ✅ 通过的文章显示作者名称，增强用户认同感
- ✅ 所有栏目统一显示文章总数，不再区分投稿类型

## 📌 注意事项

1. 执行 SQL 脚本前请备份数据库
2. 执行后需要清理缓存（如有）
3. 检查前台和后台的栏目显示是否正常
4. 如有自定义的栏目相关功能，需要相应调整
