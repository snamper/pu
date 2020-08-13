<!DOCTYPE html>
<html dir="ltr" lang="zh-CN">
<head>
<meta charset="UTF-8" />
	<title><?php
	if(!empty($cat_name)){
		echo $cat_name.' - ';
	}
	 echo $site_name;
	 ?></title>
	<meta name="keywords" content="<?php 
	if(!empty($cat_name)){
		echo $cat_name.',';
	}
		echo $site_keyword; ?>">
	<meta name="description" content="<?php echo $site_description; ?>">
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo base_url()?>assets/bootstrap.css" />
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo base_url()?>assets/index.css?d=20120705" />
	<!--[if lt IE 9]>
	<script src="<?php echo base_url()?>assets/js/html5shiv.js"></script>
	<![endif]-->
</head>
<body>

<header id="branding" role="banner">
    <div id="site-title">
        <h1>
            <a href="<?php echo site_url();?>" title="<?php echo $site_name;?>" rel="home" class="logo"><?php echo $site_name;?></a>
        </h1>
		<div id="site-op">
			<form action="<?php echo site_url('home/search');?>">
				<div class="input-append">
				  <input class="span2" id="appendedInputButton" type="text" name="keyword">
				  <input class="btn" type="submit" value="搜索">
				</div>
				<div class="keyword-list">
				<?php
      			   foreach($keyword_list->result() as $row){
	                echo '<a href="'.site_url('home/search?keyword='.$row->keyword_name).'">'.$row->keyword_name.'</a>&nbsp;&nbsp;';
      				}
	  			 ?>
				</div>
			</form>
		</div>
    </div>

</header>

<nav class="main_nav">
			<div>
				<ul class="menu">
					<?php
						$is_home = '';
						if(empty($cat_slug)){
							$is_home = 'current-menu-item';
						}
						?>
					<li class="<?php echo $is_home;?>"><a href="<?php echo site_url()?>">全部</a></li>
					<?php
					   foreach($cat->result() as $row){
							$is_current = '';
							if(!empty($cat_slug) && $row->cat_slug == $cat_slug){
								$is_current = 'current-menu-item';
							}
						   echo '<li class="'.$is_current.'"><a href="'.site_url('cat/'.rawurlencode($row->cat_slug)).'">'.$row->cat_name.'</a></li>';
						}
					 ?>
				</ul>
			</div>
	</nav>

<div id="wrapper">

	<?php if($items->num_rows()>0){ ?>
	<div class="goods-all transitions-enabled masonry">
	<?php foreach ($items->result() as $array):
	//条目
		?>

		<article class="goods">
			<div class="entry-content">
			<div class="goods-pic">
				<img src="<?php echo $array->img_url ?>" class="" alt="" title="<?php echo $array->title ?>">

			</div>
				<div class="op"><div class="desc"><?php echo $array->sellernick ?>   / <strong>RMB<?php echo $array->price/100 ?></strong></div>
				<div class="buttonline">
<!--					<a href="--><?php //echo site_url('home/detail').'/'.$array->id ?><!--" title="去购买" class="btn btn-success" target="_blank">去购买</a>-->
					<a href="javascript:;" onclick="goods_detail('<?php echo $array->id ?>')" title="去购买" class="btn btn-success">去购买</a>
				</div></div>
			</div>
		</article>
	<?php endforeach;?>
	</div>
        <div class="pagenav_wrapper">
            <div class="pagenav">
            		<?=$pagination;?>
            	</div>
        </div><!-- .pagenav_wrapper -->

    	<?php } ?>
</div>

<footer id="ft" class="main-footer" role="contentinfo">
		<p><a href="<?php echo site_url();?>" title="<?php echo $site_name;?>"><?php echo $site_name;?></a> ©   • Powered by <a href="https://github.com/yuguo/33pu" title="Powered by 33号铺, 一个开源的购物推荐系统">33号铺</a></p>
</footer>


<script type='text/javascript' src="<?php echo base_url()?>assets/js/jquery.js"></script>
<script type='text/javascript' src="<?php echo base_url()?>assets/js/layer/layer.js"></script>

<script>
    //点击产品，显示出产品相信信息
    function goods_detail(goods_id)
    {
        //进行二维码的请求
        layer.open({
            type: 2,
            title: false,
            area: ['284px', '284px'],
            shade: 0.8,
            closeBtn: 0,
            shadeClose: true,
            content: '<?php echo site_url("home/qrcode")?>/'+goods_id,
        });
    }

</script>

</body>
</html>
