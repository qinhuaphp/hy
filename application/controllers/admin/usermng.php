<?php
/**/
class Usermng extends MY_Controller{
	function adduser(){
		$this->checklogin();
		$this->load->model('Group_model','group');
		$data['gs']=$this->group->fetchall();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('uname','登陆名','required|callback_checkname');
		$this->form_validation->set_rules('umarke','备注','required');
		$this->form_validation->set_rules('passwd','登陆密码','required|min_length[6]');
		$this->form_validation->set_rules('repwd','确认密码','required|matches[passwd]');
		$res=$this->form_validation->run();
		if($res==false){
			$this->form_validation->set_error_delimiters('<span>','</span>');
			$this->load->view('admin/adduser.html',$data);
		}else{
			$post=$this->input->post();
			$dat['uname']=trim($post['uname']);
			$dat['umarke']=trim($post['umarke']);
			$dat['passwd']=md5($post['passwd']);
			$dat['gid']=$post['gid'];
			$dat['status']=$post['status'];
			$this->load->model('User_model','user');
			$result=$this->user->adddata($dat);
			if($result){
					$da['msg']='注册用户成功';
				}else{
					$da['msg']='注册用户失败';
				}
				$this->load->view('admin/msg.html',$da);
			}
	
	}
	function checkname($name){
		$uname=trim($name);
		$this->load->model('User_model','user');
		$fields='uname';
		$where=array('uname'=>$uname);
		$rs=$this->user->fetchone($fields,$where);
		if(!empty($rs)){
				/*设置错误提示*/
				$this->form_validation->set_message('checkname','%s 已存在');
				return false;
				}else{
						return true;
						}
	}
	function userlist(){
		$this->checklogin();
		$this->load->model('User_model','user');
		//$array=$this->user->fetchall();
		$data['us']=$this->user->user_group();
		//print_r($data['us']);
		$this->output->enable_profiler(false);
		$this->load->view('admin/userlist.html',$data);
	}
	function edituser($uid){
		$this->checklogin();
		$uid=$this->uri->segment(4)+0;
		//echo $uid;
		$this->load->model('User_model','user');		
		$this->load->model('Group_model','group');		
		$fields='uid,uname,umarke,passwd,status,gid';
		$row=$this->user->fetchrow($fields,$uid);
		//print_r($row);
		$data['row']=$row /*$this->user->user_group($row)*/;
		//print_r($data['row']);
		$data['gs']=$this->group->fetchall();
		$this->load->view('admin/edituser.html',$data);
	}
	function edit(){
		$post=$this->input->post();
		$uid=$post['uid'];
		//$data['uname']=trim($post['uname']);
		$data['umarke']=trim($post['umarke']);
		$data['status']=$post['status'];
		$data['gid']=$post['gid'];
		$this->load->model('User_model','user');
		$rs=$this->user->renew($data,$uid);
		if($rs>0){
				$da['msg']='更新用户信息成功';	
		}else{
				$da['msg']='未更新用户信息';	
		}
		$this->load->view('admin/msg.html',$da);
	
	}
	function editpwd($uid){
		$this->checklogin();
		$uid=$uid+0;
		$this->load->model('User_model','user');
		$fields='uid,uname,passwd,umarke';
		$data['rs']=$this->user->fetchrow($fields,$uid);
		$this->load->library('form_validation');
		$this->form_validation->set_rules('passwd','新密码','required|min_length[6]');
		$this->form_validation->set_rules('repwd','确认密码','required|matches[passwd]');
		$res=$this->form_validation->run();
		if($res==false){
			$this->form_validation->set_error_delimiters('<span>','</span>');
			$this->load->view('admin/editpwd.html',$data);
		}else{
			$post=$this->input->post();
			$dat['passwd']=md5($post['passwd']);
			$uid=$post['uid'];
			$this->load->model('User_model','user');
			$result=$this->user->renew($dat,$uid);
			if($result){
					$da['msg']='修改密码成功';
				}else{
					$da['msg']='修改密码失败';
				}
				$this->load->view('admin/msg.html',$da);
			}
	}

}
?>