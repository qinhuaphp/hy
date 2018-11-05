<?php
class Msg extends MY_Controller{
	function __construct(){
		parent::__construct();
		$this->checklogin();
	}
	function top(){
		//print_r($_SESSION);
		$uname=$_SESSION['user'];
		$this->load->model('User_model','user');
		$this->load->model('Group_model','group');
		$userinfo=$this->user->fetchone('umarke,gid',array('uname'=>$uname));
		$ginfo=$this->group->fetchone('gmarke',array('gid'=>$userinfo[0]['gid']));
		$data['user']=$userinfo[0]['umarke'];
		$data['group']=$ginfo[0]['gmarke'];
		$this->load->view('admin/top.html',$data);
	}
	function drag(){
		$this->load->view('admin/drag.html');
	}
	function  left(){
		
		$this->load->model('User_model','user');
		$this->load->model('Group_model','group');
		$this->load->model('Node_model','node');
		$uname=$_SESSION['user'];
		$where=array('uname'=>$uname);
		$userinfo=$this->user->fetchone('uid,uname,umarke,gid',$where);
		//print_r($userinfo);echo '<hr>';
		//var_dump($_SESSION);
		$ginfo=$this->group->fetchone('gid,nid',array('gid'=>$userinfo[0]['gid']));
		//print_r($ginfo);echo '<hr>';
		$nids=explode(',',$ginfo[0]['nid']);
		//print_r($nids);echo '<hr>';
		$ninfo=$this->node->fetchnodes('nid,nname,nmarke,pid,level',$nids);
		//print_r($ninfo);
		$data['nodes']=$ninfo;		
		/*在页面的底部显示程序的执行状态*/
		//$this->output->enable_profiler(TRUE);
		$this->load->view('admin/left.html',$data);
	}
	function main(){
		//var_dump($_SESSION);
		$this->load->view('admin/main.html');
	}
	function right(){
		$uname=$_SESSION['user'];
		$this->load->model('User_model','user');
		$userinfo=$this->user->fetchone('umarke',array('uname'=>$uname));
		$data['user']=$userinfo[0]['umarke'];
		$this->load->view('admin/right.html',$data);


	}
	function center(){
		$this->load->view('admin/center.html');
	}
	function down(){
		$this->load->view('admin/down.html');
	}
	function userexit(){
		unset($_SESSION['user']);	
		session_destroy();
		redirect('cctv');
	}


}
?>