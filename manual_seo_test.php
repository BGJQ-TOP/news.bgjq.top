<?php
/**
 * 手动SEO自动补充功能验证
 */

// 模拟配置
const SITE_NAME = '邦国新闻';
const SITE_DESCRIPTION = '简洁、专注的新闻阅读平台，提供最新资讯和热门文章';

// 自动补充页面标题（确保达到15字）
function auto_complete_title($title, $defaultTitle = null) {
    if (empty($title)) {
        $title = $defaultTitle ?: SITE_NAME;
    }
    
    // 计算当前标题的字数
    $currentLength = mb_strlen($title, 'UTF-8');
    
    // 如果标题已经达到15字，直接返回
    if ($currentLength >= 15) {
        return $title;
    }
    
    // 补充内容库
    $supplements = [
        ' - 最新资讯、热门推荐、精选文章阅读平台',
        ' - 专业新闻资讯平台，每日更新最新内容',
        ' - 深度报道、时事评论、热点新闻一网打尽',
        ' - 权威新闻媒体，提供高质量资讯服务',
        ' - 全面覆盖国内外新闻，满足您的信息需求'
    ];
    
    // 选择最合适的补充内容
    $bestSupplement = '';
    $minRemaining = 15;
    
    foreach ($supplements as $supplement) {
        $supplementLength = mb_strlen($supplement, 'UTF-8');
        $totalLength = $currentLength + $supplementLength;
        
        if ($totalLength >= 15 && $totalLength <= 30) {
            $remaining = $totalLength - 15;
            if ($remaining < $minRemaining) {
                $minRemaining = $remaining;
                $bestSupplement = $supplement;
            }
        }
    }
    
    // 如果没有找到合适的补充，使用默认补充
    if (empty($bestSupplement)) {
        $bestSupplement = ' - 专业新闻资讯平台';
    }
    
    return $title . $bestSupplement;
}

// 自动补充页面描述（确保达到150字）
function auto_complete_description($description, $defaultDescription = null) {
    if (empty($description)) {
        $description = $defaultDescription ?: SITE_DESCRIPTION;
    }
    
    // 计算当前描述的字数
    $currentLength = mb_strlen($description, 'UTF-8');
    
    // 如果描述已经达到150字，直接返回
    if ($currentLength >= 150) {
        return $description;
    }
    
    // 补充内容库
    $supplements = [
        ' 邦国新闻致力于为用户提供最新、最全面的新闻内容，涵盖国内外时事、财经资讯、科技动态、娱乐八卦、体育赛事等多个领域。我们拥有专业的编辑团队，确保新闻内容的准确性和时效性。平台提供个性化推荐和智能搜索功能，支持多种阅读模式，让用户能够轻松找到感兴趣的新闻内容。邦国新闻注重用户体验，界面简洁易用，内容质量高，是您获取新闻资讯的首选平台。',
        ' 我们每日更新时事新闻、热点资讯、深度报道，确保您能够及时了解最新动态。平台提供全面的新闻分类体系，帮助您快速找到感兴趣的新闻内容。无论是时事评论、深度分析还是最新动态，我们都能满足您的信息需求。欢迎持续关注邦国新闻，获取更多精彩内容。',
        ' 邦国新闻是一个专业的中文新闻资讯平台，专注于为用户提供高质量的新闻阅读体验。我们重视内容的准确性和时效性，每个栏目都有专业编辑团队精心挑选的内容。平台支持多种功能，包括个性化推荐、智能搜索、阅读历史记录等，让您能够更好地管理和使用新闻平台。',
        ' 我们的平台拥有庞大的新闻数据库，涵盖多个领域的内容。无论是国内新闻、国际新闻、财经资讯、科技动态、娱乐八卦还是体育赛事，都能在这里找到。我们致力于为用户提供最有价值的新闻内容，帮助您掌握最新动态。欢迎使用邦国新闻，发现更多精彩内容。'
    ];
    
    // 选择最合适的补充内容
    $bestSupplement = '';
    $minRemaining = 150;
    
    foreach ($supplements as $supplement) {
        $supplementLength = mb_strlen($supplement, 'UTF-8');
        $totalLength = $currentLength + $supplementLength;
        
        if ($totalLength >= 150 && $totalLength <= 200) {
            $remaining = $totalLength - 150;
            if ($remaining < $minRemaining) {
                $minRemaining = $remaining;
                $bestSupplement = $supplement;
            }
        }
    }
    
    // 如果没有找到合适的补充，使用默认补充
    if (empty($bestSupplement)) {
        $bestSupplement = ' 邦国新闻是一个专业的中文新闻资讯平台，致力于为用户提供最新、最全面的新闻内容。我们每日更新时事新闻、热点资讯、深度报道，涵盖多个领域的内容。平台拥有专业的编辑团队，确保新闻内容的准确性和时效性。我们提供个性化推荐、智能搜索功能，支持多种阅读模式，让用户能够轻松找到感兴趣的新闻内容。';
    }
    
    return $description . $bestSupplement;
}

// 测试用例
echo "=== SEO自动补充功能验证 ===\n\n";

// 测试后台页面标题
echo "后台页面标题测试：\n";
$adminTitles = [
    "系统设置",
    "数据统计", 
    "推送管理",
    "用户管理",
    "投稿审核",
    "栏目管理",
    "文章管理",
    "仪表板"
];

foreach ($adminTitles as $title) {
    $result = auto_complete_title($title);
    $length = mb_strlen($result, 'UTF-8');
    echo "• {$title} → {$result} (长度: {$length})\n";
}

echo "\n后台页面描述测试：\n";
foreach ($adminTitles as $title) {
    $result = auto_complete_description($title . "页面");
    $length = mb_strlen($result, 'UTF-8');
    echo "• {$title}页面 → " . substr($result, 0, 50) . "... (长度: {$length})\n";
}

echo "\n=== 验证完成 ===\n";
?>