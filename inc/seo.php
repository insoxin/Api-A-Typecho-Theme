<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * SEO 相关功能模块
 * 包含百度收录检查等SEO功能
 */

/**
 * Check if current page is indexed by Baidu
 * Display index status with submission link
 * 使用缓存减少对百度的频繁请求
 */
function baidu_record() {
    // Sanitize and validate server variables
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
    $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    
    if (empty($host) || empty($uri)) {
        return;
    }
    
    // 构建并验证URL
    $url = 'https://' . $host . $uri;
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return;
    }
    
    // 转义输出
    $host = htmlspecialchars($host, ENT_QUOTES, 'UTF-8');
    $uri = htmlspecialchars($uri, ENT_QUOTES, 'UTF-8');
    
    // 检查缓存
    $options = Typecho_Widget::widget('Widget_Options');
    $enableCache = isset($options->enableCache) && $options->enableCache == '1';
    
    if ($enableCache && class_exists('API_Cache')) {
        $cache_key = 'baidu_record_' . md5($url);
        $cached = API_Cache::get($cache_key);
        
        if ($cached !== false) {
            echo $cached;
            return;
        }
    }
    
    // 检查百度收录状态
    $indexed = checkBaidu($url);
    
    if ($indexed == 1) {
        $output = "百度已收录";
    } else {
        $escaped_url = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
        $output = '<a style="color:red;" rel="external nofollow" title="点击提交收录！" target="_blank" href="https://zhanzhang.baidu.com/sitesubmit/index?sitename=' . urlencode($url) . '">百度未收录</a>';
    }
    
    // 缓存结果（24小时）
    if ($enableCache && class_exists('API_Cache')) {
        API_Cache::set($cache_key, $output, 86400);
    }
    
    echo $output;
}

/**
 * Check if URL is indexed by Baidu search
 * @param string $url URL to check
 * @return int 1 if indexed, -1 if not
 */
function checkBaidu($url) {
    // Use HTTPS for security
    $search_url = 'https://www.baidu.com/s?wd=' . urlencode($url);
    
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $search_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10); // Add timeout
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true); // Verify SSL
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false); // Don't follow redirects for security
    
    $rs = curl_exec($curl);
    $error = curl_error($curl);
    curl_close($curl);
    
    // Handle curl errors
    if ($error || $rs === false) {
        return -1;
    }
    
    // Check if content indicates page is not indexed
    if (strpos($rs, '没有找到') === false) {
        return 1;
    } else {
        return -1;
    }
}
