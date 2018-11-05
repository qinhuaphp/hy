<?php
class Login extends CI_Controller{
	function __construct(){
		parent::__construct();
		session_start();
	}
	function checkuser(){
		//var_dump($_SESSION);
		$this->load->library('form_validation');
		$this->form_validation->set_rules('uname','登录名或密码','required|alpha');
		$this->form_validation->set_rules('passwd','登录名或密码','required|callback_checkpwd');
		$result=$this->form_validation->run();
		if($result==false){
			//$this->output->enable_profiler(TRUE);
			$this->form_validation->set_error_delimiters('<span>','</span>');
			$this->load->view('admin/userlogin.html');
			

		}else if($result){
				$_SESSION['user']=trim($this->input->post('uname'));
				//print_r($_SESSION);exit;
				redirect('admin/msg/main');
		}
	}
	function checkpwd($passwd){
		$uname=trim($this->input->post('uname'));
		//echo $uname;exit;
		$passwd=md5($passwd);
		$this->load->model('User_model','user');
		$fields='uname,passwd';
		$where=array('uname'=>$uname,'status'=>'1');
		$rs=$this->user->fetchone($fields,$where);
		//var_dump($rs);exit;
		if(!empty($rs)){
				if($passwd!=$rs[0]['passwd']){
						$this->form_validation->set_message('checkpwd','%s 错误');
						return false;	
				}else{
						return true;
				}
			}else{
					$this->form_validation->set_message('checkpwd','%s不存在');
					return false;	
			}
	
	
	}

}
?>