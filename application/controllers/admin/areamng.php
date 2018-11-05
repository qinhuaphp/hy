<?php
class Areamng extends MY_Controller{
	function addarea(){
		$this->checklogin();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('aname','地区名称','required|alpha|callback_checkaname');
		$this->form_validation->set_rules('amark','地区备注','required');
		$result=$this->form_validation->run();
		//$this->output->enable_profiler(TRUE);
		if($result==false){
			$this->form_validation->set_error_delimiters('<span>','</span>');
			$this->load->view('admin/addarea.html');
		}else{
			$this->load->model('Area_model');
			$data['aname']=trim($this->input->post('aname'));
			$data['amark']=trim($this->input->post('amark'));
			$result=$this->Area_model->adddata($data);
			if($result){
				$dat['msg']='成功添加地区';
				$this->load->view('admin/msg.html',$dat);
			}
		}
		
	}
	function checkaname($aname){
		$aname=trim($aname);
		$this->load->model('Area_model','area');
		$where=array('aname'=>$aname);
		$row=$this->area->fetchone('aid,aname',$where);
		if(!empty($row)){
				$this->form_validation->set_message('checkaname','%s 已存在');
				return false;
			}else{
					return true;
				
			}
		
	}
	function arealist(){
		$this->checklogin();
		$this->load->model('Area_model');
		$data['results']=$this->Area_model->fetchall();
		//print_r($data['results']);
		$this->load->view('admin/arealist.html',$data);
	}
	function editarea($id){
		$this->checklogin();
		$aid=$id+0;
		$this->load->model('Area_model');
		$fields='aid,aname,amark';
		$data['rs']=$this->Area_model->fetchrow($fields,$aid);
		//print_r($rs);
		$this->load->view('admin/editarea.html',$data);
	}
	function edit(){
		$aid=trim($this->input->post('aid'))+0;
		//echo $aid;
		$data['aname']=trim($this->input->post('aname'));
		$data['amark']=trim($this->input->post('amark'));
		//print_r($data);exit;
		$this->load->model('Area_model','area');
		$res=$this->area->renew($data,$aid);
		if($res>0){
			$dat['msg']='编辑成功';
		}else{
			$dat['msg']='编辑失败';
		}
		$this->load->view('admin/msg.html',$dat);
	}
}
?>