<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * 缩略图功能模块
 * 处理文章缩略图显示，支持多种来源和缓存
 */

/**
 * 添加自定义字段（缩略图）
 * @param object $layout Layout 对象
 * @return void
 */
function themeFields($layout) {
    $thumb = new Typecho_Widget_Helper_Form_Element_Text(
        'thumb', 
        NULL, 
        NULL, 
        _t('自定义缩略图'), 
        _t('输入缩略图地址(仅文章有效)')
    );
    $layout->addItem($thumb);
}

/**
 * 显示文章缩略图
 * 支持多种来源：自定义字段、附件、内容图片、标签图片、随机图片
 * 使用缓存提升性能
 * @param object $widget Widget 对象
 * @return void
 */
function showThumbnail($widget) {
    $options = Typecho_Widget::widget('Widget_Options');
    $themeUrl = $widget->widget('Widget_Options')->themeUrl;
    
    // 检查缓存
    $enableCache = isset($options->enableCache) && $options->enableCache == '1';
    $cid = $widget->cid;
    
    if ($enableCache && class_exists('API_Cache') && $cid) {
        $cache_key = 'thumbnail_' . $cid;
        $cached = API_Cache::get($cache_key);
        
        if ($cached !== false) {
            echo $cached;
            return;
        }
    }
    
    // 获取缩略图URL
    $ctu = getThumbnailUrl($widget, $themeUrl, $options);
    
    // 应用懒加载
    $lazyLoad = isset($options->lazyLoad) && $options->lazyLoad == '1';
    $output = '';
    
    // 处理输出
    if ($options->slimg && $options->slimg == 'showoff') {
        // showoff 模式：无图则不显示
        $random = getRandomThumbnail($themeUrl, $options);
        if ($ctu != $random) {
            if ($widget->is('post') || $widget->is('page')) {
                $output = applyCdnUrl($ctu, $options);
            } else {
                $output = buildImgTag($ctu, $options, $lazyLoad);
            }
        }
    } else {
        // 其他模式
        if (!$widget->is('post') && !$widget->is('page')) {
            if ($options->slimg && $options->slimg == 'allsj') {
                $ctu = getRandomThumbnail($themeUrl, $options);
            }
        }
        $output = applyCdnUrl($ctu, $options);
    }
    
    // 缓存结果
    if ($enableCache && class_exists('API_Cache') && $cid) {
        API_Cache::set($cache_key, $output, 3600);
    }
    
    echo $output;
}

/**
 * 获取缩略图 URL
 * @param object $widget Widget 对象
 * @param string $themeUrl 主题URL
 * @param object $options 选项对象
 * @return string 缩略图URL
 */
function getThumbnailUrl($widget, $themeUrl, $options) {
    $random = getRandomThumbnail($themeUrl, $options);
    $cai = ''; // 图片后缀（如七牛裁剪参数）
    
    // 优先级1：自定义字段
    if ($widget->fields->thumb) {
        return $widget->fields->thumb;
    }
    
    // 优先级2：附件图片
    $attach = $widget->attachments(1)->attachment;
    if ($attach && $attach->isImage) {
        return $attach->url . $cai;
    }
    
    // 优先级3：内容中的图片
    $pattern = '/\<img.*?src\=\"(https?:\/\/[^\"]+)\"[^>]*>/i';
    $patternMD = '/\!\[.*?\]\((https?:\/\/[^\)]+\.(jpg|jpeg|png|gif|webp))/i';
    $patternMDfoot = '/\[.*?\]:\s*(https?:\/\/[^\s]+\.(jpg|jpeg|png|gif|webp))/i';
    
    if (preg_match($pattern, $widget->content, $thumbUrl)) {
        return $thumbUrl[1] . $cai;
    } elseif (preg_match($patternMD, $widget->content, $thumbUrl)) {
        return $thumbUrl[1] . $cai;
    } elseif (preg_match($patternMDfoot, $widget->content, $thumbUrl)) {
        return $thumbUrl[1] . $cai;
    }
    
    // 优先级4：标签图片
    if ($widget->tags) {
        foreach ($widget->tags as $tag) {
            // 清理并验证slug，防止目录遍历
            $slug = preg_replace('/[^a-zA-Z0-9_-]/', '', $tag['slug']);
            if (empty($slug)) {
                continue;
            }
            
            $tagImagePath = './usr/themes/api/img/tag/' . $slug . '.jpg';
            if (is_file($tagImagePath)) {
                return $themeUrl . '/img/tag/' . $slug . '.jpg';
            }
        }
    }
    
    // 优先级5：随机图片
    return $random;
}

/**
 * 获取随机缩略图
 * @param string $themeUrl 主题URL
 * @param object $options 选项对象
 * @return string 随机缩略图URL
 */
function getRandomThumbnail($themeUrl, $options) {
    $dir = './usr/themes/api/img/sj/';
    $n = sizeof(scandir($dir)) - 2;
    
    if ($n <= 0) {
        $n = 99;
    }
    
    $rand = rand(1, $n);
    
    // Showimg 模式使用固定图片
    if ($options->slimg && $options->slimg == 'Showimg') {
        return $themeUrl . '/img/mr.png';
    }
    
    return $themeUrl . '/img/sj/' . $rand . '.jpg';
}

/**
 * 应用 CDN 加速
 * @param string $url 原始URL
 * @param object $options 选项对象
 * @return string 处理后的URL
 */
function applyCdnUrl($url, $options) {
    if (isset($options->cdnUrl) && !empty($options->cdnUrl)) {
        $cdnUrl = rtrim($options->cdnUrl, '/');
        $themeUrl = $options->themeUrl;
        
        // 只替换主题资源URL
        if (strpos($url, $themeUrl) === 0) {
            $url = str_replace($themeUrl, $cdnUrl, $url);
        }
    }
    
    return htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
}

/**
 * 构建图片标签
 * @param string $src 图片URL
 * @param object $options 选项对象
 * @param bool $lazyLoad 是否懒加载
 * @param string $alt 图片描述
 * @return string HTML 图片标签
 */
function buildImgTag($src, $options, $lazyLoad = false, $alt = '') {
    $src = applyCdnUrl($src, $options);
    $alt = !empty($alt) ? htmlspecialchars($alt, ENT_QUOTES, 'UTF-8') : '文章缩略图';
    
    if ($lazyLoad) {
        // 懒加载：使用 data-src 和占位符
        return '<img class="lazyload" src="data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 1 1\'%3E%3C/svg%3E" data-src="' . $src . '" alt="' . $alt . '">';
    } else {
        return '<img src="' . $src . '" alt="' . $alt . '">';
    }
}
