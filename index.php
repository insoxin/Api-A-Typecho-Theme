<?php
/**
 * API主题是为了重推https://api.isoyu.com/而产生的一款简洁css3大气时尚摄影杂志响应式typecho模板她的一大亮点就是调用//api.isoyu.com/mm_images.php随机妹子生活照API
 *
 * @package API Theme
 * @author 姬长信
 * @version 1.0
 * @link https://blog.isoyu.com/
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
    
?>
<?php if($this->options->slimg && 'guanbi'==$this->options->slimg): ?>
<?php else: ?>
<?php if($this->options->slimg && 'showoff'==$this->options->slimg): ?><a href="<?php $this->permalink() ?>" ><?php showThumbnail($this); ?></a>
<?php else: ?><figure>
<img src="<?php showThumbnail($this); ?>"  alt="Image" class="img-responsive"></figure>
        <?php endif; ?>
        <?php endif; ?>
<div class="copyrights">Collect from <a href="<?php $this->options->siteUrl(); ?>" ><?php $this->options->title() ?></a></div>
<div class="container-fluid">
		<div class="row fh5co-post-entry">
<?php while($this->next()): ?>
    <div class="post">
			<article class="col-lg-3 col-md-3 col-sm-3 col-xs-6 col-xxs-12 animate-box">
				<span class="fh5co-meta"><?php $this->category(','); ?></span>
				<h2 class="fh5co-article-title"><a href="<?php $this->permalink() ?>"><?php $this->title() ?></a></h2>
				<span class="fh5co-meta fh5co-date">On <?php $this->date('F-j '); ?> read(<?php get_post_view($this) ?>)<?php echo art_count($this->cid); ?>汉字</span>
  </div>
<?php endwhile; ?>
<div class="lists-navigator clearfix clearfix">
    <?php $this->pageNav('←','→','2','...'); ?>
  </div>
</div>
<?php $this->need('footer.php'); ?>