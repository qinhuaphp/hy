<?php
class Reghuman extends MY_Controller{
	function index(){
		$this->load->model('Link_model','link');
		$this->load->model('Site_model','site');
		$this->load->library('form_validation');
		$data['links']=$this->link->fetchall();
		$data['sites']=$this->site->fetchall();
		//设置验证规则
		$this->form_validation->set_rules('loginname','登录名','required|callback_checkname|xss_clean');
		$this->form_validation->set_rules('hname','真实姓名','required|xss_clean');
		$this->form_validation->set_rules('tellphone','手机','required|callback_checkphone|xss_clean');
		$this->form_validation->set_rules('passwd','登陆密码','required|min_length[6]');
		$this->form_validation->set_rules('repeatpwd','确认密码','required|matches[passwd]');
		$this->form_validation->set_rules('cid','服务','required|xss_clean');
		//$this->form_validation->set_rules('sex','性别','required|xss_clean');
		$res=$this->form_validation->run();
		if($res==false){
			$this->form_validation->set_error_delimiters('<span style="color:red;font-size:12px;">','</span>');
			//$this->load->view('admin/addcompany.html',$data);
			$this->load->view('header.html',$data);
			$this->load->view('reghuman.html');
			$this->load->view('footer.html');
		}else{
				$dat['loginname']=trim($this->input->post('loginname',true));
				$dat['hname']=trim($this->input->post('hname',true));
				$dat['cid']=$this->input->post('cid',true);
				//$dat['sex']=$this->input->post('sex',true);
				$dat['passwd']=md5(trim($this->input->post('passwd',true)));
				$dat['tellphone']=trim($this->input->post('tellphone',true));
				$this->load->model('Human_model','human');
				//print_r($dat);exit;
				$res=$this->human->adddata($dat);
				if($res){
						$this->load->view('header.html',$data);
						$this->load->view('regsuccess.html');
						$this->load->view('footer.html');
				}
		}

	}
	function checkname($loginname){
		$loginname=trim($loginname);
		$this->load->model('Human_model','human');
		$row=$this->human->fetchone('loginname',array('loginname'=>$loginname));
		if(!empty($row)){
				$this->form_validation->set_message('checkname','%s 已存在');
				return false;
		}else{
				return true;
		}
	}
	function checkphone($tellphone){
			$tellphone=trim($tellphone);
			$rs=preg_match('/^18(\d{9})$|^15(\d{9})$|^13(\d{9})$|^147(\d{8})$|^0(\d{3,4})-(\d{7,8})$/',$tellphone);
			if($rs==false){
				$this->form_validation->set_message('checkphone','%s 格式不正确');
				return false;
			}else{
				$this->load->model('Human_model','human');
				$fields='tellphone';
				$where=array('tellphone'=>$tellphone);
				$row=$this->human->fetchone($fields,$where);
				if(!empty($row)){
					/*设置错误提示*/
					$this->form_validation->set_message('checkphone','%s 已存在');
					return false;
					}else{
							return true;
							}
			}
	}
    
    function ajaxcheckname(){
		if($this->input->is_ajax_request()){		
			$loginname=trim($this->input->post('loginname',true));
			$this->load->model('Human_model','human');
			$row=$this->human->fetchone('loginname',array('loginname'=>$loginname));
			if(!empty($row)){
					echo '0';
			}else{
					echo '1';
			}
		}	

	}
	function ajaxcheckphone(){
		if($this->input->is_ajax_request()){		
			$tellphone=trim($this->input->post('tellphone',true));
			$this->load->model('Human_model','human');
			$row=$this->human->fetchone('tellphone',array('tellphone'=>$tellphone));
			if(!empty($row)){
					echo '0';
			}else{
					echo '1';
			}
		}	
		
	}

}
?>