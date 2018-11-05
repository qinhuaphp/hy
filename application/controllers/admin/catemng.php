<?php
class Catemng extends MY_Controller{
	function addcate(){
		$this->checklogin();
		$this->load->model('Area_model','area');
		$this->load->model('Cate_model','cate');
		$data['areas']=$this->area->fetchall();
		$data['cates']=$this->cate->gettree();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('cname','栏目名称','required|callback_checkname');
		$this->form_validation->set_rules('cintrol','栏目简介','required');
        $this->form_validation->set_rules('orderext','预约说明','required');
		$result=$this->form_validation->run();
		//$this->output->enable_profiler(TRUE);
		if($result==false){
			$this->form_validation->set_error_delimiters('<span>','</span>');
			$this->load->view('admin/addcate.html',$data);
		}else{
			//$this->load->model('Area_model');
			$date['cname']=trim($this->input->post('cname'));
			$date['cintrol']=trim($this->input->post('cintrol'));
			$date['pid']=$this->input->post('pid');
			$aids=$this->input->post('aid');
            $data['orderext']=$this->input->post('orderext');
			if(!empty($aids)){
				$date['aid']=implode(',',$aids);
				//print_r($date['aid']);exit;
				}	
				//print_r($date);exit;
			$result=$this->cate->adddata($date);
			if($result){
				$dat['msg']='成功添加栏目';
				$this->load->view('admin/msg.html',$dat);
			}
		}
		
	}
	function checkname($cname){
		$cname=trim($cname);
		$this->load->model('Cate_model','cate');
		$where=array('cname'=>$cname);
		$row=$this->cate->fetchone('cid,cname',$where);
		if(!empty($row)){
				$this->form_validation->set_message('checkname','%s 已存在');
				return false;
			}else{
					return true;
				
			}
	}
	function catelist(){
		$this->checklogin();
		$this->load->model('Cate_model','cate');
		$data['results']=$this->cate->gettree();
		$this->output->enable_profiler(false);
		$this->load->view('admin/catelist.html',$data);
	}
	function editcate($cid){
		$this->checklogin();
		$cid=$cid+0;
		$this->load->model('Cate_model');
		$this->load->model('Area_model');
		$fields='cid,cname,cintrol,aid,orderext';
		$data['rs']=$this->Cate_model->fetchrow($fields,$cid);
		$data['rs'][0]['aid']=explode(',',$data['rs'][0]['aid']);
		$data['areas']=$this->Area_model->fetchall();
		//print_r($data['rs']);
		//在模板中使用in_array()函数判断是否在栏目结果数组中的aid数组内存在aid，
		//若存在则把相应的aid的复选框设为checked
		$this->load->view('admin/editcate.html',$data);
	}
	function edit(){
		$cid=trim($this->input->post('cid'))+0;
		//echo $aid;
		$data['cname']=trim($this->input->post('cname'));
		$data['cintrol']=$this->input->post('cintrol');
        $data['orderext']=$this->input->post('orderext');
		//判断$aid数组是否为空，若为空则值为0；
		//如果不空则把数组转换为字符串再更新数据
		$aid=$this->input->post('aid');
		if(!empty($aid)){
			$data['aid']=implode(',',$aid);
		}else{
			$data['aid']=0;
		}
		//print_r($data);exit;
		$this->load->model('Cate_model','cate');
		$res=$this->cate->renew($data,$cid);
		if($res>0){
			$dat['msg']='编辑成功';
		}else{
			$dat['msg']='编辑失败';
		}
		$this->load->view('admin/msg.html',$dat);
	}
}
?>