<?php
class Consultmng extends MY_Controller{
	function addconsult(){
		$this->checklogin();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('con_name','客服名称','required');
		$this->form_validation->set_rules('con_qq','客服QQ','required|numeric');
		$result=$this->form_validation->run();
		if($result==false){
			$this->form_validation->set_error_delimiters('<span>','</span>');
			$this->load->view('admin/addconsult.html');
		}else{
			$this->load->model('Consult_model','consult');
			$data['con_name']=trim($this->input->post('con_name'));
			$data['con_qq']=trim($this->input->post('con_qq'));
			$result=$this->consult->adddata($data);
			if($result){
				$dat['msg']='成功添加客服';
				$this->load->view('admin/msg.html',$dat);
			}
		}
	}
 function consultlist(){
		$this->checklogin();
		$this->load->model('Consult_model');
		$data['results']=$this->Consult_model->fetchall();
		//print_r($data['results']);
		$this->load->view('admin/consultlist.html',$data);
	 }
	function editconsult($id){
		$this->checklogin();
		$con_id=$id+0;
		$this->load->model('Consult_model');
		$fields='con_id,con_name,con_qq';
		$data['rs']=$this->Consult_model->fetchrow($fields,$con_id);
		//print_r($rs);
		$this->load->view('admin/editconsult.html',$data);
	}
	function edit(){
		$con_id=trim($this->input->post('con_id'))+0;
		//echo $aid;
		$data['con_name']=trim($this->input->post('con_name'));
		$data['con_qq']=trim($this->input->post('con_qq'));
		//print_r($data);exit;
		$this->load->model('consult_model','consult');
		$res=$this->consult->renew($data,$con_id);
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