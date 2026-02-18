<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * 统计功能模块
 * 包含文章字数统计、阅读量统计等
 */

/**
 * 统计文章字数并显示
 * @param int $cid 文章ID
 * @return void
 */
function art_count($cid) {
    // 检查缓存
    $options = Typecho_Widget::widget('Widget_Options');
    $enableCache = isset($options->enableCache) && $options->enableCache == '1';
    
    if ($enableCache && class_exists('API_Cache')) {
        $cache_key = 'art_count_' . $cid;
        $cached = API_Cache::get($cache_key);
        
        if ($cached !== false) {
            echo $cached;
            return;
        }
    }
    
    $db = Typecho_Db::get();
    $rs = $db->fetchRow(
        $db->select('table.contents.text')
           ->from('table.contents')
           ->where('table.contents.cid = ?', $cid)
           ->limit(1)
    );
    
    $count = 0;
    if ($rs && isset($rs['text'])) {
        $count = mb_strlen($rs['text'], 'UTF-8');
    }
    
    // 缓存结果（永久，因为文章内容不常变）
    if ($enableCache && class_exists('API_Cache')) {
        API_Cache::set($cache_key, $count, 0);
    }
    
    echo $count;
}

/**
 * 获取并更新文章浏览量
 * 如果 views 列不存在会自动创建
 * 使用缓存减少数据库写入
 * @param object $archive Archive widget 对象
 * @return void
 */
function get_post_view($archive) {
    $cid = $archive->cid;
    $db = Typecho_Db::get();
    $prefix = $db->getPrefix();
    
    // 检查 views 列是否存在
    if (!array_key_exists('views', $db->fetchRow($db->select()->from('table.contents')))) {
        $db->query('ALTER TABLE `' . $prefix . 'contents` ADD `views` INT(10) DEFAULT 0;');
        echo 0;
        return;
    }
    
    $row = $db->fetchRow($db->select('views')->from('table.contents')->where('cid = ?', $cid));
    
    if (!$row) {
        echo 0;
        return;
    }
    
    $views = (int) $row['views'];
    
    // 只在单篇文章页面增加浏览量
    if ($archive->is('single')) {
        // 使用缓存机制减少数据库写入频率
        $options = Typecho_Widget::widget('Widget_Options');
        $enableCache = isset($options->enableCache) && $options->enableCache == '1';
        
        if ($enableCache && class_exists('API_Cache')) {
            $cache_key = 'post_view_updated_' . $cid;
            $updated = API_Cache::get($cache_key);
            
            // 如果缓存中没有记录，说明需要更新数据库
            if ($updated === false) {
                $views++;
                $db->query($db->update('table.contents')->rows(array('views' => $views))->where('cid = ?', $cid));
                
                // 设置缓存，5分钟内不再更新数据库（减少写入）
                API_Cache::set($cache_key, true, 300);
            }
        } else {
            // 没有启用缓存，直接更新
            $views++;
            $db->query($db->update('table.contents')->rows(array('views' => $views))->where('cid = ?', $cid));
        }
        
        echo $views;
    } else {
        echo $views;
    }
}
