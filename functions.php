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
 * @return bool 是否加载成功
 */
function api_load_module($module) {
    $file = API_THEME_INC . '/' . $module . '.php';
    if (file_exists($file)) {
        require_once $file;
        return true;
    }
    return false;
}

// 按顺序加载所有模块（cache必须最先加载）
$modules = array(
    'cache',        // 缓存系统（核心模块，其他模块依赖）
    'config',       // 主题配置
    'seo',          // SEO 功能
    'statistics',   // 统计功能（依赖cache）
    'thumbnail',    // 缩略图功能（依赖cache）
    'navigation',   // 导航功能
    'comments',     // 评论功能
    'content'       // 内容处理
);

$failed_modules = array();
foreach ($modules as $module) {
    if (!api_load_module($module)) {
        $failed_modules[] = $module;
    }
}

// 如果有关键模块加载失败，记录错误
if (!empty($failed_modules) && in_array('cache', $failed_modules)) {
    error_log('API Theme: Failed to load critical modules: ' . implode(', ', $failed_modules));
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
