<?php
class Order extends MY_Controller{
	//下订单
	function index(){
		if(empty($_SESSION['userid'])||!isset($_SESSION['userid'])){
			redirect('login/index');
		}
		if($_SESSION['userid']['user']=='human'||$_SESSION['userid']['user']=='team'){
			$da['msg']='不能预定<br>同类型的婚礼个人或团队';
			$this->load->model('Link_model','link');
			$this->load->model('Site_model','site');
			$da['links']=$this->link->fetchall();
			$da['sites']=$this->site->fetchall();
			$this->load->view('header.html',$da);
			$this->load->view('ok.html');
			$this->load->view('footer.html');
			//exit;
		}else{
			$default=array('hid');
			$array=$this->uri->uri_to_assoc(3,$default);
			//print_r($array);
			$this->load->model('Link_model','link');
			$this->load->model('Site_model','site');
			$this->load->model('Order_model','order');
			$this->load->library('form_validation');
			$data['links']=$this->link->fetchall();
			$data['sites']=$this->site->fetchall();
			$where=$this->order->oscreen($array);
			if(isset($where['hid'])){
				//echo $array['hid'];
				$table='human';
				$fields=array('human.hid','human.hname','human.hprice','human.tellphone','cate.cname');
				$jointable='cate';
				$join='cate.cid=human.cid';
			}
			/*
			if(isset($where['teamid'])){
				//echo $array['hid'];
				$table='team';
				$fields=array('team.teamid','team.tname','team.phone','teamlable.tlmark');
				$jointable='teamlable';
				$join='teamlable.tlid=team.tlid';
			}
			*/
			//查询出预约对象的信息
			$data['obinfo']=$this->order->fetchob($fields,$where,$table,$jointable,$join);
			//print_r($data['obinfo']);
			/*设置验证规则*/
			//$this->form_validation->set_rules('perprice','价格','required|is_natural|min_length[1]|max_length[5]|xss_clean');
			$this->form_validation->set_rules('address','预约地址','required|trim|xss_clean');
			$this->form_validation->set_rules('omark','订单备注','required|trim|xss_clean');
			$this->form_validation->set_rules('oauthor','预约者姓名','required|trim|xss_clean');
			$this->form_validation->set_rules('ophone','联系电话','required|trim|callback_checkphone|xss_clean');
			$res=$this->form_validation->run();
			if($res==false){
				$this->form_validation->set_error_delimiters('<span style="color:red;font-size:12px;">','</span>');
				$this->load->view('header.html',$data);
				$this->load->view('order.html');
				$this->load->view('footer.html');
			}else{
				$post=$this->input->post();
				//print_r($post);
				//print_r($_SESSION);
				//生成订单编号，由固定的前缀‘hunyi’+当前时间+1到1百万的随机数组成
				$dat['onum']='hunyi'.date('YmdHis',time()).rand(1,1000000);
				//echo $dat['onum'];
				$dat['odate']=$post['year'].'/'.$post['mouth'].'/'.$post['day'];
				//echo $dat['odate'];
				$dat['otime']=$post['otime'];
				$dat['address']=$post['address'];
				$dat['omark']=$post['omark'];
				$dat['oauthor']=$post['oauthor'];
				$dat['ophone']=$post['ophone'];
				$dat['hid']=$post['hid']+0;
				$dat['addtime']=time();
				$dat['perprice']=$post['perprice'];
				if($_SESSION['userid']['user']=='company'){
					$this->load->model('Company_model','company');
					$arra=$this->company->fetchone('coid',array('loginname'=>$_SESSION['userid']['loginname']));
					$pk['coid']=$arra[0]['coid'];
					//echo $pk['coid'];
					
				}
				if($_SESSION['userid']['user']=='member'){
					$this->load->model('Member_model','member');
					$arra=$this->member->fetchone('coid',array('mname'=>$_SESSION['userid']['loginname']));
					$pk['mid']=$arra[0]['mid'];
					//echo $pk['mid'];
				}
				$dat=array_merge($dat,$pk);
				//print_r($dat);
				$res=$this->order->adddata($dat);
				if($res){
					$data['msg']='预约成功！<br>请到个人中心付款或继续浏览';
					$this->load->view('header.html',$data);
					$this->load->view('ok.html');
					$this->load->view('footer.html');
				}
				
			}
		}	
	}
	function checkphone($ophone){
		$phone=trim($ophone);
			$rs=preg_match('/^18(\d{9})$|^12(\d{9})$|^17(\d{9})$|^15(\d{9})$|^13(\d{9})$|^147(\d{8})$/',$ophone);
			if($rs==false){
				$this->form_validation->set_message('checkphone','%s 格式不正确');
				return false;
			}else{
				return true;
			}
		
	}
	//会员中心查看预约
	function olist(){
		//print_r($_SESSION['userid']);
		$this->load->model('Order_model','order');
		$this->load->library('pagination');
		$pagesize=10;
		$offset=$this->uri->segment(3);
		$res=$this->order->getoblimit($pagesize,$offset,$_SESSION['userid']['user'],$_SESSION['userid']['loginname']);
		$total=array_pop($res);
		$data['res']=$res;
		//print_r($data['res']);
		//配置分页项目
		$config['base_url']=site_url('order/olist');
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
		//print_r($data['res']);
		$this->output->enable_profiler(false);
		$this->load->view('myorder.html',$data);

	}
	
}
?>