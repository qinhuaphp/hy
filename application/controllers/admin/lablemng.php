<?php
class Lablemng extends MY_Controller{
	function addlable(){
		$this->checklogin();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('laname','标签名称','required');
		$this->form_validation->set_rules('lamark','标签备注','required');
		$this->load->model('Lable_model', 'lable');
        $result=$this->form_validation->run();
		if($result==false){
            $data['lables']=$this->lable->gettree();
			$this->form_validation->set_error_delimiters('<span>','</span>');
			$this->load->view('admin/addlable.html', $data);
		}else{
			$data['laname']=trim($this->input->post('laname'));
			$data['lamark']=trim($this->input->post('lamark'));
			$data['pid']=$this->input->post('pid');
            $result=$this->lable->adddata($data);
			if($result){
				$dat['msg']='成功添加标签';
				$this->load->view('admin/msg.html',$dat);
			}
		}
		
	}
	function lablelist(){
		$this->checklogin();
		$this->load->model('Lable_model', 'lable');
		$data['lables']=$this->lable->gettree();
        
		$this->load->view('admin/lablelist.html',$data);
	}
    
	function editlable($id){
		$this->checklogin();
		$laid=$id+0;
		$this->load->model('Lable_model');
		$fields='laid,laname,lamark';
		$data['rs']=$this->Lable_model->fetchrow($fields,$laid);
		//print_r($rs);
		$this->load->view('admin/editlable.html',$data);
	}
	function edit(){
		$laid=$this->input->post('laid')+0;
		//echo $laid;
		$data['laname']=trim($this->input->post('laname'));
		$data['lamark']=trim($this->input->post('lamark'));
		//print_r($data);exit;
		$this->load->model('Lable_model');
		$res=$this->Lable_model->renew($data,$laid);
		if($res>0){
			$dat['msg']='编辑成功';
		}else{
			$dat['msg']='编辑失败';
		}
		$this->load->view('admin/msg.html',$dat);
	}
}
?>