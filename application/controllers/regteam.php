<?php
class Regteam extends MY_Controller{
	function index(){
		$this->load->model('Link_model','link');
		$this->load->model('Site_model','site');
		$this->load->library('form_validation');
		$data['links']=$this->link->fetchall();
		$data['sites']=$this->site->fetchall();
		//设置验证规则
		$this->form_validation->set_rules('loginname','登录名','required|callback_checkname|xss_clean');
		$this->form_validation->set_rules('tname','团队名称','required|xss_clean|callback_checktname');
		$this->form_validation->set_rules('phone','手机','required|callback_checkphone|xss_clean');
		$this->form_validation->set_rules('passwd','登陆密码','required|min_length[6]');
		$this->form_validation->set_rules('repeatpwd','确认密码','required|matches[passwd]');
		$this->form_validation->set_rules('tlid','服务','required|xss_clean');
		$res=$this->form_validation->run();
		if($res==false){
			$this->form_validation->set_error_delimiters('<span style="color:red;font-size:12px;">','</span>');
			//$this->load->view('admin/addcompany.html',$data);
			$this->load->view('header.html',$data);
			$this->load->view('regteam.html');
			$this->load->view('footer.html');
		}else{
				$dat['loginname']=trim($this->input->post('loginname',true));
				$dat['tname']=trim($this->input->post('tname',true));
				$dat['tlid']=$this->input->post('tlid',true);
				$dat['passwd']=md5(trim($this->input->post('passwd',true)));
				$dat['phone']=trim($this->input->post('phone',true));
				$this->load->model('Team_model','team');
				//print_r($dat);exit;
				$res=$this->team->adddata($dat);
				if($res){
						$this->load->view('header.html',$data);
						$this->load->view('regsuccess.html');
						$this->load->view('footer.html');
				}
		}

	}
	function checkname($loginname){
		$loginname=trim($loginname);
		$this->load->model('Team_model','team');
		$row=$this->team->fetchone('loginname',array('loginname'=>$loginname));
		if(!empty($row)){
				$this->form_validation->set_message('checkname','%s 已存在');
				return false;
		}else{
				return true;
		}
	}
	function checkphone($phone){
			$phone=trim($phone);
			$rs=preg_match('/^18(\d{9})$|^15(\d{9})$|^13(\d{9})$|^147(\d{8})$|^0(\d{3,4})-(\d{7,8})$/',$phone);
			if($rs==false){
				$this->form_validation->set_message('checkphone','%s 格式不正确');
				return false;
			}else{
				$this->load->model('Team_model','team');
				$fields='phone';
				$where=array('phone'=>$phone);
				$row=$this->team->fetchone($fields,$where);
				if(!empty($row)){
					/*设置错误提示*/
					$this->form_validation->set_message('checkphone','%s 已存在');
					return false;
					}else{
							return true;
							}
			}
	}
	function checktname($tname){
		$tname=trim($tname);
		$this->load->model('Team_model','team');
		$row=$this->team->fetchone('tname',array('tname'=>$tname));
		if(!empty($row)){
				$this->form_validation->set_message('checktname','%s 已存在');
				return false;
		}else{
				return true;
		}
		
	}
    
    function ajaxcheckname(){
		if($this->input->is_ajax_request()){
			$loginname=trim($this->input->post('loginname',true));
			$this->load->model('Team_model','team');
			$row=$this->team->fetchone('loginname',array('loginname'=>$loginname));
			if(!empty($row)){
						echo '0';
			}else{
						echo '1';
			}
		}
		
		
	}
	function ajaxchecktname(){
		if($this->input->is_ajax_request()){
			$tname=trim($this->input->post('tname',true));
			$this->load->model('Team_model','team');
			$row=$this->team->fetchone('tname',array('tname'=>$tname));
			if(!empty($row)){
						echo '0';
			}else{
						echo '1';
			}
		}
	}
	function ajaxcheckphone(){
		if($this->input->is_ajax_request()){
			$phone=trim($this->input->post('phone',true));
			$this->load->model('Team_model','team');
			$row=$this->team->fetchone('phone',array('phone'=>$phone));
			if(!empty($row)){
						echo '0';
			}else{
						echo '1';
			}
		}
		
	}

	
}
?>