<?php
class Nodemng extends MY_Controller{
	function addnode(){
		$this->checklogin();
		$this->load->model('Node_model','node');
		/*获取除level字段的值为3，即除方法名外的数据*/
		$where=array('level !='=>3);
		$fields='nid,nname,nmarke,pid,level';
		$data['ns']=$this->node->fetchall($fields,$where);
		$this->load->library('form_validation');
		$this->form_validation->set_rules('nname','权限名','required|callback_checkname');
		$this->form_validation->set_rules('nmarke','备注','required');
		$res=$this->form_validation->run();
		if($res==false){
			$this->form_validation->set_error_delimiters('<span>','</span>');
			$this->load->view('admin/addnode.html',$data);
		}else{
			$post=$this->input->post();
			$dat['nname']=trim($post['nname']);
			$dat['nmarke']=trim($post['nmarke']);
			$dat['pid']=$post['pid'];
			$dat['level']=$post['level'];
			$result=$this->node->adddata($dat);
			if($result){
					$da['msg']='成功添加权限';
				}else{
					$da['msg']='添加权限失败';
				}
				$this->load->view('admin/msg.html',$da);
			}
	}
	function checkname($nname){
		$nname=trim($nname);
		$this->load->model('Node_model','node');
		$where=array('nname'=>$nname);
		$row=$this->node->fetchone('nname',$where);
		if(!empty($row)){
				$this->form_validation->set_message('checkname','%s 已存在');
				return false;
			}else{
					return true;
				
			}
		
	}
	function nodelist(){
		$this->checklogin();
		$this->load->model('Node_model','node');
		$data['rs']=$this->node->tree();
		$this->output->enable_profiler(false);
		$this->load->view('admin/nodelist.html',$data);
	}

}
?>