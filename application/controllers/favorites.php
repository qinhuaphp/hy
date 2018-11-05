<?php
class Favorites extends MY_Controller{
	function add(){
		$this->load->model('Favorites_model','favorites');
		if($this->input->is_ajax_request()){
			if(empty($_SESSION)||empty($_SESSION['userid'])){
				echo '0';
			}else{
				$default=array('hid','teamid');
				$array=$this->uri->uri_to_assoc(3,$default);
				//print_r($array);
				$arr=$this->favorites->screen($array);
				//print_r($arr);
				//print_r($_SESSION);
				switch($_SESSION['userid']['user']){
					case 'company';
						$this->load->model('Company_model','company');
						$id=$this->company->fetchone('coid',array('loginname'=>$_SESSION['userid']['loginname']));
						//print_r($id);
						$data=array_merge($arr,$id[0]);
						//print_r($data);exit;
						$row=$this->favorites->fetchone('faid',$data);
						if(!empty($row)){
							//$da['msg']='您已收藏，请勿重复收藏！';
							echo '1';
						}else{
							$data['fatime']=time();
							$res=$this->favorites->adddata($data);
							if($res){
									//$da['msg']='收藏成功!';
								echo '2';
								}
						}
					break;
					case 'member';
						$this->load->model('Member_model','member');
						$id=$this->member->fetchone('mid',array('mname'=>$_SESSION['userid']['loginname']));
						//print_r($id);
						$data=array_merge($arr,$id[0]);
						//print_r($data);exit;
						$row=$this->favorites->fetchone('faid',$data);
						if(!empty($row)){
							//$da['msg']='您已收藏，请勿重复收藏！';
							echo '1';
						}else{
							$data['fatime']=time();
							$res=$this->favorites->adddata($data);
							if($res){
								//$da['msg']='收藏成功!';
								echo '2';
								}
						}
					break;
					case 'team';
							echo '不能收藏同类型的团队或个人';
							
					break;
					case 'human';
							echo '不能收藏同类型的团队或个人';
				}
			}
		}
	}
	
}
?>
