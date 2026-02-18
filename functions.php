<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * API 主题函数加载器
 * 采用模块化设计，将功能分散到不同的文件中
 * 
 * @package API Theme
 * @author 姬长信
 * @version 2.0
 * @link https://blog.isoyu.com/
 */

// 定义主题目录常量
define('API_THEME_DIR', dirname(__FILE__));
define('API_THEME_INC', API_THEME_DIR . '/inc');

/**
 * 加载模块文件
 * @param string $module 模块名称
 */
function api_load_module($module) {
    $file = API_THEME_INC . '/' . $module . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
}

// 按顺序加载所有模块
$modules = array(
    'cache',        // 缓存系统（必须最先加载）
    'config',       // 主题配置
    'seo',          // SEO 功能
    'statistics',   // 统计功能
    'thumbnail',    // 缩略图功能
    'navigation',   // 导航功能
    'comments',     // 评论功能
    'content'       // 内容处理
);

foreach ($modules as $module) {
    api_load_module($module);
}

/**
 * 主题激活时的操作
 */
function themeInit() {
    // 初始化缓存目录
    if (class_exists('API_Cache')) {
        API_Cache::init();
    }
}

// 执行初始化
themeInit();
