#!/bin/bash

# 邦国崛起新闻系统 - 自动部署脚本
# 适用于Linux + Nginx + PHP + MariaDB环境

set -e

# 颜色定义
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# 日志函数
log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# 检查权限
check_permissions() {
    if [[ $EUID -eq 0 ]]; then
        log_error "请勿使用root用户运行此脚本"
        exit 1
    fi
    
    if ! sudo -n true 2>/dev/null; then
        log_error "当前用户没有sudo权限"
        exit 1
    fi
}

# 检查依赖
check_dependencies() {
    log_info "检查系统依赖..."
    
    local deps=("nginx" "php8.1" "mariadb-server" "git" "curl" "unzip")
    local missing_deps=()
    
    for dep in "${deps[@]}"; do
        if ! command -v $dep &> /dev/null; then
            missing_deps+=($dep)
        fi
    done
    
    if [[ ${#missing_deps[@]} -gt 0 ]]; then
        log_warning "缺少以下依赖: ${missing_deps[*]}"
        log_info "开始安装依赖..."
        
        sudo apt update
        sudo apt install -y ${missing_deps[@]}
        
        log_success "依赖安装完成"
    else
        log_success "所有依赖已安装"
    fi
}

# 配置数据库
setup_database() {
    log_info "配置数据库..."
    
    # 检查MariaDB服务状态
    if ! systemctl is-active --quiet mariadb; then
        log_info "启动MariaDB服务..."
        sudo systemctl start mariadb
        sudo systemctl enable mariadb
    fi
    
    # 创建数据库和用户
    local db_name="bgjq_news"
    local db_user="bgjq_news_user"
    local db_password=$(openssl rand -base64 12)
    
    log_info "创建数据库: $db_name"
    sudo mysql -e "CREATE DATABASE IF NOT EXISTS $db_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    
    log_info "创建数据库用户: $db_user"
    sudo mysql -e "CREATE USER IF NOT EXISTS '$db_user'@'localhost' IDENTIFIED BY '$db_password';"
    sudo mysql -e "GRANT ALL PRIVILEGES ON $db_name.* TO '$db_user'@'localhost';"
    sudo mysql -e "FLUSH PRIVILEGES;"
    
    # 保存数据库配置
    cat > database_config.txt << EOF
数据库配置信息：
数据库名称: $db_name
数据库用户: $db_user
数据库密码: $db_password
EOF
    
    log_success "数据库配置完成"
}

# 配置PHP
setup_php() {
    log_info "配置PHP环境..."
    
    # 安装PHP扩展
    local php_extensions=(
        "php8.1-fpm"
        "php8.1-mysql"
        "php8.1-mbstring"
        "php8.1-xml"
        "php8.1-curl"
        "php8.1-gd"
        "php8.1-zip"
        "php8.1-intl"
    )
    
    sudo apt install -y ${php_extensions[@]}
    
    # 配置PHP-FPM
    local php_ini="/etc/php/8.1/fpm/php.ini"
    local php_fpm_conf="/etc/php/8.1/fpm/pool.d/www.conf"
    
    # 优化PHP配置
    sudo sed -i 's/^max_execution_time = .*/max_execution_time = 300/' $php_ini
    sudo sed -i 's/^memory_limit = .*/memory_limit = 256M/' $php_ini
    sudo sed -i 's/^post_max_size = .*/post_max_size = 100M/' $php_ini
    sudo sed -i 's/^upload_max_filesize = .*/upload_max_filesize = 100M/' $php_ini
    sudo sed -i 's/^display_errors = .*/display_errors = Off/' $php_ini
    sudo sed -i 's/^log_errors = .*/log_errors = On/' $php_ini
    
    # 重启PHP-FPM
    sudo systemctl restart php8.1-fpm
    sudo systemctl enable php8.1-fpm
    
    log_success "PHP配置完成"
}

# 配置Nginx
setup_nginx() {
    log_info "配置Nginx..."
    
    # 创建网站目录
    local web_root="/var/www/news.bgjq.top"
    sudo mkdir -p $web_root
    
    # 设置目录权限
    sudo chown -R www-data:www-data $web_root
    sudo chmod -R 755 $web_root
    
    # 复制Nginx配置文件
    sudo cp deploy/nginx.conf /etc/nginx/sites-available/news.bgjq.top
    
    # 启用网站
    sudo ln -sf /etc/nginx/sites-available/news.bgjq.top /etc/nginx/sites-enabled/
    
    # 测试Nginx配置
    if ! sudo nginx -t; then
        log_error "Nginx配置测试失败"
        exit 1
    fi
    
    # 重启Nginx
    sudo systemctl restart nginx
    sudo systemctl enable nginx
    
    log_success "Nginx配置完成"
}

# 部署应用代码
deploy_application() {
    log_info "部署应用代码..."
    
    local web_root="/var/www/news.bgjq.top"
    
    # 复制文件到网站目录
    sudo cp -r . $web_root/
    
    # 设置正确的权限
    sudo chown -R www-data:www-data $web_root
    sudo find $web_root -type d -exec chmod 755 {} \;
    sudo find $web_root -type f -exec chmod 644 {} \;
    
    # 设置可执行权限
    sudo chmod +x $web_root/deploy/deploy.sh
    
    # 创建必要的目录
    sudo mkdir -p $web_root/logs
    sudo mkdir -p $web_root/uploads
    sudo mkdir -p $web_root/cache
    
    sudo chown www-data:www-data $web_root/logs $web_root/uploads $web_root/cache
    sudo chmod 755 $web_root/logs $web_root/uploads $web_root/cache
    
    log_success "应用代码部署完成"
}

# 导入数据库
import_database() {
    log_info "导入数据库结构..."
    
    local db_name="bgjq_news"
    local db_user="bgjq_news_user"
    local db_password=$(grep "数据库密码" database_config.txt | cut -d: -f2 | tr -d ' ')
    
    # 导入基础数据库
    if [[ -f "sql/bgjq.sql" ]]; then
        log_info "导入现有数据库..."
        mysql -u $db_user -p$db_password $db_name < sql/bgjq.sql
    fi
    
    # 导入新闻系统升级脚本
    if [[ -f "sql/news_system_upgrade.sql" ]]; then
        log_info "导入新闻系统升级脚本..."
        mysql -u $db_user -p$db_password $db_name < sql/news_system_upgrade.sql
    fi
    
    log_success "数据库导入完成"
}

# 配置环境变量
setup_environment() {
    log_info "配置环境变量..."
    
    local web_root="/var/www/news.bgjq.top"
    local db_password=$(grep "数据库密码" database_config.txt | cut -d: -f2 | tr -d ' ')
    local indexnow_key=$(openssl rand -hex 16)
    local password_salt=$(openssl rand -hex 16)
    
    # 创建 .env 环境配置文件
    cat > $web_root/.env << EOF
# 数据库配置
DB_HOST=localhost
DB_PORT=3306
DB_NAME=bgjq_news
DB_USER=bgjq_news_user
DB_PASS=$db_password
DB_CHARSET=utf8mb4

# 站点配置
SITE_URL=https://news.bgjq.top
SITE_NAME=邦国新闻
DEBUG_MODE=false

# IndexNow 配置
INDEXNOW_ENABLED=true
INDEXNOW_API_KEY=$indexnow_key

# 安全配置
PASSWORD_SALT=$password_salt
SESSION_TIMEOUT=7200
CSRF_TOKEN_NAME=csrf_token

# SMTP 邮件配置
SMTP_HOST=smtp.example.com
SMTP_PORT=587
SMTP_USER=noreply@news.bgjq.top
SMTP_PASS=password
SMTP_SECURE=tls
EOF
    
    # 设置文件权限
    sudo chmod 600 $web_root/.env
    sudo chown www-data:www-data $web_root/.env
    
    log_success "环境变量配置完成"
}

# 生成SSL证书（可选）
generate_ssl() {
    log_info "生成SSL证书（可选）..."
    
    if command -v certbot &> /dev/null; then
        log_info "安装Certbot..."
        sudo apt install -y certbot python3-certbot-nginx
        
        log_info "申请SSL证书..."
        sudo certbot --nginx -d news.bgjq.top -d www.news.bgjq.top
        
        # 设置自动续期
        (sudo crontab -l 2>/dev/null; echo "0 12 * * * /usr/bin/certbot renew --quiet") | sudo crontab -
        
        log_success "SSL证书配置完成"
    else
        log_warning "Certbot未安装，跳过SSL证书生成"
    fi
}

# 系统优化
optimize_system() {
    log_info "系统优化..."
    
    # 优化内核参数
    cat >> /etc/sysctl.conf << EOF
# 网络优化
net.core.somaxconn = 65535
net.core.netdev_max_backlog = 65535
net.ipv4.tcp_max_syn_backlog = 65535
net.ipv4.tcp_syncookies = 1
net.ipv4.tcp_tw_reuse = 1
net.ipv4.tcp_tw_recycle = 1
net.ipv4.tcp_fin_timeout = 30

# 内存优化
vm.swappiness = 10
vm.dirty_ratio = 60
vm.dirty_background_ratio = 2
EOF
    
    sudo sysctl -p
    
    # 创建日志轮转配置
    cat > /etc/logrotate.d/news-bgjq << EOF
/var/log/nginx/news.bgjq.top.access.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
    postrotate
        /usr/sbin/nginx -s reload
    endscript
}

/var/log/php/error.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
}
EOF
    
    log_success "系统优化完成"
}

# 健康检查
health_check() {
    log_info "执行健康检查..."
    
    local checks_passed=0
    local total_checks=6
    
    # 检查Nginx状态
    if systemctl is-active --quiet nginx; then
        echo "✓ Nginx服务运行正常"
        ((checks_passed++))
    else
        echo "✗ Nginx服务异常"
    fi
    
    # 检查PHP-FPM状态
    if systemctl is-active --quiet php8.1-fpm; then
        echo "✓ PHP-FPM服务运行正常"
        ((checks_passed++))
    else
        echo "✗ PHP-FPM服务异常"
    fi
    
    # 检查MariaDB状态
    if systemctl is-active --quiet mariadb; then
        echo "✓ MariaDB服务运行正常"
        ((checks_passed++))
    else
        echo "✗ MariaDB服务异常"
    fi
    
    # 检查网站可访问性
    if curl -s -o /dev/null -w "%{http_code}" http://localhost | grep -q "200"; then
        echo "✓ 网站可正常访问"
        ((checks_passed++))
    else
        echo "✗ 网站访问异常"
    fi
    
    # 检查数据库连接
    if mysql -u bgjq_news_user -p$(grep "数据库密码" database_config.txt | cut -d: -f2 | tr -d ' ') -e "SELECT 1;" &> /dev/null; then
        echo "✓ 数据库连接正常"
        ((checks_passed++))
    else
        echo "✗ 数据库连接异常"
    fi
    
    # 检查文件权限
    if [[ -w "/var/www/news.bgjq.top/logs" ]]; then
        echo "✓ 文件权限设置正确"
        ((checks_passed++))
    else
        echo "✗ 文件权限异常"
    fi
    
    if [[ $checks_passed -eq $total_checks ]]; then
        log_success "所有健康检查通过 ($checks_passed/$total_checks)"
    else
        log_warning "部分健康检查未通过 ($checks_passed/$total_checks)"
    fi
}

# 显示部署信息
show_deployment_info() {
    log_success "部署完成！"
    echo ""
    echo "=== 部署信息汇总 ==="
    echo "网站地址: https://news.bgjq.top"
    echo "后台地址: https://news.bgjq.top/admin"
    echo "API接口: https://news.bgjq.top/api/v1/push"
    echo ""
    echo "=== 默认管理员账号 ==="
    echo "用户名: admin"
    echo "密码: 请首次登录后立即修改"
    echo ""
    echo "=== 数据库配置 ==="
    cat database_config.txt
    echo ""
    echo "=== 后续操作 ==="
    echo "1. 修改默认管理员密码"
    echo "2. 配置IndexNow密钥"
    echo "3. 添加子站配置"
    echo "4. 配置SSL证书（如需）"
    echo ""
    echo "部署日志文件: deploy.log"
}

# 主函数
main() {
    echo ""
    echo "=== 邦国崛起新闻系统自动部署脚本 ==="
    echo ""
    
    # 记录部署日志
    exec > >(tee -a deploy.log) 2>&1
    
    # 执行部署步骤
    check_permissions
    check_dependencies
    setup_database
    setup_php
    setup_nginx
    deploy_application
    import_database
    setup_environment
    optimize_system
    health_check
    show_deployment_info
    
    echo ""
    log_success "部署脚本执行完毕！"
}

# 执行主函数
main "$@"