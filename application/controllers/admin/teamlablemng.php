<?php
class Teamlablemng extends MY_Controller{
	function addtl(){
		$this->checklogin();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('tlname','标签名称','required|callback_checktlname');
		$this->form_validation->set_rules('tlmark','标签备注','required');
		$result=$this->form_validation->run();
		if($result==false){
			$this->form_validation->set_error_delimiters('<span>','</span>');
			$this->load->view('admin/addtl.html');
		}else{
			$this->load->model('Teamlable_model','tm');
			$dat['tlname']=trim($this->input->post('tlname'));
			$dat['tlmark']=trim($this->input->post('tlmark'));
			$result=$this->tm->adddata($dat);
			if($result){
				$da['msg']='成功添加标签';
				$this->load->view('admin/msg.html',$da);
			}
		}
	}
	function checktlname($tlname){
		$this->load->model('Teamlable_model','tm');
		$fields='tlname';
		$tlname=trim($tlname);
		$where=array('tlname'=>$tlname);
		$rs=$this->tm->fetchone($fields,$where);
		if(!empty($rs)){
			$this->form_validation->set_message('checktlname','%s 已存在');	
			return false;
		}else{
			return true;
		}
	}
function tllist(){
		$this->checklogin();
		$this->load->model('Teamlable_model','tm');
		$data['lables']=$this->tm->fetchall();
		$this->load->view('admin/tllist.html',$data);
	}



}
?>