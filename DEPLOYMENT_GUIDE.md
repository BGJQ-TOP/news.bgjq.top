# 邦国崛起新闻系统 - 最终部署指南

## 📋 部署前准备

### 服务器要求
- **操作系统**: Linux (Ubuntu 20.04+ 推荐)
- **Web服务器**: Nginx 1.18+
- **PHP版本**: PHP 8.1+
- **数据库**: MariaDB 10.6+
- **内存**: 至少 2GB RAM
- **存储**: 至少 10GB 可用空间

### 数据库信息（已提供）
- **数据库用户**: `bgjq`
- **数据库密码**: `BGJQ1314!`
- **数据库名称**: `bgjq_news` (将自动创建)

## 🚀 一键部署脚本

### 1. 上传项目文件
```bash
# 将项目文件上传到服务器
scp -r news.bgjq.top/ root@your-server-ip:/var/www/

# 设置正确的权限
sudo chown -R www-data:www-data /var/www/news.bgjq.top
sudo chmod -R 755 /var/www/news.bgjq.top
```

### 2. 运行自动部署脚本
```bash
# 进入项目目录
cd /var/www/news.bgjq.top

# 给部署脚本执行权限
chmod +x deploy/deploy.sh

# 运行部署脚本（将自动配置数据库）
./deploy/deploy.sh
```

## 🔧 手动部署步骤

### 1. 安装系统依赖
```bash
# 更新系统
sudo apt update && sudo apt upgrade -y

# 安装必要软件
sudo apt install -y nginx mariadb-server php8.1 php8.1-fpm php8.1-mysql php8.1-mbstring php8.1-xml php8.1-curl php8.1-gd php8.1-zip php8.1-intl
```

### 2. 配置数据库
```bash
# 登录MySQL
sudo mysql -u root -p

# 创建新闻系统数据库
CREATE DATABASE IF NOT EXISTS bgjq_news CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# 检查用户是否存在，如果不存在则创建
CREATE USER IF NOT EXISTS 'bgjq'@'localhost' IDENTIFIED BY 'BGJQ1314!';

# 授予权限
GRANT ALL PRIVILEGES ON bgjq_news.* TO 'bgjq'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3. 导入数据库结构
```bash
# 导入基础数据库（如果尚未导入）
mysql -u bgjq -pBGJQ1314! < sql/bgjq.sql

# 导入独立新闻系统数据库
mysql -u bgjq -pBGJQ1314! < sql/news_system_standalone.sql
```

### 4. 配置PHP
```bash
# 编辑PHP配置文件
sudo nano /etc/php/8.1/fpm/php.ini

# 修改以下参数：
max_execution_time = 300
memory_limit = 256M
post_max_size = 100M
upload_max_filesize = 100M
display_errors = Off
log_errors = On

# 重启PHP-FPM
sudo systemctl restart php8.1-fpm
sudo systemctl enable php8.1-fpm
```

### 5. 配置Nginx
```bash
# 复制Nginx配置文件
sudo cp news.bgjq.top /etc/nginx/sites-available/news.bgjq.top

# 启用网站
sudo ln -sf /etc/nginx/sites-available/news.bgjq.top /etc/nginx/sites-enabled/

# 禁用默认站点（如果存在）
sudo rm -f /etc/nginx/sites-enabled/default

# 测试Nginx配置
sudo nginx -t

# 重启Nginx
sudo systemctl restart nginx
sudo systemctl enable nginx
```

### 6. 配置环境变量
```bash
# 创建环境配置文件
sudo nano /var/www/news.bgjq.top/config/env.php
```

**env.php 内容：**
```php
<?php
// 数据库配置
define('DB_HOST', 'localhost');
define('DB_NAME', 'bgjq_news');
define('DB_USER', 'bgjq');
define('DB_PASS', 'BGJQ1314!');
define('DB_CHARSET', 'utf8mb4');

// 系统配置
define('SITE_URL', 'https://news.bgjq.top');
define('SITE_NAME', '邦国崛起新闻系统');
define('DEBUG_MODE', false);

// IndexNow配置
define('INDEXNOW_ENABLED', true);
define('INDEXNOW_API_KEY', 'your_indexnow_key_here');
define('INDEXNOW_BING_URL', 'https://www.bing.com/indexnow');
define('INDEXNOW_YANDEX_URL', 'https://yandex.com/indexnow');

// 安全配置
define('SESSION_TIMEOUT', 3600);
define('CSRF_TOKEN_NAME', 'csrf_token');

// 路径配置
define('ROOT_PATH', '/var/www/news.bgjq.top');
define('VIEW_PATH', ROOT_PATH . '/views');
define('ASSET_URL', SITE_URL . '/assets');
?>
```

### 7. 设置文件权限
```bash
# 设置正确的文件权限
sudo chown -R www-data:www-data /var/www/news.bgjq.top
sudo find /var/www/news.bgjq.top -type d -exec chmod 755 {} \;
sudo find /var/www/news.bgjq.top -type f -exec chmod 644 {} \;

# 创建必要的目录
sudo mkdir -p /var/www/news.bgjq.top/logs
sudo mkdir -p /var/www/news.bgjq.top/uploads
sudo mkdir -p /var/www/news.bgjq.top/cache

sudo chown www-data:www-data /var/www/news.bgjq.top/logs /var/www/news.bgjq.top/uploads /var/www/news.bgjq.top/cache
sudo chmod 755 /var/www/news.bgjq.top/logs /var/www/news.bgjq.top/uploads /var/www/news.bgjq.top/cache
```

## 🔒 SSL证书配置（可选）

### 使用Certbot获取免费SSL证书
```bash
# 安装Certbot
sudo apt install -y certbot python3-certbot-nginx

# 获取SSL证书
sudo certbot --nginx -d news.bgjq.top -d www.news.bgjq.top

# 设置自动续期
(sudo crontab -l 2>/dev/null; echo "0 12 * * * /usr/bin/certbot renew --quiet") | sudo crontab -
```

## 🧪 系统测试

### 1. 运行系统测试
```bash
cd /var/www/news.bgjq.top
php tests/SystemTest.php
```

### 2. 健康检查
```bash
# 检查服务状态
sudo systemctl status nginx
sudo systemctl status php8.1-fpm
sudo systemctl status mariadb

# 测试网站访问
curl -I https://news.bgjq.top
```

## 🌐 访问系统

### 前台访问
- **网址**: https://news.bgjq.top
- **功能**: 新闻浏览、文章阅读、用户互动

### 后台管理
- **网址**: https://news.bgjq.top/admin
- **默认账号**: 
  - 用户名: `admin`
  - 密码: `admin123`
- **功能**: 文章管理、栏目管理、用户管理、数据统计

### API接口
- **推送API**: https://news.bgjq.top/api/v1/push
- **文档**: 查看项目README.md中的API文档

## 📊 部署完成检查清单

- [ ] Nginx服务正常运行
- [ ] PHP-FPM服务正常运行
- [ ] MariaDB服务正常运行
- [ ] 数据库连接正常
- [ ] 网站可以正常访问
- [ ] 后台管理可以登录
- [ ] 系统测试通过
- [ ] SSL证书配置（如需要）
- [ ] 文件权限设置正确

## 🔧 故障排除

### 常见问题

1. **Nginx配置测试失败**
   ```bash
   # 检查配置语法
   sudo nginx -t
   
   # 查看错误日志
   sudo tail -f /var/log/nginx/error.log
   ```

2. **数据库连接失败**
   ```bash
   # 测试数据库连接
   mysql -u bgjq -pBGJQ1314! -e "SELECT 1;"
   ```

3. **文件权限问题**
   ```bash
   # 重新设置权限
   sudo chown -R www-data:www-data /var/www/news.bgjq.top
   sudo find /var/www/news.bgjq.top -type d -exec chmod 755 {} \;
   sudo find /var/www/news.bgjq.top -type f -exec chmod 644 {} \;
   ```

4. **PHP错误**
   ```bash
   # 查看PHP错误日志
   sudo tail -f /var/log/php8.1-fpm.log
   ```

## 📞 技术支持

如果部署过程中遇到问题，请检查：
1. 系统日志文件
2. 错误信息提示
3. 网络连接状态
4. 文件权限设置

**部署完成！您的邦国崛起新闻系统现在已经可以正常使用了！** 🎉