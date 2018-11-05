<?php
class Linkmng extends MY_Controller{
	function addlink(){
		$this->checklogin();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('lname','链接名称','required');
		$this->form_validation->set_rules('urladdress','链接地址','required');
		$result=$this->form_validation->run();
		if($result==false){
			$this->form_validation->set_error_delimiters('<span>','</span>');
			$this->load->view('admin/addlink.html');
		}else{
			$this->load->model('Link_model');
			$data['lname']=trim($this->input->post('lname'));
			$data['urladdress']=trim($this->input->post('urladdress'));
			$result=$this->Link_model->adddata($data);
			if($result){
				$dat['msg']='成功添加地区';
				$this->load->view('admin/msg.html',$dat);
			}
		}
	}
	function linklist(){
		$this->checklogin();
		$this->load->model('Link_model');
		$data['results']=$this->Link_model->fetchall();
		//print_r($data['results']);
		$this->load->view('admin/linklist.html',$data);
	}
	function editlink($id){
		$this->checklogin();
		$lid=$id+0;
		$this->load->model('Link_model');
		$fields='lid,lname,urladdress';
		$data['rs']=$this->Link_model->fetchrow($fields,$lid);
		//print_r($rs);
		$this->load->view('admin/editlink.html',$data);
	}
	function edit(){
		$lid=trim($this->input->post('lid'))+0;
		//echo $aid;
		$data['lname']=trim($this->input->post('lname'));
		$data['urladdress']=trim($this->input->post('urladdress'));
		//print_r($data);exit;
		$this->load->model('Link_model');
		$res=$this->Link_model->renew($data,$lid);
		//echo $res;exit;
		if($res>0){
			$dat['msg']='编辑成功';
		}else{
			$dat['msg']='编辑失败';
		}
		$this->load->view('admin/msg.html',$dat);
	}
}
?>