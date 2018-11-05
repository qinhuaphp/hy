<?php
class Ajaxcenter extends MY_Controller{
	/*ajax更新密码*/
	function ajaxcheckpwd(){
		if($this->input->is_ajax_request()){
			$post=$this->input->post();
			//公司验证原密码
			if(isset($post['coid'])){
				$coid=$this->input->post("coid")+0;
				$pwd=md5($this->input->post('copwd'));
				$this->load->model('Company_model','company');
				$fields=array('coid','copwd');
				$where=array('coid'=>$coid);
				$res=$this->company->fetchone($fields,$where);
				if($pwd!=$res[0]['copwd']){
					echo '0';
				}else{
					echo '1';
				}
			}
			//婚礼个人验证原密码
			if(isset($post['hid'])){
				$hid=$this->input->post("hid")+0;
				$pwd=md5($this->input->post('passwd'));
				$this->load->model('Human_model','human');
				$fields=array('hid','passwd');
				$where=array('hid'=>$hid);
				$res=$this->human->fetchone($fields,$where);
				if($pwd!=$res[0]['passwd']){
					echo '0';
				}else{
					echo '1';
				}
			}
			//婚礼团队验证原密码
			if(isset($post['teamid'])){
				$teamid=$this->input->post("teamid")+0;
				$pwd=md5($this->input->post('passwd'));
				$this->load->model('Team_model','teamid');
				$fields=array('teamid','passwd');
				$where=array('teamid'=>$teamid);
				$res=$this->teamid->fetchone($fields,$where);
				if($pwd!=$res[0]['passwd']){
					echo '0';
				}else{
					echo '1';
				}
			}
			//新人验证原密码
			if(isset($post['mid'])){
				$mid=$this->input->post("mid")+0;
				$pwd=md5($this->input->post('passwd'));
				$this->load->model('Member_model','member');
				$fields=array('mid','passwd');
				$where=array('mid'=>$mid);
				$res=$this->member->fetchone($fields,$where);
				if($pwd!=$res[0]['passwd']){
					echo '0';
				}else{
					echo '1';
				}
			}
		}
	}
	/*ajax验证原手机号码*/
	function ajaxcheckphone(){
		$post=$this->input->post();
		if($this->input->is_ajax_request()){
			//公司检测原手机号
			if(isset($post['coid'])){
				$coid=$this->input->post("coid")+0;
				$cophone=trim($this->input->post('cophone'));
				$this->load->model('Company_model','company');
				$fields=array('coid','cophone');
				$where=array('coid'=>$coid);
				$res=$this->company->fetchone($fields,$where);
				if($cophone!=$res[0]['cophone']){
					echo '0';
				}else{
					echo '1';
				}
			}
			//个人检查原手机号
			if(isset($post['hid'])){
				$hid=$this->input->post("hid")+0;
				$tellphone=trim($this->input->post('tellphone'));
				$this->load->model('Human_model','human');
				$fields=array('hid','tellphone');
				$where=array('hid'=>$hid);
				$res=$this->human->fetchone($fields,$where);
				if($tellphone!=$res[0]['tellphone']){
					echo '0';
				}else{
					echo '1';
				}
			}
			//团队检查原手机号
			if(isset($post['teamid'])){
				$teamid=$this->input->post("teamid")+0;
				$tellphone=trim($this->input->post('phone'));
				$this->load->model('Team_model','team');
				$fields=array('teamid','phone');
				$where=array('teamid'=>$teamid);
				$res=$this->team->fetchone($fields,$where);
				if($tellphone!=$res[0]['phone']){
					echo '0';
				}else{
					echo '1';
				}
			}
		}
		
	}
	/*ajax检测新手机号是否占用*/
	function ajaxcheckuse(){
		$post=$this->input->post();
		if($this->input->is_ajax_request()){
			//公司检测原手机号
			if(isset($post['coid'])){
				$coid=$this->input->post("coid")+0;
				$cophone=trim($this->input->post('newphone'));
				$this->load->model('Company_model','company');
				$fields=array('coid','cophone');
				$where=array('cophone'=>$cophone);
				$res=$this->company->fetchone($fields,$where);
				if(empty($res)){
					echo '1';
				}else{
					echo '0';
				}
			}
			//个人检查原手机号
			if(isset($post['hid'])){
				$hid=$this->input->post("hid")+0;
				$tellphone=trim($this->input->post('newphone'));
				$this->load->model('Human_model','human');
				$fields=array('hid','tellphone');
				$where=array('tellphone'=>$tellphone);
				$res=$this->human->fetchone($fields,$where);
				if(empty($res)){
					echo '1';
				}else{
					echo '0';
				}
			}
			//团队检查原手机号
			if(isset($post['teamid'])){
				$teamid=$this->input->post("teamid")+0;
				$tellphone=trim($this->input->post('newphone'));
				$this->load->model('Team_model','team');
				$fields=array('teamid','phone');
				$where=array('phone'=>$tellphone);
				$res=$this->team->fetchone($fields,$where);
				if(empty($res)){
					echo '1';
				}else{
					echo '0';
				}
			}
		}
		
	}
	/*ajax检查作品案例的名称是否重复*/
	function ajaxcheckwname(){
		if($this->input->is_ajax_request()){
			$wname=trim($this->input->post('wname'));
			$this->load->model('Works_model','works');
			$fields='wname';
			$where=array('wname'=>$wname);
			$rs=$this->works->fetchone($fields,$where);
			if(!empty($rs)){
				echo '0';
			}else{
				echo '1';
			}
		}
	
	}
	/*ajax刷新案例的发布时间*/
	function ajaxuptime(){
		if($this->input->is_ajax_request()){
			$wid=$this->input->post('wid');
			//echo $wid;
			$this->load->model('Works_model','works');
			$data['addtime']=time();
			$res=$this->works->renew($data,$wid);
			if($res>0){
				echo '1';
			}else{
				echo '0';
			}
		}
		
	}
	
}
?>