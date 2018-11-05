<?php
class Groupmng extends MY_Controller{
	function addgroup(){
		$this->checklogin();
		$this->load->model('Node_model','node');
		$data['ns']=$this->node->tree();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('gname','组名','required|callback_checkname');
		$this->form_validation->set_rules('gmarke','备注','required');
		$this->form_validation->set_rules('nid','权限','required');
		$res=$this->form_validation->run();
		if($res==false){
			$this->form_validation->set_error_delimiters('<span>','</span>');
			$this->load->view('admin/addgroup.html',$data);
		}else{
			$post=$this->input->post();
			$dat['gname']=trim($post['gname']);
			$dat['gmarke']=trim($post['gmarke']);
			$dat['status']=$post['status'];
			$dat['nid']=implode(',',$post['nid']);
			print_r($dat['nid']);exit;
			//$dat['nid']=$post['nid'];
			$this->load->model('Group_model','group');
			$result=$this->group->adddata($dat);
			if($result){
					$da['msg']='成功添加用户组';
				}else{
					$da['msg']='添加用户组失败';
				}
				$this->load->view('admin/msg.html',$da);
			}
	}
	function checkname($name){
		$gname=trim($name);
		$this->load->model('Group_model','group');
		$fields='gname';
		$where=array('gname'=>$gname);
		$rs=$this->group->fetchone($fields,$where);
		if(!empty($rs)){
				/*设置错误提示*/
				$this->form_validation->set_message('checkname','%s 已存在');
				return false;
				}else{
						return true;
						}
	}
	function grouplist(){
		$this->checklogin();
		$this->load->model('Group_model','group');
		$data['gs']=$this->group->fetchall();
		//print_r($data['us']);
		$this->load->view('admin/grouplist.html',$data);
	}
/* 修改用户组权限*/
	function editnode($gid){
		$this->checklogin();
		$gid=$gid+0;
		$this->load->model('Group_model','group');
		$this->load->model('Node_model','node');
		$fields='gid,gmarke,nid';
		$row=$this->group->fetchrow($fields,$gid);
		//把nid组成的字符串转换为数组，使用where in条件
		$nids=explode(',',$row[0]['nid']);
		//print_r($nids);
		$nodes=$this->node->tree();
		$data['gs']=$row;
		$data['ns']=$nodes;
		$data['nids']=$nids;
		//print_r($data);
		$this->load->view('admin/editnode.html',$data);
	}
	function edit(){
		$post=$this->input->post();
		$data['nid']=implode(',',$post['nid']);
		$gid=$post['gid'];
		$this->load->model('group_model','group');
		$rs=$this->group->renew($data,$gid);
		if($rs>0){
				$da['msg']='更新权限成功';
		}else{
				$da['msg']='未更新权限';
		}
		$this->load->view('admin/msg.html',$da);
	}
	



}
?>