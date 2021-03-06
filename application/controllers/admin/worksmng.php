<?php
class Worksmng extends MY_Controller{
	function workslist(){
		$this->checklogin();
		$default=array('coid','hid','teamid');
		$arr=$this->uri->uri_to_assoc(4,$default);
		$this->load->model('Works_model','works');
		$arr=$this->works->screen($arr);
		//print_r($arr);
		$fields=array('works.wid','works.wname','works.cover_thumb','works.addtime','works.coid','works.hid','works.teamid');
		$data['works']=$this->works->getall($fields,$arr);
		//print_r($data['works']);
		$this->output->enable_profiler(false);
		$this->load->view('admin/workslist.html',$data);
	}
	function editwork(){
		$this->checklogin();
		$wid=$this->uri->segment(4)+0;
		$this->load->model('Works_model','works');
		$fields=array('wid','wname','cover_thumb','wintrol','wcontent','wprice','coid','hid','teamid');
		$data['winfo']=$this->works->fetchrow($fields,$wid);
		//print_r($data['winfo']);
		$config['max_width']='800';
		$config['max_heifht']='600';
		$config['max_size']='850';
		$config['upload_path']='data/upload/';
		$config['allowed_types']='gif|png|jpg|jpeg';
		$config['remove_spaces']=true;
		$config['encrypt_name']=true;
		$this->load->library('upload',$config);
		//执行上传
		$result=$this->upload->do_upload('cover_org');
		//获取上传文件的信息
		$imginfo=$this->upload->data();
		//var_dump($imginfo);exit;
		$post=$this->input->post();	
		if($post==false){
			$this->load->view('admin/editwork.html',$data);
		}else{
				$wid=$this->input->post('wid');
				$dat['wname']=trim($this->input->post('wname'));
				$dat['wprice']=trim($this->input->post('wprice'));
				$dat['wintrol']=trim($this->input->post('wintrol'));
				$dat['wcontent']=$this->input->post('wcontent');
				//表单被提交但是没有文件被上传
				if($imginfo['file_name']==''){
					//print_r($dat);exit;
					$rs=$this->works->renew($dat,$wid);
						if($rs>0){
							$da['msg']='成功修改作品信息';	
						}else{
							$da['msg']='未修改作品信息';
						}
							$this->load->view('admin/msg.html',$da);
					}else if($result==false){//如果有文件被上传，则判断上传的文件是否符合要求
								$errorinfo=$this->upload->display_errors('<span>','</span>');
								$data['error']=$errorinfo;
								$this->load->view('admin/editwork.html',$data);
					}else{//即提交了表单又上传了文件，则提交全部的数据
					//把图片的相对路径和文件名存入数据库
								$dat['cover_org']=$config['upload_path'].$imginfo['file_name'];
								$sourceimg=$imginfo['full_path'];
								$medconf['source_image']=$sourceimg;
								$medconf['new_image']='data/images/'.$imginfo['file_name'];
								$medconf['create_thumb']=true;
								$medconf['quelity']='80';
								$medconf['width']=365;
								$medconf['height']=265;
								$medconf['maintain_ratio']=true;
								$this->load->library('image_lib',$medconf);
								$this->image_lib->resize();
								//把缩略图的地址插入数据库
								$dat['cover_thumb']=$this->image_lib->full_dst_path;
								
								$rs=$this->works->renew($dat,$wid);
								if($rs>0){
									$da['msg']='成功修改公司信息';	
								}else{
									$da['msg']='未修改公司信息';
								}
									$this->load->view('admin/msg.html',$da);
							}
		}
		/*
		$this->output->enable_profiler(true);
		$this->load->view('admin/editwork.html',$data);
		*/
	}
	
}
?>