<?php
class Login extends MY_Controller{
	function index(){
		$this->load->model('Link_model','link');
		$this->load->model('Site_model','site');
		$data['links']=$this->link->fetchall();
		$data['sites']=$this->site->fetchall();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('user','登录名','required|alpha|xss_clean');
		$this->form_validation->set_rules('passwd','登录名、密码','required|xss_clean|callback_checkpwd');
		$result=$this->form_validation->run();
		if($result==false){
			//$this->output->enable_profiler(TRUE);
			//print_r($this->input->post());
			$this->form_validation->set_error_delimiters('<span style="color:red;font-size:12px;">','</span>');
			$this->load->view('header.html',$data);
			$this->load->view('login.html');
			$this->load->view('footer.html');
		}else{
				$post['loginname']=trim($this->input->post('loginname',true));
				$post['user']=trim($this->input->post('user',true));
				//echo '登录成功';
				$_SESSION['userid']=$post;
				//print_r($_SESSION);exit;
				redirect('hunyi/index');
		}

	}
	function checkpwd($passwd){
			$user=trim($this->input->post('user'));
			$name=trim($this->input->post('loginname'));
			$passwd=md5($passwd);
			switch($user){
					case  'company';
						$this->load->model('Company_model','company');
						$res=$this->company->fetchone('loginname,copwd',array('loginname'=>$name));
						if(!empty($res)){
							if($passwd!=$res[0]['copwd']){
									$this->form_validation->set_message('checkpwd','%s 错误');
									return false;	
							}else{
									return true;
							}
						}else{
								$this->form_validation->set_message('checkpwd','%s不存在');
								return false;	
						}
					break;
					case 'human';
						$this->load->model('Human_model','human');
						$res=$this->human->fetchone('loginname,passwd',array('loginname'=>$name));
						if(!empty($res)){
							if($passwd!=$res[0]['passwd']){
									$this->form_validation->set_message('checkpwd','%s 错误');
									return false;	
							}else{
									return true;
							}
						}else{
								$this->form_validation->set_message('checkpwd','%s不存在');
								return false;	
						}
					break;
					case 'team';
						$this->load->model('Team_model','team');
						$res=$this->team->fetchone('loginname,passwd',array('loginname'=>$name));
						if(!empty($res)){
							if($passwd!=$res[0]['passwd']){
									$this->form_validation->set_message('checkpwd','%s 错误');
									return false;	
							}else{
									return true;
							}
						}else{
								$this->form_validation->set_message('checkpwd','%s不存在');
								return false;	
						}
					break;
					case 'member';
						$this->load->model('Member_model','member');
						$res=$this->member->fetchone('mname,passwd',array('mname'=>$name));
						if(!empty($res)){
							if($passwd!=$res[0]['passwd']){
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
	function quite(){
		unset($_SESSION);
		session_destroy();
		redirect('hunyi/index','location');
	}
    
    function ajaxlogin(){
		if($this->input->is_ajax_request()){
		//echo '是ajax请求';
		//接受ajax发送的数据
		$user=$this->input->post('user',true);
		$name=trim($this->input->post('loginname',true));
		$passwd=md5($this->input->post('passwd'));
		//查询数据，若查到则返回1，否则返回0
		switch($user){
					case  'company';
						$this->load->model('Company_model','company');
						$res=$this->company->fetchone('loginname,cophone,copwd',array('cophone'=>$name));
						//print_r($res);
						if(!empty($res)){
							if($passwd!=$res[0]['copwd']){
								echo  '0'/*'密码无效'*/;
							}
							if($passwd==$res[0]['copwd']){
								$_SESSION['userid']=array('loginname'=>$res[0]['loginname'],'user'=>'company');
								echo $res[0]['loginname'];
							}
						}else{
							echo  '1'/*'登录名无效'*/;
						}
							
						
					break;
					case 'human';
						$this->load->model('Human_model','human');
						$res=$this->human->fetchone('loginname,passwd',array('tellphone'=>$name));
						if(!empty($res)){
							if($passwd!=$res[0]['passwd']){
								echo  '0'/*'密码无效'*/;
							}
							if($passwd==$res[0]['passwd']){
								$_SESSION['userid']=array('loginname'=>$res[0]['loginname'],'user'=>'human');
								echo $res[0]['loginname'];
							}
						}else{
							echo  '1'/*'登录名无效'*/;
						}
						
					break;
					case 'team';
						$this->load->model('Team_model','team');
						$res=$this->team->fetchone('loginname,passwd',array('phone'=>$name));
						if(!empty($res)){
							if($passwd!=$res[0]['passwd']){
								echo  '0'/*'密码无效'*/;
							}
							if($passwd==$res[0]['passwd']){
								$_SESSION['userid']=array('loginname'=>$res[0]['loginname'],'user'=>'team');
								echo $res[0]['loginname'];
							}
						}else{
							echo  '1'/*'登录名无效'*/;
						}
						
					break;
					case 'member';
						$this->load->model('Member_model','member');
						$res=$this->member->fetchone('mname,passwd',array('phone'=>$name));
						if(!empty($res)){
							if($passwd!=$res[0]['passwd']){
								echo  '0'/*'密码无效'*/;
							}
							if($passwd==$res[0]['passwd']){
								$_SESSION['userid']=array('loginname'=>$res[0]['mname'],'user'=>'member');
								echo $res[0]['mname'];
							}
						}else{
							echo  '1'/*'登录名无效'*/;
						}
					
			}

		}
	}
	function ajaxnamelogin(){
		if($this->input->is_ajax_request()){
			$user=$this->input->post('user',true);
			$name=trim($this->input->post('loginname',true));
			$passwd=md5($this->input->post('passwd'));
			//查询数据，若查到则返回1，否则返回0
		switch($user){
					case  'company';
						$this->load->model('Company_model','company');
						$res=$this->company->fetchone('loginname,copwd',array('loginname'=>$name));
						//print_r($res);
						if(!empty($res)){
							if($passwd!=$res[0]['copwd']){
								echo  '0'/*'密码无效'*/;
							}
							if($passwd==$res[0]['copwd']){
								$_SESSION['userid']=array('loginname'=>$res[0]['loginname'],'user'=>'company');
								echo $res[0]['loginname'];
							}
						}else{
							echo  '1'/*'登录名无效'*/;
						}
							
						
					break;
					case 'human';
						$this->load->model('Human_model','human');
						$res=$this->human->fetchone('loginname,passwd',array('loginname'=>$name));
						if(!empty($res)){
							if($passwd!=$res[0]['passwd']){
								echo  '0'/*'密码无效'*/;
							}
							if($passwd==$res[0]['passwd']){
								$_SESSION['userid']=array('loginname'=>$res[0]['loginname'],'user'=>'human');
								echo $res[0]['loginname'];
							}
						}else{
							echo  '1'/*'登录名无效'*/;
						}
						
					break;
					case 'team';
						$this->load->model('Team_model','team');
						$res=$this->team->fetchone('loginname,passwd',array('loginname'=>$name));
						if(!empty($res)){
							if($passwd!=$res[0]['passwd']){
								echo  '0'/*'密码无效'*/;
							}
							if($passwd==$res[0]['passwd']){
								$_SESSION['userid']=array('loginname'=>$res[0]['loginname'],'user'=>'team');
								echo $res[0]['loginname'];
							}
						}else{
							echo  '1'/*'登录名无效'*/;
						}
						
					break;
					case 'member';
						$this->load->model('Member_model','member');
						$res=$this->member->fetchone('mname,passwd',array('mname'=>$name));
						if(!empty($res)){
							if($passwd!=$res[0]['passwd']){
								echo  '0'/*'密码无效'*/;
							}
							if($passwd==$res[0]['passwd']){
								$_SESSION['userid']=array('loginname'=>$res[0]['mname'],'user'=>'member');
								echo $res[0]['mname'];
							}
						}else{
							echo  '1'/*'登录名无效'*/;
						}
					
			}
		}
	}
	
}
?>