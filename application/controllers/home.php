<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('M_item');
		$this->load->model('M_keyword');
		$this->load->library('pagination');
		$this->load->model('M_cat');
	}

    /**
     * 首页控制器
     *
     */
    public function index(){
        $this->page();
    }

    /**
     * 翻页控制器
     *
     * @param integer $page 第几页
     */
	public function page($page = 1)
	{
		$this->config->load('site_info');
        //$this->output->cache(10);

		$limit=40;
		//每页显示数目

		$config['base_url'] = site_url('/home/page');
		//site_url可以防止换域名代码错误。

		$config['total_rows'] = $this->M_item->count_items();
		//这是模型里面的方法，获得总数。
        $config['use_page_numbers'] = TRUE;
        $config['first_url'] = site_url('/home');
		$config['per_page'] = $limit;
		$config['first_link'] = '首页';
		$config['last_link'] = '尾页';
		$config['num_links']=10;
		//上面是自定义文字以及左右的连接数

		$this->pagination->initialize($config);
		//初始化配置

		$data['pagination']=$this->pagination->create_links();
		//通过数组传递参数
		//以上是重点

		//关键词列表，这个在后台配置
		$data['keyword_list'] = $this->M_keyword->get_all_keyword(5);

		//类别
		$data['cat'] = $this->M_cat->get_all_cat();

		//条目数据
		$data['items']=$this->M_item->get_all_item($limit,($page-1)*$limit);

		//站点信息
		$data['site_name'] = $this->config->item('site_name');

		//keysords和description
		$data['site_keyword'] = $this->config->item('site_keyword');
		$data['site_description'] = $this->config->item('site_description');
		
		$this->load->view('home_view',$data);
	}

	/**
	 * 跳转函数，同时记录点击数量
	 *
	 * 点击记数要排除机器访问
	 */
	function redirect($item_id){

        $this->load->library('user_agent');
        if(!$this->agent->is_robot()){
            $this->M_item->add_click_count($item_id);
        }

		Header("HTTP/1.1 303 See Other");
		Header("Location: ".$this->M_item->get_item_clickurl($item_id));
		exit;
	}

	/**
	 * 搜索结果页
	 *
	 */
	public function search(){
        $this->load->model('M_taobaoapi');
        $data['cat'] = $this->M_cat->get_all_cat();

         //获取搜索关键词+过滤
        $data['keyword'] = trim($this->input->get('keyword', TRUE),"'\"><");

        $this->M_keyword->add_keyword_if_not_exist($data['keyword']);

		//关键词列表，这个在后台配置
		$data['keyword_list'] = $this->M_keyword->get_all_keyword(5);


		//搜索条目的结果
		$data['resp'] = $this->M_item->searchItem($data['keyword']);
        
        
		//站点信息
		$data['site_name'] = $this->config->item('site_name');
		//keysords和description
		$data['site_keyword'] = $this->config->item('site_keyword');
		$data['site_description'] = $this->config->item('site_description');

		$this->load->view('search_view',$data);
	}

	//获取二维码
	public function qrCode($id)
    {
        $this->load->model('M_item');
        //查看当前商品的详情
        $result = $this -> M_item -> getItem($id);
        if (empty($result)) {
            $data['date_uri'] = null;
            $data['good_info'] = null;
        } else {
            //拿取出 mobile_url 字段
            $data['good_info'] = $result;
            $promotion_info = json_decode($result -> promotion_info, true);
            $mobile_url = $promotion_info['goods_promotion_url_generate_response']['goods_promotion_url_list']['0']['mobile_url'];

            //进行二维码的生成
            $qrCode = new \Endroid\QrCode\QrCode($mobile_url);
            $qrCode->setSize(300);
            $qrCode->setMargin(10);

            // Set advanced options
            $qrCode->setWriterByName('png');
            $qrCode->setEncoding('UTF-8');
            $qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]);
            $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);
            $qrCode->setValidateResult(false);

            $qrCode->setRoundBlockSize(true, \Endroid\QrCode\QrCode::ROUND_BLOCK_SIZE_MODE_MARGIN); // The size of the qr code is shrinked, if necessary, but the size of the final image remains unchanged due to additional margin being added (default)
            $qrCode->setRoundBlockSize(true, \Endroid\QrCode\QrCode::ROUND_BLOCK_SIZE_MODE_ENLARGE); // The size of the qr code and the final image is enlarged, if necessary
            $qrCode->setRoundBlockSize(true, \Endroid\QrCode\QrCode::ROUND_BLOCK_SIZE_MODE_SHRINK); // The size of the qr code and the final image is shrinked, if necessary

            $qrCode->setWriterOptions(['exclude_xml_declaration' => true]);
            $data['date_uri'] = $qrCode->writeDataUri();
        }

        //进行页面的渲染
        $this -> load->view('qrcode_view', $data);
    }

	//查看更多的商品
    //TODO 未完成 后期再进行补写
	public function more()
    {
        $this->load->model('M_taobaoapi');
        $data['cat'] = $this->M_cat->get_all_cat();

        //获取搜索关键词
        $keyword = trim($this->input->get('keyword', TRUE),"'\"");

        /* cid是类别id */
        $cid = '0';

        $data['resp'] = $this->M_taobaoapi->searchItem($keyword, $cid);
        $data['keyword'] =  $keyword;

        $this->load->view('admin/include_header');
        $this->load->view('admin/search_view',$data);
    }


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */