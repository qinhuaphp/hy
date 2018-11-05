<?php
class Custom extends MY_Controller{
	function index(){
		$this->load->model('Link_model','link');
		$this->load->model('Site_model','site');
		//$this->load->model('Consult_model','consult');
		$this->load->model('Article_model','article');
		$data['links']=$this->link->fetchall();
		$data['sites']=$this->site->fetchall();
        
		//$data['consults']=$this->consult->fetchall();
		//取出成功案例的前6个推荐的数据
		$where=array('cid'=>2);
		$data['anli']=$this->article->arrecom($where, 9);
		//取出婚礼学院的前10个推荐的数据
		$where=array('cid'=>1);
		$data['xy']=$this->article->arrecom($where, 4);
		//print_r($data['xy']);
		$this->output->enable_profiler(false);
		$this->load->view('header.html',$data);
		$this->load->view('custom.html');
		$this->load->view('footer.html');
	}
    /*私人订制列表页*/
    function orderpublic() {
        $this->load->model('Link_model','link');
		$this->load->model('Site_model','site');
		$data['links']=$this->link->fetchall();
		$data['sites']=$this->site->fetchall();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('name','姓名','required');
		$this->form_validation->set_rules('phone','手机号','required|callback_checkphone');
		$this->form_validation->set_rules('address','地点','required');
		$this->form_validation->set_rules('marrydate','婚期','required');
		$this->form_validation->set_rules('budget','预算','required');
		$rs=$this->form_validation->run();
		if ($rs == false) {
			$this->form_validation->set_error_delimiters('<span style="color:red;font-size:12px;">','</span>');
            
			$this->load->view('header.html', $data);
			$this->load->view('orderpublic.html');
			$this->load->view('footer.html');
			}
			else{
				$post=$this->input->post();
				//print_r($post['marrydate']);echo '<br>';
				$data['name']=trim($post['name']);
				$data['phone']=trim($post['phone']);
				$data['address']=trim($post['address']);
				$data['marrydate']=strtotime($post['marrydate']);
				$data['budget']=trim($post['budget']);
				//echo date('Y-m-d',$data['marrydate']);echo '<br>';
				//print_r($data);
				$this->load->model('Custom_model','custom');
				$res=$this->custom->adddata($data);
				if($res){
					$dat['msg']='<script type="text/javascript">alert(\'发布定制信息成功!请耐心等待工作人员的电话回复\');window.location.href='.'"'.site_url('hunyi/customform').'"</script>';
					//$this->load->view('orderpublicsucc.html',$dat);
				}
			}	
			
	}
	
}
?>