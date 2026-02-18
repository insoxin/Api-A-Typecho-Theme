<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

function themeConfig($form) {


    //博主qq
    $qq = new Typecho_Widget_Helper_Form_Element_Text('qq', NULL, '192666378', _t('博主QQ'), _t('在这里填入博主的QQ号码如：192666378'));
    $form->addInput($qq);
  //博主微博
    $weiboz = new Typecho_Widget_Helper_Form_Element_Text('weiboz', NULL, '//weibo.com', _t('博主所属微博'), _t('在这里填入博主所属微博，新浪填写//weibo.com腾讯微博就填http://t.qq.com等，如我的微博地址是：//weibo.com/3461828744那么就填写//weibo.com'));
    $form->addInput($weiboz);
   //博主微博
    $weibo = new Typecho_Widget_Helper_Form_Element_Text('weibo', NULL, '3461828744', _t('博主微博'), _t('在这里填入博主的微博地址，如我的微博地址是：//weibo.com/3461828744那么就填写3461828744'));
    $form->addInput($weibo);
//头像界面展示图
    $tt = new Typecho_Widget_Helper_Form_Element_Text('tt', NULL, 'https://api.isoyu.com/mm_images.php', _t('关于界面展示图
'), _t(' 填入图片地址，建议图片高度158px'));
    $form->addInput($tt);
/** 输出文章缩略图 */
$slimg = new Typecho_Widget_Helper_Form_Element_Select('slimg', array(
        'showon'=>'有图文章显示缩略图，无图文章随机显示缩略图',
        'Showimg' => '有图文章显示缩略图，无图文章只显示一张固定的缩略图',      
        'showoff' => '有图文章显示缩略图，无图文章则不显示缩略图',
        'allsj' => '所有文章一律显示随机缩略图',
        'guanbi' => '关闭所有缩略图显示'
    ), 'guanbi',
    _t('缩略图设置'), _t('默认选择“关闭所有缩略图显示”'));
    $form->addInput($slimg->multiMode());
   //统计代码
$tongji = new Typecho_Widget_Helper_Form_Element_Textarea('tongji', NULL, '统计代码', _t('统计代码'), _t(''));
$form->addInput($tongji);

}


/**
 * Check if current page is indexed by Baidu
 * Display index status with submission link
 */
function baidu_record() {
    // Sanitize and validate server variables
    $host = isset($_SERVER['HTTP_HOST']) ? htmlspecialchars($_SERVER['HTTP_HOST'], ENT_QUOTES, 'UTF-8') : '';
    $uri = isset($_SERVER['REQUEST_URI']) ? htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8') : '';
    
    if (empty($host) || empty($uri)) {
        return;
    }
    
    $url = 'https://' . $host . $uri;
    
    if (checkBaidu($url) == 1) {
        echo "百度已收录";
    } else {
        // Use HTTPS and properly escape URL
        $escaped_url = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
        echo '<a style="color:red;" rel="external nofollow" title="点击提交收录！" target="_blank" href="https://zhanzhang.baidu.com/sitesubmit/index?sitename=' . urlencode($url) . '">百度未收录</a>';
    }
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
/**
 * Count and display article character count
 * @param int $cid Content ID
 * @return void
 */
function art_count($cid) {
    $db = Typecho_Db::get();
    $rs = $db->fetchRow(
        $db->select('table.contents.text')
           ->from('table.contents')
           ->where('table.contents.cid = ?', $cid)
           ->limit(1)
    );
    
    if ($rs && isset($rs['text'])) {
        echo mb_strlen($rs['text'], 'UTF-8');
    } else {
        echo '0';
    }
}
/**
 * Get and update post view count
 * Automatically creates views column if it doesn't exist
 * @param object $archive Archive widget object
 * @return void
 */
function get_post_view($archive) {
    $cid = $archive->cid;
    $db = Typecho_Db::get();
    $prefix = $db->getPrefix();
    
    // Check if views column exists
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
    
    // Increment view count only on single post pages
    if ($archive->is('single')) {
        $views = (int) $row['views'] + 1;
        $db->query($db->update('table.contents')->rows(array('views' => $views))->where('cid = ?', $cid));
        echo $views;
    } else {
        echo (int) $row['views'];
    }
}
/**
 * Add custom fields to post editor
 * @param object $layout Layout object
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
 * Display post thumbnail image
 * Supports multiple sources: custom field, attachments, content images, tag-based, and random
 * @param object $widget Widget object
 * @return void
 */
function showThumbnail($widget) {
    // Get theme options
    $options = Typecho_Widget::widget('Widget_Options');
    $themeUrl = $widget->widget('Widget_Options')->themeUrl;
    
    // Random thumbnail directory
    $dir = './usr/themes/api/img/sj/';
    $n = sizeof(scandir($dir)) - 2;
    
    // Fallback if directory scan fails
    if ($n <= 0) {
        $n = 99;
    }
    
    $rand = rand(1, $n);
    $random = $themeUrl . '/img/sj/' . $rand . '.jpg';
    
    // Use fixed image if 'Showimg' mode is selected
    if ($options->slimg && $options->slimg == 'Showimg') {
        $random = $themeUrl . '/img/mr.png';
    }
    
    $cai = ''; // Image suffix for CDN processing (e.g., Qiniu thumbnail rules)
    $ctu = $random; // Default thumbnail URL
    
    // Priority 1: Custom thumbnail field
    if ($widget->fields->thumb) {
        $ctu = $widget->fields->thumb;
    }
    // Priority 2: First attachment if it's an image
    else {
        $attach = $widget->attachments(1)->attachment;
        if ($attach && $attach->isImage) {
            $ctu = $attach->url . $cai;
        }
        // Priority 3: Extract image from content
        else {
            $pattern = '/\<img.*?src\=\"(.*?)\"[^>]*>/i';
            $patternMD = '/\!\[.*?\]\((http(s)?:\/\/.*?(jpg|png))/i';
            $patternMDfoot = '/\[.*?\]:\s*(http(s)?:\/\/.*?(jpg|png))/i';
            
            // Try HTML img tag
            if (preg_match($pattern, $widget->content, $thumbUrl)) {
                $ctu = $thumbUrl[1] . $cai;
            }
            // Try inline Markdown image
            elseif (preg_match($patternMD, $widget->content, $thumbUrl)) {
                $ctu = $thumbUrl[1] . $cai;
            }
            // Try reference-style Markdown image
            elseif (preg_match($patternMDfoot, $widget->content, $thumbUrl)) {
                $ctu = $thumbUrl[1] . $cai;
            }
            // Priority 4: Tag-based thumbnail
            elseif ($widget->tags) {
                foreach ($widget->tags as $tag) {
                    $tagImagePath = './usr/themes/api/img/tag/' . $tag['slug'] . '.jpg';
                    if (is_file($tagImagePath)) {
                        $ctu = $themeUrl . '/img/tag/' . $tag['slug'] . '.jpg';
                        break;
                    }
                }
            }
        }
    }
    
    // Handle 'showoff' mode - only show if not random
    if ($options->slimg && $options->slimg == 'showoff') {
        if ($ctu == $random) {
            echo '';
        } else {
            if ($widget->is('post') || $widget->is('page')) {
                echo $ctu;
            } else {
                echo '<img src="' . htmlspecialchars($ctu, ENT_QUOTES, 'UTF-8') . '">';
            }
        }
    } else {
        // Force random for 'allsj' mode on non-post/page views
        if (!$widget->is('post') && !$widget->is('page')) {
            if ($options->slimg && $options->slimg == 'allsj') {
                $ctu = $random;
            }
        }
        echo htmlspecialchars($ctu, ENT_QUOTES, 'UTF-8');
    }
}
/**
 * Parse and modify content before display
 * Replaces CDN URLs and adds target="_blank" to links
 * @param object $obj Content object
 * @return void
 */
function parseContent($obj) {
    $options = Typecho_Widget::widget('Widget_Options');
    
    // Replace source URLs with CDN URLs if configured
    if (!empty($options->src_add) && !empty($options->cdn_add)) {
        $obj->content = str_ireplace($options->src_add, $options->cdn_add, $obj->content);
    }
    
    // Add target="_blank" to all links
    $obj->content = preg_replace("/<a href=\"([^\"]*)\">/i", "<a href=\"\\1\" target=\"_blank\">", $obj->content);
    
    echo trim($obj->content);
}
/**
 * Display next post link
 * @param object $widget Widget object
 * @param string $default Default text if no next post
 * @return void
 */
function theNext($widget, $default = NULL) {
    $db = Typecho_Db::get();
    $sql = $db->select()->from('table.contents')
        ->where('table.contents.created > ?', $widget->created)
        ->where('table.contents.status = ?', 'publish')
        ->where('table.contents.type = ?', $widget->type)
        ->where('table.contents.password IS NULL')
        ->order('table.contents.created', Typecho_Db::SORT_ASC)
        ->limit(1);
    
    $content = $db->fetchRow($sql);
    
    if ($content) {
        $content = $widget->filter($content);
        $title = htmlspecialchars($content['title'], ENT_QUOTES, 'UTF-8');
        $link = '<a href="' . $content['permalink'] . '" title="' . $title . '">←</a>';
        echo $link;
    } else {
        echo $default;
    }
}

/**
 * Display previous post link
 * @param object $widget Widget object
 * @param string $default Default text if no previous post
 * @return void
 */
function thePrev($widget, $default = NULL) {
    $db = Typecho_Db::get();
    $sql = $db->select()->from('table.contents')
        ->where('table.contents.created < ?', $widget->created)
        ->where('table.contents.status = ?', 'publish')
        ->where('table.contents.type = ?', $widget->type)
        ->where('table.contents.password IS NULL')
        ->order('table.contents.created', Typecho_Db::SORT_DESC)
        ->limit(1);
    
    $content = $db->fetchRow($sql);
    
    if ($content) {
        $content = $widget->filter($content);
        $title = htmlspecialchars($content['title'], ENT_QUOTES, 'UTF-8');
        $link = '<a href="' . $content['permalink'] . '" title="' . $title . '">→</a>';
        echo $link;
    } else {
        echo $default;
    }
}

/**
 * Custom comment list display
 * @param object $comments Comment object
 * @param object $options Options object
 * @return void
 */
function threadedComments($comments, $options) {
    $commentClass = '';
    if ($comments->authorId) {
        if ($comments->authorId == $comments->ownerId) {
            $commentClass .= ' comment-by-author';
        } else {
            $commentClass .= ' comment-by-user';
        }
    }

    $commentLevelClass = $comments->levels > 0 ? ' comment-child' : ' comment-parent';
?>

<li id="li-<?php $comments->theId(); ?>" class="comment-body<?php 
if ($comments->levels > 0) {
    echo ' comment-child';
    $comments->levelsAlt(' comment-level-odd', ' comment-level-even');
} else {
    echo ' comment-parent';
}
$comments->alt(' comment-odd', ' comment-even');
echo $commentClass;
?>">
    <div id="<?php $comments->theId(); ?>">
        <div class="comment-author">
            <?php
            // Avatar CDN configuration
            $host = 'https://secure.gravatar.com';
            $url = '/avatar/';
            $size = '32';
            $rating = Helper::options()->commentsAvatarRating;
            $hash = md5(strtolower($comments->mail));
            $avatar = $host . $url . $hash . '?s=' . $size . '&r=' . $rating . '&d=';
            $authorName = htmlspecialchars($comments->author, ENT_QUOTES, 'UTF-8');
            ?>
            <img class="avatar" src="<?php echo $avatar ?>" alt="<?php echo $authorName; ?>" width="<?php echo $size ?>" height="<?php echo $size ?>" />
            <cite class="fn"><?php $comments->author(); ?></cite>
        </div>
        <div class="comment-meta">
            <a href="<?php $comments->permalink(); ?>"><?php $comments->date('Y-m-d H:i'); ?></a>
            <span class="comment-reply"><?php $comments->reply(); ?></span>
        </div>
        <?php $comments->content(); ?>
    </div>
<?php if ($comments->children) { ?>
    <div class="comment-children">
        <?php $comments->threadedComments($options); ?>
    </div>
<?php } ?>
</li>
<?php }