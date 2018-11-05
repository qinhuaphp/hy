<?php
class Reg extends MY_Controller{
	function index(){
		$this->load->model('Link_model','link');
		$this->load->model('Site_model','site');
		$data['links']=$this->link->fetchall();
		$data['sites']=$this->site->fetchall();
		$this->load->view('header.html',$data);
		$this->load->view('regindex.html');
		$this->load->view('footer.html');
	}
	function regmember(){
		$this->load->model('Link_model','link');
		$this->load->model('Site_model','site');		
		$this->load->library('form_validation');
		$data['links']=$this->link->fetchall();
		$data['sites']=$this->site->fetchall();
		//设置验证规则
		$this->form_validation->set_rules('mname','登录名','required|callback_checkname|xss_clean');
		$this->form_validation->set_rules('realname','真实姓名','required|xss_clean');
		$this->form_validation->set_rules('phone','手机','required|callback_checkphone|xss_clean');
		$this->form_validation->set_rules('age','年龄','required');
		$this->form_validation->set_rules('passwd','登陆密码','required|min_length[6]');
		$this->form_validation->set_rules('repeatpwd','确认密码','required|matches[passwd]');
		$this->form_validation->set_rules('nickname','昵称','required|xss_clean');
		$this->form_validation->set_rules('sex','性别','required');
		$this->form_validation->set_rules('address','通讯地址','required|xss_clean');
		$res=$this->form_validation->run();
		if($res==false){
			$this->form_validation->set_error_delimiters('<span style="color:red;font-size:12px;">','</span>');
			//$this->load->view('admin/addcompany.html',$data);
			$this->load->view('header.html',$data);
			$this->load->view('regmember.html');
			$this->load->view('footer.html');
		}else{
			$dat['mname']=trim($this->input->post('mname',true));
			$dat['nickname']=trim($this->input->post('nickname',true));
			$dat['realname']=trim($this->input->post('realname',true));
			$dat['phone']=trim($this->input->post('phone',true));
			$dat['age']=$this->input->post('age',true);
			$dat['passwd']=md5(trim($this->input->post('passwd',true)));
			$dat['sex']=$this->input->post('sex',true);
			$dat['address']=trim($this->input->post('address',true));
			//print_r($dat);exit;
			$this->load->model('Member_model','member');
			$res=$this->member->adddata($dat);
			if($res){
					$data['msg']='<script type="text/javascript">alert(\'恭喜您，注册成功！\');window.location.href='.'"'.site_url('hunyi/index').'"</script>';
					$this->load->view('header.html',$data);
					$this->load->view('regmember.html');
					$this->load->view('footer.html');
			}
			
		}
	
	}
	function checkname($mname){
		$mname=trim($mname);
		$rs=preg_match('/^\w{4,20}$/i',$mname);
		if($rs==false){
			$this->form_validation->set_message('checkname','%s 不符合要求');
			return false;
		}else{
			$this->load->model('Member_model','member');
			$row=$this->member->fetchone('mname',array('mname'=>$mname));
			if(!empty($row)){
					$this->form_validation->set_message('checkname','%s 已存在');
					return false;
			}else{
					return true;
			}
		}
		
	}
	function checkphone($phone){
			$phone=trim($phone);
			$rs=preg_match('/^18(\d{9})$|^15(\d{9})$|^13(\d{9})$|^147(\d{8})$|^0(\d{3,4})-(\d{7,8})$/',$phone);
			if($rs==false){
				$this->form_validation->set_message('checkphone','%s 格式不正确');
				return false;
			}else{
				$this->load->model('Member_model','member');
				$fields='phone';
				$where=array('phone'=>$phone);
				$row=$this->member->fetchone($fields,$where);
				if(!empty($row)){
					/*设置错误提示*/
					$this->form_validation->set_message('checkphone','%s 已存在');
					return false;
					}else{
							return true;
							}
			}
	}
	
	
}
?>