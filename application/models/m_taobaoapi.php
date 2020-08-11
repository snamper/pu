<?php

/**
 * 拼多多API接口
 * Class M_pddapi
 */

class M_taobaoapi extends CI_Model{

    private $pdd_config;

	function __construct()
	{
		parent::__construct();
		$this->config->load('site_info');
        //拼多多的配置
        $this -> pdd_config = [
            'key'           => $this->config->item('pdd_client_id'),
            'secret'        => $this->config->item('pdd_client_secret'),
            'debug'         => true,
        ];

        define('PU_HTTP_PROXY',    $this->config->item('http_proxy'));
	}


    /**
     * 搜索条目
     *
     * @param string $keyword  搜索关键词
     * @param integer $cid  淘宝的后台类目ID
     * @return String $resp XML字符串
     */
    function searchItem($keyword, $cid){
        //实例化相关的数据
        $client = new \Com\Pdd\Pop\Sdk\PopHttpClient($this -> pdd_config['key'], $this -> pdd_config['secret']);
        //设置相关的参数
        $request = new \Com\Pdd\Pop\Sdk\Api\Request\PddDdkGoodsSearchRequest();

        //设置相关的关键字
        $request->setKeyword($keyword);
        $request->setIsBrandGoods(true);
        //$request->setWithCoupon(true);
        //商品ID列表
        $request->setActivityTags(array(7,21));
        $request->setPage(1);
        $request->setPageSize(10);
        $request->setSortType(0);

        try{
            $response = $client->syncInvoke($request);
        } catch(Com\Pdd\Pop\Sdk\PopHttpException $e){
            echo $e->getMessage();
            exit;
        }

        $resp = $response->getContent();
        if (empty($resp['goods_search_response']['goods_list'])) {
            return $resp['goods_search_response']['total_count'] = 0;
        }
    	//执行API请求并打印结果
    	return $resp['goods_search_response'];
    }

    /**
     * 根据条目ID获取更详细的信息，包括图片列表
     *
     * @param integer $item_id  条目ID
     * @return string $resp 包含图片列表的XML
     */
    function getItemInfo($item_id){
        if($item_id == ''){
            return '';
        }else{
            $c = new TopClient;
            $c->appkey = APPKEY;
            $c->secretKey = SECRETKEY;
            $req = new ItemGetRequest;
            //prop_imgs 选择颜色的时候出现的图
            //item_imgs->item_img->url 所有的大图
            //desc 好像很厉害的样子
            $req->setFields("prop_img.url,item_img.url,nick,num_iid");
            //  $req->setFields("detail_url,num_iid,title,nick,type,cid,seller_cids,props,input_pids,input_str,desc,pic_url,num,valid_thru,list_time,delist_time,stuff_status,location,price,post_fee,express_fee,ems_fee,has_discount,freight_payer,has_invoice,has_warranty,has_showcase,modified,increment,approve_status,postage_id,product_id,auction_point,property_alias,item_img,prop_img,sku,video,outer_id,is_virtual");
            $req->setNumIid($item_id);
            $resp = $c->execute($req);
            return $resp;
        }
    }

    function getCats($parentid)
    {
        $client = new \Com\Pdd\Pop\Sdk\PopHttpClient($this -> pdd_config['key'], $this -> pdd_config['secret']);
        //设置相关的参数
        $request = new \Com\Pdd\Pop\Sdk\Api\Request\PddGoodsOptGetRequest();

        $request->setParentOptId(1);

        $response = $client->syncInvoke($request);
        $content = $response->getContent();
        if (empty($content['goods_opt_get_response']['goods_opt_list'])) {
            return [];
        }
        return $content['goods_opt_get_response'];
    }
}