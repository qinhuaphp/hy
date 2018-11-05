<?php
class Human extends MY_Controller{
	function index(){
        $data["controller_name"] = $this->uri->segment(1);
		$this->load->model('Link_model','link');
		$this->load->model('Site_model','site');
		$this->load->model('Cate_model','cate');
		$this->load->model('Human_model','human');		
		$default=array('cid','order');
		$arr=$this->uri->uri_to_assoc(3,$default);
		$cid=$arr['cid']+0;
		$data['cate']=$this->cate->fetchrow(array('cid','cname'),$cid);
		//加载分页类
		$this->load->library('pagination');
		$data['links']=$this->link->fetchall();
		$data['sites']=$this->site->fetchall();
        $res = $this->db->select('hname')->from('human')->where(array('cid'=>$cid))->get()->result_array();

		$data['cid']=$cid;
		//print_r($arr);echo '<br>';
		$pagesize=10;
		//从头部导航栏链接过来的时候URL上无order段
        /*
		if($arr['order']==''){
		$offset=intval($this->uri->segment(5));
		//echo $offset;echo '<hr>';
		$order=array('hid','desc');
		$config['uri_segment']=5;
		$config['base_url']=site_url('human/index/cid/'.$cid.'');
		}else{//从本页面来的链接的URL上存在order段
			$offset=intval($this->uri->segment(7));
			//echo $offset;
			$order=array($arr['order'],'desc');
			$config['uri_segment']=7;
			$config['base_url']=site_url('human/index/cid/'.$cid.'/order/'.$arr['order'].'');
		}
        */
        $offset=intval($this->uri->segment(5));
        $order=array('hid','desc');
		$config['uri_segment']=5;
		$config['base_url']=site_url('human/index/cid/'.$cid.'');
		$rs=$this->human->hlist($cid,$pagesize,$offset,$order);
		$total=array_pop($rs);
		$data['res']=$rs;
		//print_r($data['res']);
		$config['total_rows']=$total;
		$config['per_page']=$pagesize;
	    $config['first_link']='首页';
		$config['num_links']=2;
		$config['prev_link']='上一页';
		$config['next_link']='下一页';
		$config['last_link']='尾页';
		$config['display_pages']=true;
		$this->pagination->initialize($config);
		//创建链接
	    $data['link']=$this->pagination->create_links();
		$data['total']=$config['total_rows']; 
		//print_r($data);exit();
        if($this->input->is_ajax_request()){
            $str='<img src="'.base_url().'application/views/images/star.jpg" align="center">';
            foreach($data['res'] as $k=>$v){
                $data['res'][$k]['level']=str_repeat($str,$v['level']);
                $data['res'][$k]['sex']=$v['sex']='0'?'男':'女';
            }
            $da['res']=$data['res'];
            $da['link']=$data['link'];
            echo json_encode($da);
        }else{
            
            $this->output->enable_profiler(false);
            $this->load->view('header.html',$data);
            $this->load->view('personallist.html');
            $this->load->view('footer.html');
        }

	}
	function detail(){
        $data["controller_name"] = $this->uri->segment(1);
		$hid=$this->uri->segment(3)+0;
		$this->load->model('Link_model','link');
		$this->load->model('Site_model','site');
		$this->load->model('Consult_model','consult');
		$this->load->model('Human_model','human');
        $data['links']=$this->link->fetchall();
        $data['sites']=$this->site->fetchall();
        $data['consults']=$this->consult->fetchall();
        
        $data['hws']=$this->human->hworks($hid);
        $data['human_order'] = $this->human->get_human_order($hid);
        //print_r($data);exit();
        $this->output->enable_profiler(false);
        //print_r($data);exit();
        $this->load->view('header.html',$data);
        $this->load->view('personaldetail.html');
        $this->load->view('footer.html');
      
		
	}
	function hwork(){
        $data["controller_name"] = $this->uri->segment(1);
		if($this->input->is_ajax_request()){
			$default=array('wid','hid');
			$array=$this->uri->uri_to_assoc(3,$default);
			$this->load->model('Works_model','work');
			$res=$this->work->fetchone('wcontent',$array);
			//print_r($data['workinfo']);
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
	function team(){
        $data["controller_name"] = $this->uri->segment(1);
		$this->load->model('Link_model','link');
		$this->load->model('Site_model','site');
		$default=array('tlid','order');
		$arr=$this->uri->uri_to_assoc(3,$default);
		$tlid=$arr['tlid']+0;
		$data['links']=$this->link->fetchall();
		$data['sites']=$this->site->fetchall();
        $re = $this->db->select('tlname')->from('teamlable')->where(array('tlid'=>$tlid))->get()->result_array();

        $this->load->model('Team_model','team');		
		$this->load->model('Teamlable_model','tl');		
        $data['tl']=$this->tl->fetchrow(array('tlid','tlmark'),$tlid);
		$data['tlid']=$tlid;
		$this->load->library('pagination');
		$pagesize=10;
        /*
		//从头部导航栏链接过来的时候URL上无order段
		if($arr['order']==''){
            $data['order'] = '';
		$offset=intval($this->uri->segment(5));
		//echo $offset;echo '<hr>';
		$order=array('tlid','desc');
		$config['uri_segment']=5;
		$config['base_url']=site_url('human/team/tlid/'.$tlid.'');
		}else{//从本页面来的链接的URL上存在order段
			$offset=intval($this->uri->segment(7));
			//echo $offset;
            $data['order'] = $arr['order'];
			$order=array($arr['order'],'desc');
			$config['uri_segment']=7;
			$config['base_url']=site_url('human/team/tlid/'.$tlid.'/order/'.$arr['order'].'');
		}
        */
        
        $offset=intval($this->uri->segment(5));
        $order=array('tlid','desc');
		$config['uri_segment']=5;
		$config['base_url']=site_url('human/team/tlid/'.$tlid.'');
		$rs=$this->team->tlist($tlid,$pagesize,$offset,$order);
		$total=array_pop($rs);
		$data['res']=$rs;
		//print_r($data['res']);echo $total;
		$config['total_rows']=$total;
		$config['per_page']=$pagesize;
	    $config['first_link']='首页';
		$config['num_links']=2;
		$config['prev_link']='上一页';
		$config['next_link']='下一页';
		$config['last_link']='尾页';
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
            $da['link']=$data['link'];
            $da['res']=$data['res'];
            echo json_encode($da);
        }else{
            $this->output->enable_profiler(false);
            $this->load->view('header.html',$data);
            $this->load->view('teamlist.html');
            $this->load->view('footer.html');
        }

		
	}
	function tdetail(){
        $data["controller_name"] = $this->uri->segment(1);
		$default=array('tlid','teamid');
		$arr=$this->uri->uri_to_assoc(3,$default);
		$tlid=$arr['tlid'];
		$teamid=$arr['teamid'];
		$this->load->model('Link_model','link');
		$this->load->model('Site_model','site');
	    $data['sites']=$this->site->fetchall();
		$data['links']=$this->link->fetchall();

            $this->load->model('Team_model','team');		
            $this->load->model('Teamlable_model','tl');		
            $data['tl']=$this->tl->fetchrow(array('tlid','tlmark'),$tlid);

            
            $data['tws']=$this->team->tworks($teamid);
            
            //print_r($data);exit();
            $this->output->enable_profiler(false);
            $this->load->view('header.html',$data);
            $this->load->view('teamdetail.html');
            $this->load->view('footer.html');
        
	}
	function twork(){
        $data["controller_name"] = $this->uri->segment(1);
		if($this->input->is_ajax_request()){
			$default=array('wid','teamid');
			$array=$this->uri->uri_to_assoc(3,$default);
			$this->load->model('Works_model','work');
			$res=$this->work->fetchone('wcontent',$array);
			//print_r($data['workinfo']);
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
				echo json_encode($da[0][0]);
			}
		}
	}
}
?>