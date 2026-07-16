# 数据库设置指南

## 🔍 问题诊断

当前错误信息表明：`Table 'bgjq_news.news_articles' doesn't exist`

这意味着虽然数据库连接配置正确，但独立新闻系统的表还没有被创建。

## 🚀 解决方案

### 方案1：手动导入数据库（推荐）

#### 步骤1：登录MySQL
```bash
# 登录MySQL（使用您提供的数据库信息）
mysql -u bgjq -pBGJQ1314!
```

#### 步骤2：检查当前数据库状态
```sql
-- 查看当前数据库中的表
SHOW TABLES FROM bgjq_news;

-- 如果表不存在，继续下一步
```

#### 步骤3：导入独立新闻系统数据库
```sql
-- 切换到目标数据库
USE bgjq_news;

-- 导入独立新闻系统表结构
SOURCE /var/www/news.bgjq.top/sql/news_system_standalone.sql;

-- 或者使用source命令的完整路径
SOURCE /var/www/news.bgjq.top/sql/news_system_standalone.sql;
```

#### 步骤4：验证导入结果
```sql
-- 查看导入的表
SHOW TABLES;

-- 检查表结构
DESCRIBE news_articles;
DESCRIBE news_categories;

-- 检查示例数据
SELECT * FROM news_articles LIMIT 3;
SELECT * FROM news_categories;
```

### 方案2：使用命令行导入

```bash
# 直接使用mysql命令导入
mysql -u bgjq -pBGJQ1314! bgjq_news < /var/www/news.bgjq.top/sql/news_system_standalone.sql

# 验证导入
mysql -u bgjq -pBGJQ1314! -e "SHOW TABLES FROM bgjq_news;"
```

### 方案3：使用PHP脚本自动创建表

如果上述方法不可行，可以使用以下PHP脚本自动创建表：

```php
<?php
// database_setup.php - 自动创建数据库表
require_once 'config/database.php';

try {
    $db = getDbConnection();
    
    // 检查表是否存在
    $tables = ['news_articles', 'news_categories', 'news_tags', 'news_article_tags'];
    
    foreach ($tables as $table) {
        $stmt = $db->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        $exists = $stmt->fetch();
        
        if (!$exists) {
            echo "表 {$table} 不存在，需要创建...\n";
        } else {
            echo "表 {$table} 已存在\n";
        }
    }
    
    echo "请手动导入 sql/news_system_standalone.sql 文件\n";
    
} catch (PDOException $e) {
    echo "数据库错误: " . $e->getMessage() . "\n";
}
?>
```

## 📋 数据库表结构验证

导入成功后，应该有以下表：

### 核心表
1. `news_articles` - 新闻文章主表
2. `news_categories` - 新闻栏目分类表  
3. `news_tags` - 新闻标签表
4. `news_article_tags` - 文章标签关联表

### 用户交互表
5. `news_likes` - 新闻点赞记录表
6. `news_comments` - 新闻评论表

### 管理系统表
7. `news_admin_users` - 新闻后台管理员表
8. `news_subsite_configs` - 新闻子站配置表
9. `news_push_logs` - 新闻推送日志表
10. `news_indexnow_logs` - IndexNow提交日志表
11. `news_operation_logs` - 新闻操作日志表
12. `news_carousels` - 新闻轮播图表

## 🔧 故障排除

### 常见错误及解决方案

#### 错误1：权限不足
```bash
# 错误信息：Access denied for user 'bgjq'@'localhost'
# 解决方案：检查用户权限
mysql -u root -p -e "GRANT ALL PRIVILEGES ON bgjq_news.* TO 'bgjq'@'localhost'; FLUSH PRIVILEGES;"
```

#### 错误2：数据库不存在
```bash
# 错误信息：Unknown database 'bgjq_news'
# 解决方案：创建数据库
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS bgjq_news CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

#### 错误3：SQL文件路径错误
```bash
# 确保SQL文件路径正确
ls -la /var/www/news.bgjq.top/sql/

# 如果文件不存在，重新上传
scp sql/news_system_standalone.sql root@your-server-ip:/var/www/news.bgjq.top/sql/
```

## ✅ 验证步骤

### 1. 数据库连接验证
```php
<?php
// test_db.php
require_once 'config/database.php';
$db = getDbConnection();
if ($db) {
    echo "数据库连接成功！\n";
    
    // 测试查询
    $stmt = $db->query("SELECT COUNT(*) as count FROM news_articles");
    $result = $stmt->fetch();
    echo "文章数量: " . $result['count'] . "\n";
    
} else {
    echo "数据库连接失败！\n";
}
?>
```

### 2. 网站功能验证
- 访问首页：https://news.bgjq.top
- 访问后台：https://news.bgjq.top/admin
- 测试API：https://news.bgjq.top/api/v1/push

### 3. 日志检查
```bash
# 检查错误日志
tail -f /var/log/nginx/news.bgjq.top.error.log
tail -f /var/log/php8.1-fpm.log
```

## 📞 技术支持

如果遇到问题，请检查：
1. 数据库用户权限
2. SQL文件路径和内容
3. 文件权限设置
4. 错误日志信息

**导入完成后，您的新闻系统应该可以正常运行！** 🎉