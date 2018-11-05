<?php
class Company extends MY_Controller{
	function index(){
        $data["controller_name"] = $this->uri->segment(1);
		$this->load->model('Link_model','link');
		$this->load->model('Site_model','site');
		$this->load->model('Consult_model','consult');
		$this->load->model('Company_model','company');
		 //加载分页类
		$this->load->library('pagination');
		$data['consults']=$this->consult->fetchall();
		$data['links']=$this->link->fetchall();
		$data['sites']=$this->site->fetchall();
		$pagesize=10;
		$offset=intval($this->uri->segment(3));
        //$order_name = $this->uri->segment(3, 'coregtime');
        $order = array('coid', 'desc');
        $config['base_url'] = site_url('company/index');
/* 		if($this->uri->segment(3)=='level'){
			$order=array('level','desc');
			$config['base_url']=site_url('company/index/level');
		}
		if($this->uri->segment(3)=='volume'){
			$order=array('volume','desc');
			$config['base_url']=site_url('company/index/volume');
		}
		if($this->uri->segment(3)=='default'){
			$order=array('coregtime','desc');
			$config['base_url']=site_url('company/index/default');
		} */
	
		$arr=$this->company->clist($pagesize,$offset,$order);
		$total=array_pop($arr);
		$data['res']=$arr;
		//配置分页项目
		//$config['base_url']=site_url('company/index');
		$config['total_rows']=$total;
		$config['per_page']=$pagesize;
	    $config['first_link']='首页';
		$config['num_links']=2;
		$config['prev_link']='上一页';
		$config['next_link']='下一页';
		$config['last_link']='尾页';
		$config['uri_segment']=3;
		$config['display_pages']=true;
		$this->pagination->initialize($config);
		//创建链接
	    $data['link']=$this->pagination->create_links();
		$data['total']=$config['total_rows']; 
        if($this->input->is_ajax_request()){
           $str='<img src="'.base_url().'application/views/images/star.jpg" align="middle">';
           foreach($data['res'] as $k=>$v){
               $data['res'][$k]['level']=str_repeat($str,$v['level']);
            }
          $da['res']=$data['res'];
          $da['link']=$data['link'];
          echo json_encode($da);
        }else{
		//print_r($data);exit();
		$this->output->enable_profiler(false);
		$this->load->view('header.html',$data);
		$this->load->view('companylist.html');
		$this->load->view('footer.html');
        }
	}
	function detail(){
        $data["controller_name"] = $this->uri->segment(1);
		$coid=$this->uri->segment(3)+0;
		$this->load->model('Link_model','link');
		$this->load->model('Site_model','site');
		$this->load->model('Consult_model','consult');
		$this->load->model('Company_model','company');
        $data['sites']=$this->site->fetchall();
		$data['links']=$this->link->fetchall();
        
 
        $data['sites']=$this->site->fetchall();
        $data['consults']=$this->consult->fetchall();
        $data['links']=$this->link->fetchall();
        $data['cws']=$this->company->coworks($coid);
        $data['company_order'] = $this->company->get_company_order($coid);
        //print_r($data);exit();
        $this->output->enable_profiler(false);
        $this->load->view('header.html',$data);
        $this->load->view('companydetail.html');
        $this->load->view('footer.html');

	}
	function work(){
        $data["controller_name"] = $this->uri->segment(1);
		if($this->input->is_ajax_request()){
			$default=array('wid','coid');
			$array=$this->uri->uri_to_assoc(3,$default);
			//print_r($array);
			$this->load->model('Works_model','work');
			$res=$this->work->fetchone('wcontent',$array);
			/*
			//正则提取img标签
			preg_match_all('/<img[^>]*src=[\'"]?([^\'"\s]+)[\'"]?[^>]*>/',$res[0]['wcontent'],$data);
			//print_r($res);
			echo json_encode($data[0]); 
			*/
			/*正则提取图片和视频flash地址*/
			//提取图片地址
			$rs=preg_match_all('/<img[^>]*src=[\'"]?([^\'"\s]+)[\'"]?[^>]*>/',$res[0]['wcontent'],$data);
			//print_r($rs);
			if($rs!=false){
				echo json_encode($data[0]);
				exit;
			}
			//提取视频flash地址
			$vsrc=preg_match_all('/(src|width|height)=([\"|\'])?(.*?)(?(2)\2|\s)/',$res[0]['wcontent'],$da);
			//print_r($vsrc);
			if($vsrc!=false){
				//print_r($da[0][0]);
				//$data['vsrc']=array('vsrc'=>explode('=',$da[0][0]));
				//print_r($data['vsrc']);
				//返回字符串类型的对象
				echo json_encode($da[0][0]);
			}
		}
		

		
	}
	
}
?>