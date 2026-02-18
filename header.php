<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
	<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php $this->archiveTitle(array(

    'category'  =>  _t(' %s '),

    'search'    =>  _t(' %s '),

    'tag'       =>  _t(' %s '),

    'author'    =>  _t(' %s '),

    'date'      =>  _t(' %s ')

    ), '', ' - '); ?><?php $this->options->title(); ?></title>
<meta name="viewport" content="width=device-width">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta name="description" content="<?php $this->options->description() ?>" />
	<meta name="keywords" content="<?php $this->keywords(); ?>" />
	<!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
	<link rel="shortcut icon" href="favicon.ico">
	<!-- Google Fonts -->
	<link href='https://fonts.googleapis.com/css?family=Playfair+Display:400,700,400italic|Roboto:400,300,700' rel='stylesheet' type='text/css'>
	<!-- Animate -->
	<link rel="stylesheet" href="<?php $this->options->themeUrl('css/animate.css'); ?>">
	<!-- Icomoon -->
	<link rel="stylesheet" href="<?php $this->options->themeUrl('css/icomoon.css'); ?>">
	<!-- Bootstrap  -->
	<link rel="stylesheet" href="<?php $this->options->themeUrl('css/bootstrap.css'); ?>">

	<link rel="stylesheet" href="<?php $this->options->themeUrl('css/style.css'); ?>">
	
	<!-- Comments -->
	<link rel="stylesheet" href="<?php $this->options->themeUrl('css/comments.css'); ?>">


	<!-- Modernizr JS -->
	<script src="<?php $this->options->themeUrl('js/modernizr-2.6.2.min.js'); ?>"></script>
	<!-- FOR IE9 below -->
	<!--[if lt IE 9]>
	<script src="<?php $this->options->themeUrl('js/respond.min.js'); ?>"></script>
	<![endif]-->

	</head>
<body>
	<div id="fh5co-offcanvas">
		<a href="#" class="fh5co-close-offcanvas js-fh5co-close-offcanvas"><span><i class="icon-cross3"></i> <span>Close</span></span></a>
		<div class="fh5co-bio">
			<figure>
				<img src="<?php $this->options->tt(); ?>" alt="<?php $this->options->title() ?>" class="img-responsive">
			</figure>
			<h3 class="heading">About Me</h3>
			<h2><?php $this->options->title() ?></h2>
			<p><?php $this->options->description() ?></p>
			<ul class="fh5co-social">
				<li><a href="https://mail.qq.com/cgi-bin/qm_share?t=qm_mailme&email=<?php $this->author('mail'); ?>"><i class="icon-email"></i></a></li>
				<li><a href="tencent://AddContact/?fromId=45&fromSubId=1&subcmd=all&uin=<?php $this->options->qq(); ?>&website=<?php echo $SITE['title'];?>"><i class="icon-qq"></i></a></li>

<li><a href="<?php $this->options->weiboz(); ?>/<?php $this->options->weibo(); ?>"><i class="icon-sina-weibo"></i></a></li>
				<li><a href="<?php $this->options->feedUrl(); ?>"><i class="icon-rss"></i></a></li>
			</ul>
		</div>

		<div class="fh5co-menu">
			<div class="fh5co-box">
				<h3 class="heading">Categories</h3>
				<ul>
    <?php $this->widget('Widget_Metas_Category_List')
               ->parse('<li><a href="{permalink}">{name}</a> ({count})</li>'); ?>
</ul>
			</div>
			<div class="fh5co-box">
				<h3 class="heading">Search</h3>
				<form method="post">
					<div class="form-group">
						<input type="text"name="s"  class="form-control" placeholder="Type a keyword">
					</div>
				</form>
<?php $this->options->tongji(); ?>
			</div>
		</div>
	</div>
	<!-- END #fh5co-offcanvas -->
	<header id="fh5co-header">
		
		<div class="container-fluid">

			<div class="row">
				<a href="#" class="js-fh5co-nav-toggle fh5co-nav-toggle"><i></i></a>
				<ul class="fh5co-social">
					<li><a href="https://mail.qq.com/cgi-bin/qm_share?t=qm_mailme&email=<?php $this->author('mail'); ?>"><i class="icon-email"></i></a></li>
				<li><a href="tencent://AddContact/?fromId=45&fromSubId=1&subcmd=all&uin=<?php $this->options->qq(); ?>&website=<?php echo $SITE['title'];?>"><i class="icon-qq"></i></a></li>

<li><a href="<?php $this->options->weiboz(); ?>/<?php $this->options->weibo(); ?>"><i class="icon-sina-weibo"></i></a></li>
				<li><a href="<?php $this->options->feedUrl(); ?>"><i class="icon-rss"></i></a></li>
				</ul>
				<div class="col-lg-12 col-md-12 text-center">
					<h1 id="fh5co-logo"><a href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title() ?> <sup>TM</sup></a></h1>
				</div>

			</div>
		
		</div>

	</header>