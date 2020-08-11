<div id="search_input">
		<div>
			<a href="<?php echo site_url('admin')?>" class="logo"></a>
		    <form id="myForm" action="<?php echo site_url('admin/search')?>" method="get">
			    <input type="text" value="<?php echo $keyword?>" name="keyword" class="input-xlarge" style="margin-bottom:0;">
			    <input type="submit" value="搜索" class="btn btn-success" />
			</form>
		</div>
		<div>
			<select id="cat_select" name="cat_select" style="margin-bottom:0;">
			<option value="0">全部</option>
			<?php
			foreach($cat->result() as $row){
			   echo '<option value="'.$row->cat_id.'">'.$row->cat_name.'</option>';
			}
			?>
			</select>
			<span class="muted">（点击商品后会自动添加到该分类）</span>
		</div>

</div><!-- .search_input -->

<!-- 搜索结果列表 -->
<ul id='search-list'>
<?php
	if(empty($resp['total_count'])){
		echo '没有找到条目，请修改关键词或者类别。';
	} else{
		foreach($resp['goods_list'] as $taobaoke_item){
		?>
			<li>
				<a href='#' data-num_id='<?php echo $taobaoke_item['goods_id'] ?>' data-search_id='<?php echo $taobaoke_item['search_id'] ?>'
				title='<?php echo htmlspecialchars(strip_tags($taobaoke_item['goods_name']),ENT_QUOTES); ?>' data-price='<?php echo $taobaoke_item['min_normal_price']?>' data-group_price='<?php echo $taobaoke_item['min_group_price']?>'
				data-sellernick='<?php echo htmlspecialchars($taobaoke_item['mall_name'],ENT_QUOTES); ?>'>
				<img src="<?php echo $taobaoke_item['goods_thumbnail_url'] ?>" alt="<?php echo htmlspecialchars(strip_tags($taobaoke_item['goods_name']),ENT_QUOTES)?>"/>
				</a>
				<p>
                    <span class="right"><?php echo $taobaoke_item['sales_tip'] ?>已售</span>
                </P>
                <P>
                    <span>预估佣金：<?php echo round($taobaoke_item['promotion_rate'] / 1000 * $taobaoke_item['min_group_price'] / 100, 2); ?> 元</span>
                </p>
                <p>
                    <span>拼团价/单买价：<?php echo $taobaoke_item['min_group_price'] / 100 ?> 元 / <?php echo $taobaoke_item['min_normal_price'] / 100?> 元</span>
                </p>
			</li>
		<?php
		}
	}
?>
</ul>


<script type='text/javascript' src='<?php echo base_url()?>assets/js/jquery.js'></script>
<script type="text/javascript">
(function($) {
	var global_clickurl,global_title,global_price,global_nick;
		//搜索结果中的条目点击
	$('#search-list li a').click(
			function(event){
				event.preventDefault();
				var item = {},
					thisItem = $(this),
					successMessage = '<div class="alert alert-success">添加成功！</div>';

				item.img_url = thisItem.find('img').attr('src');
				item.sellernick = thisItem.data('sellernick');
				item.title = htmlEncode(thisItem.attr('title'));
				item.price = thisItem.data('price');
				item.group_price = thisItem.data('group_price');
				item.cid = $('#cat_select').val();
				item.num_iid = thisItem.data('num_id');
				item.search_id = thisItem.data('search_id');

				$.post('<?php echo site_url("admin/setitem/")?>',
						   { img_url: item.img_url,
							title: item.title,
							cid: item.cid,
							sellernick: item.sellernick,
                            search_id: item.search_id,
							price: item.price,
                            group_price: item.group_price,
							num_iid: item.num_iid
						   },
						   function(data) {
						   	
						   }).success(function(body){
							 if(body == '1'){
							 	thisItem.addClass('success').append(successMessage);
							 }else{
							 	alert(body);
							 }
						   }).error(function(body){
						   	alert('添加失败'+body);
						   });

					event.preventDefault();
		}
	);

	function htmlEncode(value){
	  return $('<div/>').text(value).html();
	}

	function htmlDecode(value){
	  return $('<div/>').html(value).text();
	}

})(jQuery);
</script>
</body>
<html>