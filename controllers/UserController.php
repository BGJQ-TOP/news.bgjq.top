<?php
/**
 * 前台用户控制器
 */
require_once CORE_PATH . '/BaseController.php';

class UserController extends BaseController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new UserModel();
    }
    
    /**
     * 登录页面
     */
    public function login() {
        if (isset($_SESSION['bgjq_user_id'])) {
            header('Location: /profile');
            exit;
        }
        
        $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '/contribute';
        
        $rawHotArticles = (new ArticleModel())->getHotArticles(10);
        $hotArticles = $this->mapArticleFields($rawHotArticles);
        
        $this->render('login', [
            'hotArticles' => $hotArticles,
            'redirect' => $redirect,
            'pageTitle' => '用户登录 - 8W社区账号登录' . SEO_TITLE_SUFFIX,
            'pageDescription' => '使用8W社区账号登录邦国新闻系统，登录后可投稿文章。外交官可直接发布外交公告。' . SITE_DESCRIPTION,
            'pageKeywords' => '登录,8W社区,账号登录,外交官,' . SITE_KEYWORDS
        ]);
    }
    
    /**
     * 处理登录
     */
    public function doLogin() {
        header('Content-Type: application/json');
        
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : '/contribute';
        
        if (empty($username) || empty($password)) {
            echo json_encode(['success' => false, 'message' => '请输入用户名和密码']);
            return;
        }
        
        $user = $this->userModel->verifyUser($username, $password);
        
        if ($user) {
            $_SESSION['bgjq_user_id'] = $user['id'];
            $_SESSION['bgjq_username'] = $user['username'];
            $_SESSION['bgjq_role'] = $user['role'];
            $_SESSION['bgjq_game_id'] = $user['game_id'];
            $_SESSION['bgjq_country_id'] = $user['country_id'];
            
            echo json_encode([
                'success' => true, 
                'message' => '登录成功',
                'redirect' => $redirect
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => '用户名或密码错误']);
        }
    }
    
    /**
     * 退出登录
     */
    public function logout() {
        unset($_SESSION['bgjq_user_id']);
        unset($_SESSION['bgjq_username']);
        unset($_SESSION['bgjq_role']);
        unset($_SESSION['bgjq_game_id']);
        unset($_SESSION['bgjq_country_id']);
        
        header('Location: /');
        exit;
    }
    
    /**
     * 检查登录状态
     */
    public function checkLogin() {
        header('Content-Type: application/json');
        
        if (isset($_SESSION['bgjq_user_id'])) {
            $user = $this->userModel->getById($_SESSION['bgjq_user_id']);
            $country = $this->userModel->getUserCountry($_SESSION['bgjq_user_id']);
            
            echo json_encode([
                'success' => true,
                'logged_in' => true,
                'user' => [
                    'id' => $_SESSION['bgjq_user_id'],
                    'username' => $_SESSION['bgjq_username'],
                    'role' => $_SESSION['bgjq_role'],
                    'country' => $country
                ]
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'logged_in' => false
            ]);
        }
    }
    
    /**
     * 判断当前用户是否为外交官
     */
    public function isDiplomat() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['bgjq_user_id'])) {
            echo json_encode(['success' => false, 'message' => '未登录']);
            return;
        }
        
        $isDiplomat = $this->userModel->isDiplomat($_SESSION['bgjq_user_id']);
        
        echo json_encode([
            'success' => true,
            'is_diplomat' => $isDiplomat
        ]);
    }
    
    /**
     * 判断当前用户今日是否已发布外交公告
     */
    public function canPublishDiplomatAnnouncement() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['bgjq_user_id'])) {
            echo json_encode(['success' => false, 'message' => '未登录']);
            return;
        }
        
        if (!$this->userModel->isDiplomat($_SESSION['bgjq_user_id'])) {
            echo json_encode([
                'success' => true,
                'can_publish' => false,
                'reason' => '您不是外交官'
            ]);
            return;
        }
        
        if ($this->userModel->hasPublishedDiplomatAnnouncementToday($_SESSION['bgjq_user_id'])) {
            echo json_encode([
                'success' => true,
                'can_publish' => false,
                'reason' => '您今日已发布外交公告'
            ]);
            return;
        }
        
        echo json_encode([
            'success' => true,
            'can_publish' => true
        ]);
    }
    
    /**
     * 获取当前登录用户信息
     */
    public function getCurrentUser() {
        if (isset($_SESSION['bgjq_user_id'])) {
            return $this->userModel->getById($_SESSION['bgjq_user_id']);
        }
        return null;
    }
    
    /**
     * 检查用户是否已登录
     */
    public static function isLoggedIn() {
        return isset($_SESSION['bgjq_user_id']);
    }
    
    /**
     * 检查当前登录用户是否为外交官
     */
    public static function isUserDiplomat() {
        return isset($_SESSION['bgjq_role']) && $_SESSION['bgjq_role'] === 'diplomat';
    }
    
    /**
     * 检查当前登录用户是否有外交官权限或更高
     */
    public static function isUserDiplomatOrAbove() {
        if (!isset($_SESSION['bgjq_role'])) {
            return false;
        }
        return in_array($_SESSION['bgjq_role'], ['secretary_general', 'permanent_member', 'diplomat']);
    }
}
