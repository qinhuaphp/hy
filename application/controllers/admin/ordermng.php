<?php
class Ordermng extends MY_Controller{
	function orderlist(){
		$this->checklogin();
		$this->load->model('Order_model','order');
		$this->load->library('pagination');
		$post=$this->input->post();
		//var_dump($post);
		//echo '<br>';
		$pagesize=30;
		//获取偏移量
		$offset=intval($this->uri->segment(4));
		//多条件复合查询
		if(false!=$post){
			//计算出除了提交按钮后的post数组的单元个数（提交按钮占2个单元）
			$length=count($post)-2;
			//echo $length;
			//取出除了提交按钮外的所有post数组的单元
			$post=array_slice($post,0,$length);
			//print_r($post);echo '<br>';
			static $where=array();
			$where=$this->order->oscreen($post);
			//print_r($where);
			
			$res=$this->order->getorderlimit($pagesize,$offset,$where);
			$total=array_pop($res);
			$data['res']=$res;
			//print_r($data['res']);
		}
		//初始查询
		if(false==$post){
			$res=$this->order->getorderlimit($pagesize,$offset);
			$total=array_pop($res);
			$data['res']=$res;
			//print_r($data['res']);
		}
		//配置分页项目
		$config['total_rows']=$total;
		$config['per_page']=$pagesize;
		$config['base_url']=site_url('admin/ordermng/orderlist');
		$config['first_link']='<<';
		$config['num_links']=2;
		$config['prev_link']='<';
		$config['next_link']='>';
		$config['last_link']='>>';
		$config['uri_segment']=4;
		$config['display_pages']=true;
		$this->pagination->initialize($config);
		//创建链接
		$data['link']=$this->pagination->create_links();
		$data['total']=$config['total_rows']; 
		
		$this->output->enable_profiler(false);
		$this->load->view('admin/orderlist.html',$data);
	}
	function editorder(){
		$oid=$this->uri->segment(4);
		//echo $oid;
		$this->load->model('Order_model','order');
		$fields=array('order.oid','order.onum','order.omark','order.perprice','order.addtime','order.address','order.oauthor','order.ophone','order.odate','order.otime','order.ischeck','company.coname','human.hname','team.tname','member.mname','');
		$data['cs']=$this->order->getoneorder($fields,$oid);
		$data['oid']=$oid;
		//print_r($data['cs']);
		$this->output->enable_profiler(false);
		$this->load->view('admin/editorder.html',$data);
	}
	function edit(){
		$oid=$this->input->post('oid')+0;
		//print_r($post);
		//处理预约时间
		$data['odate']=$this->input->post('year').'/'.$this->input->post('mouth').'/'.$this->input->post('day');
		$data['address']=trim($this->input->post('address',true));
		$data['perprice']=trim($this->input->post('perprice'))+0;
		$data['oauthor']=trim($this->input->post('oauthor',true));
		$data['ophone']=trim($this->input->post('ophone',true));
		$data['otime']=$this->input->post('otime');
		$data['omark']=trim($this->input->post('omark',true));
		$data['ischeck']=$this->input->post('ischeck');
		//print_r($data);
		$this->load->model('Order_model','order');
		$res=$this->order->renew($data,$oid);
		if($res>0){
			$dat['msg']='成功修改订单信息';
		}else{
			$dat['msg']='未成功修改订单信息';
		}
		$this->load->view('admin/msg.html',$dat);
	}
	
}
?>