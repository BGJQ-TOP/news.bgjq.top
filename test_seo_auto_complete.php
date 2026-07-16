<?php
/**
 * SEO自动补充功能测试脚本
 */

// 加载配置文件
require_once 'config/config.php';
require_once 'core/functions.php';

// 测试用例
echo "=== SEO自动补充功能测试 ===\n\n";

// 测试1: 短标题自动补充
echo "测试1: 短标题自动补充\n";
$shortTitle = "文章管理";
$result = auto_complete_title($shortTitle);
echo "原始标题: " . $shortTitle . " (长度: " . mb_strlen($shortTitle, 'UTF-8') . ")\n";
echo "补充后标题: " . $result . " (长度: " . mb_strlen($result, 'UTF-8') . ")\n\n";

// 测试2: 短描述自动补充
echo "测试2: 短描述自动补充\n";
$shortDesc = "文章管理页面";
$result = auto_complete_description($shortDesc);
echo "原始描述: " . $shortDesc . " (长度: " . mb_strlen($shortDesc, 'UTF-8') . ")\n";
echo "补充后描述: " . $result . " (长度: " . mb_strlen($result, 'UTF-8') . ")\n\n";

// 测试3: 空标题自动补充
echo "测试3: 空标题自动补充\n";
$emptyTitle = "";
$result = auto_complete_title($emptyTitle);
echo "原始标题: (空)\n";
echo "补充后标题: " . $result . " (长度: " . mb_strlen($result, 'UTF-8') . ")\n\n";

// 测试4: 空描述自动补充
echo "测试4: 空描述自动补充\n";
$emptyDesc = "";
$result = auto_complete_description($emptyDesc);
echo "原始描述: (空)\n";
echo "补充后描述: " . $result . " (长度: " . mb_strlen($result, 'UTF-8') . ")\n\n";

// 测试5: 已经满足条件的标题
echo "测试5: 已经满足条件的标题\n";
$longTitle = "这是一个已经满足15字要求的标题示例";
$result = auto_complete_title($longTitle);
echo "原始标题: " . $longTitle . " (长度: " . mb_strlen($longTitle, 'UTF-8') . ")\n";
echo "补充后标题: " . $result . " (长度: " . mb_strlen($result, 'UTF-8') . ")\n\n";

// 测试6: 已经满足条件的描述
echo "测试6: 已经满足条件的描述\n";
$longDesc = "这是一个已经满足150字要求的描述示例。这是一个已经满足150字要求的描述示例。这是一个已经满足150字要求的描述示例。这是一个已经满足150字要求的描述示例。这是一个已经满足150字要求的描述示例。这是一个已经满足150字要求的描述示例。这是一个已经满足150字要求的描述示例。这是一个已经满足150字要求的描述示例。这是一个已经满足150字要求的描述示例。这是一个已经满足150字要求的描述示例。";
$result = auto_complete_description($longDesc);
echo "原始描述: " . substr($longDesc, 0, 50) . "... (长度: " . mb_strlen($longDesc, 'UTF-8') . ")\n";
echo "补充后描述: " . substr($result, 0, 50) . "... (长度: " . mb_strlen($result, 'UTF-8') . ")\n\n";

// 测试7: 完整的SEO元数据生成
echo "测试7: 完整的SEO元数据生成\n";
$testTitle = "用户管理";
$testDesc = "用户管理功能";
$seoMeta = generate_seo_meta($testTitle, $testDesc);
echo "原始标题: " . $testTitle . " (长度: " . mb_strlen($testTitle, 'UTF-8') . ")\n";
echo "原始描述: " . $testDesc . " (长度: " . mb_strlen($testDesc, 'UTF-8') . ")\n";
echo "生成后标题: " . $seoMeta['title'] . " (长度: " . mb_strlen($seoMeta['title'], 'UTF-8') . ")\n";
echo "生成后描述: " . $seoMeta['description'] . " (长度: " . mb_strlen($seoMeta['description'], 'UTF-8') . ")\n\n";

// 测试8: 后台页面标题测试
echo "测试8: 后台页面标题测试\n";
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
    echo "后台页面: " . $title . " → " . $result . " (长度: " . mb_strlen($result, 'UTF-8') . ")\n";
}

echo "\n=== 测试完成 ===\n";
?>