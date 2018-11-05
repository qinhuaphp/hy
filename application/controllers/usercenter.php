<?php
class Usercenter extends MY_Controller{
	/*个人中心首页*/
	function index(){
		if(empty($_SESSION)||empty($_SESSION['userid'])){
			redirect('login/index','location');
		}
		$this->load->model('Link_model','link');
		$this->load->model('Site_model','site');
		$data['links']=$this->link->fetchall();
		$data['sites']=$this->site->fetchall();
		//print_r($_SESSION);
		switch($_SESSION['userid']['user']){
			case 'company';
					$this->load->model('Company_model','company');
					$fields=array('coid','coname','cologo','volume','level');
					$where=array('loginname'=>$_SESSION['userid']['loginname']);
					$row=$this->company->fetchone($fields,$where);
					$userinfo['id']=$row[0]['coid'];
					$userinfo['name']=$row[0]['coname'];
					$userinfo['logo']=$row[0]['cologo'];
					$userinfo['level']=$row[0]['level'];
					$userinfo['volume']=$row[0]['volume'];
					$data['userinfo']=$userinfo;
					//$data['function']='pz';
					//$data['identy']='company';
					$data['idname']='coid';
					//print_r($data['userinfo']);
					break;
			case 'human';
					$this->load->model('Human_model','human');
					$fields=array('hid','hname','hlogo','volume','level');
					$where=array('loginname'=>$_SESSION['userid']['loginname']);
					$row=$this->human->fetchone($fields,$where);
					$userinfo['id']=$row[0]['hid'];
					$userinfo['name']=$row[0]['hname'];
					$userinfo['logo']=$row[0]['hlogo'];
					$userinfo['level']=$row[0]['level'];
					$userinfo['volume']=$row[0]['volume'];
					$data['userinfo']=$userinfo;
					//$data['identy']='human';
					$data['idname']='hid';
					//print_r($data['userinfo']);
					break;
			case 'team';
					$this->load->model('Team_model','team');
					$fields=array('teamid','tname','tlogo','volume','level');
					$where=array('loginname'=>$_SESSION['userid']['loginname']);
					$row=$this->team->fetchone($fields,$where);
					$userinfo['id']=$row[0]['teamid'];
					$userinfo['name']=$row[0]['tname'];
					$userinfo['logo']=$row[0]['tlogo'];
					$userinfo['level']=$row[0]['level'];
					$userinfo['volume']=$row[0]['volume'];
					$data['userinfo']=$userinfo;
					//$data['identy']='team';
					$data['idname']='teamid';
					//print_r($data['userinfo']);
					break;
			case 'member';
					redirect('hunyi/index','location');
					break;
		}
		$this->output->enable_profiler(false);
		//$this->load->view('header.html',);
		$this->load->view('members/usercenter.html',$data);
		//$this->load->view('footer.html');
	}
	/*个人中心基本信息*/
	function baseinfo(){
		$default=array('coid','hid','teamid');
		$arr=$this->uri->uri_to_assoc(3,$default);
		//print_r($arr);
		//过滤掉空单元
		$this->load->model('Parent_model','parentm');
		$wh=$this->parentm->screen($arr);
		if(isset($wh['coid'])){
			$this->load->model('Company_model','company');
			$fields=array('coid','loginname','cophone');
			$row=$this->company->fetchrow($fields,$wh['coid']);
			$baseinfo['loginname']=$row[0]['loginname'];
			$baseinfo['phone']=$row[0]['cophone'];
			$data['baseinfo']=$baseinfo;
			//print_r($data['baseinfo']);
		}
		if(isset($wh['hid'])){
			$this->load->model('Human_model','human');
			$fields=array('hid','loginname','tellphone');
			$row=$this->human->fetchrow($fields,$wh['hid']);
			$baseinfo['loginname']=$row[0]['loginname'];
			$baseinfo['phone']=$row[0]['tellphone'];
			$data['baseinfo']=$baseinfo;
			//print_r($data['baseinfo']);
		}
		if(isset($wh['teamid'])){
			$this->load->model('Team_model','team');
			$fields=array('teamid','loginname','phone');
			$row=$this->team->fetchrow($fields,$wh['teamid']);
			$baseinfo['loginname']=$row[0]['loginname'];
			$baseinfo['phone']=$row[0]['phone'];
			$data['baseinfo']=$baseinfo;
			//print_r($data['baseinfo']);,$data
		}
		$this->output->enable_profiler(false);
		$this->load->view('members/baseinfo.html',$data);

	}
	/*裁剪图片*/
	function croppic(){
		if($this->input->is_ajax_request()){
			$default=array('coid','hid','teamid','mid');
			$arr=$this->uri->uri_to_assoc(3,$default);
			//print_r($arr);
			//过滤掉空单元
			$this->load->model('Parent_model','parentm');
			$wh=$this->parentm->screen($arr);
			$config['source_image'] =$this->input->post('sourceimg');
			//echo $config['source_image'];
			$arra=explode('.',$config['source_image']);
			//print_r($arra);
			$config['new_image']='data/images/'.time().rand(1,300000).'.'.$arra[1];
			//echo $config['new_image'];
			$config['maintain_ratio'] = FALSE; 
			$config['quality']=90;
			$config['x_axis']=$this->input->post('x')*$this->input->post('blx');
			$config['y_axis'] =$this->input->post('y')*$this->input->post('bly');
			$config['width'] =$this->input->post('w');
			$config['height'] =$this->input->post('h');
			$this->load->library('image_lib');
			$this->image_lib->initialize($config); 
			if($this->image_lib->crop()){
				if(isset($wh['coid'])){
					$this->load->model('Company_model','company');
					$data['cologo']=$this->image_lib->full_dst_path;
					$res=$this->company->renew($data,$wh['coid']);
					if($res>0){
						echo $data['cologo'];
					}else{
						echo '0';
					}
				}
				if(isset($wh['hid'])){
					$this->load->model('Human_model','human');
					$data['hlogo']=$this->image_lib->full_dst_path;
					$res=$this->human->renew($data,$wh['hid']);
					if($res>0){
						echo $data['hlogo'];
					}else{
						echo '0';
					}
				}
				if(isset($wh['teamid'])){
					$this->load->model('Team_model','team');
					$data['tlogo']=$this->image_lib->full_dst_path;
					$res=$this->team->renew($data,$wh['teamid']);
					if($res>0){
						echo $data['tlogo'];
					}else{
						echo '0';
					}
				}
				
			}
			
		}

	}
	/*修改头像*/
	function modifypic(){
		$default=array('coid','hid','teamid','mid');
		$arr=$this->uri->uri_to_assoc(3,$default);
		//print_r($arr);
		//过滤掉空单元
		$this->load->model('Parent_model','parentm');
		$wh=$this->parentm->screen($arr);
		if(isset($wh['coid'])){
			$data['idname']='coid';
			$data['upfile']='cologo';
			$data['idvalue']=$wh['coid'];
			//print_r($this->input->post('Filedata'));
			//print_r($_FILES['Filedata']);
			
		}
		if(isset($wh['hid'])){
			$data['idname']='hid';
			$data['upfile']='hlogo';
			$data['idvalue']=$wh['hid'];
			
		}
		if(isset($wh['teamid'])){
			$data['idname']='teamid';
			$data['upfile']='tlogo';
			$data['idvalue']=$wh['teamid'];
		}
		$this->load->view('members/modifypic.html',$data);
		
	}
	/*个人中心修改资料*/
	function pz(){
		$default=array('coid','hid','teamid','mid');
		$arr=$this->uri->uri_to_assoc(3,$default);
		//print_r($arr);
		//过滤掉空单元
		$this->load->model('Parent_model','parentm');
		$wh=$this->parentm->screen($arr);
		/*公司帐号*/
		if(isset($wh['coid'])){
			$this->load->model('Company_model','company');
			if(!$this->input->is_ajax_request()){
				$row=$this->company->fetchrow(array('coid','cointrol','cointrolvideo','colocation','coaccount','style'),$wh['coid']);
				$data['userinfo']=$row;
				$this->load->view('members/companymsg.html',$data);
			}else{
				$coid=$this->input->post('coid');
				$dat['cointrol']=trim($this->input->post('cointrol'));
				$dat['cointrolvideo']=$this->input->post('cointrolvideo');
				$dat['colocation']=$this->input->post('colocation');
				$dat['coaccount']=$this->input->post('coaccount');
				$dat['style']=$this->input->post('style');
				$res=$this->company->renew($dat,$coid);
				if($res>0){
					echo '1';
				}else{
					echo '0';
				}
			}	
		} 
		/*婚礼个人帐号*/
		if(isset($wh['hid'])){
			$this->load->model('Human_model','human');
			if(!$this->input->is_ajax_request()){
				$this->load->model('Team_model','team');
				$this->load->model('Cate_model','cate');
				$this->load->model('Teamlable_model','tl');
				$this->load->model('Service_model','service');
				$row=$this->human->fetchrow(array('hid','sex','hintrolvideo','hintrol','age','haccount','cid','teamid'),$wh['hid']);
				//根据栏目选取对应的服务表内的数据
				//$data['services']=$this->service->fetchall(array('sid','type'),array("cid"=>$row[0]['cid']));
				//print_r($data['services']);
				$cate=$this->cate->fetchone('cname',array('cid'=>$row[0]['cid']));
				$tl=$this->tl->fetchone('tlid',array('tlname'=>$cate[0]['cname']));
				$data['teams']=$this->team->fetchall('teamid,tname',array('ischeck'=>1,'tlid'=>$tl[0]['tlid']));
				$data['userinfo']=$row;
				//print_r($data);
				$this->load->view('members/humanmsg.html',$data);	
			}else{
				$hid=$this->input->post('hid');
				$dat['hintrol']=trim($this->input->post('hintrol'));
				$dat['hintrolvideo']=$this->input->post('hintrolvideo');
				$dat['teamid']=$this->input->post('teamid');
				$dat['haccount']=$this->input->post('haccount');
				$dat['age']=$this->input->post('age');
				$dat['sex']=$this->input->post('sex');
				$res=$this->human->renew($dat,$hid);
				if($res>0){
					echo '1';
				}else{
					echo '0';
				}
			}
		} 
		/*新人更新资料*/
		if(isset($wh['mid'])){
			$this->load->model('Member_model','member');
			if(!$this->input->is_ajax_request()){
				$row=$this->member->fetchrow(array('mid','sex','nickname','realname','age','address'),$wh['mid']);
				//print_r($row);echo $row[0]['cid'];
				$data['userinfo']=$row;
				//print_r($data);
				$this->load->view('members/membermsg.html',$data);	
			}else{
					$mid=$this->input->post('mid');
					$dat['nickname']=trim($this->input->post('nickname'));
					$dat['realname']=trim($this->input->post('realname'));
					$dat['address']=trim($this->input->post('address'));
					$dat['age']=$this->input->post('age');
					$dat['sex']=$this->input->post('sex');
					$res=$this->member->renew($dat,$mid);
					if($res>0){
						echo '1';
					}else{
						echo '0';
					}
				
			}
		} 
		/*婚礼团队帐号*/
		if(isset($wh['teamid'])){
			$this->load->model('Team_model','team');
			if(!$this->input->is_ajax_request()){
				$row=$this->team->fetchrow(array('teamid','tintrol','tintrolvideo'),$wh['teamid']);
				//print_r($row);
				$data['userinfo']=$row;
				//print_r($data);
				$this->output->enable_profiler(false);
				$this->load->view('members/teammsg.html',$data);	
			}else{
				$teamid=$this->input->post('teamid');
				$dat['tintrol']=trim($this->input->post('tintrol'));
				$dat['tintrolvideo']=$this->input->post('tintrolvideo');
				$res=$this->team->renew($dat,$teamid);
				if($res>0){
					echo '1';
				}else{
					echo '0';
				}
			}

		} 
	}
	/*更改密码*/
	function updatepwd(){
		$default=array('coid','hid','teamid');
		$arr=$this->uri->uri_to_assoc(3,$default);
		//过滤掉空单元
		$this->load->model('Parent_model','parentm');
		$wh=$this->parentm->screen($arr);
		//print_r($wh);
		/*婚庆公司更新密码*/
		if(isset($wh['coid'])){
			$data['pwd']='copwd';
			$data['idname']='coid';
			$data['idvalue']=$wh['coid'];
			$data['phone']='cophone';
			if($this->input->is_ajax_request()){
				$post=$this->input->post();
				$da['copwd']=md5($post['newpwd']);
				$coid=$post['coid'];
				$this->load->model('Company_model','company');
				$result=$this->company->renew($da,$coid);
				if($result>0){
					echo '1';
				}else{
					echo '0';
				}
			}else{
			//print_r($data);
				$this->load->library('form_validation');
				$this->form_validation->set_rules('copwd','原密码','required|callback_checkpwd');
				$this->form_validation->set_rules('newpwd','新密码','required|min_length[6]');
				$this->form_validation->set_rules('repwd','确认密码','required|matches[newpwd]');
				$res=$this->form_validation->run();
				if($res==false){
				$this->form_validation->set_error_delimiters('<span style="font-size:12px;text-align:center; height:30px; line-height:30px;display:inline; color:#ff0000; ">','</span>');
				$this->load->view('members/seccenter.html',$data);	
				}else{
						$post=$this->input->post();
						$da['copwd']=md5($post['newpwd']);
						$coid=$post['coid'];
						$this->load->model('Company_model','company');
						$result=$this->company->renew($da,$coid);
						if($result>0){
								$data['msg']='<script type="text/javascript">alert(\'成功修改密码\');window.location.href="'.site_url("usercenter/updatepwd/coid/$coid").'"</script>';	
						}else{
								$data['msg']='<script type="text/javascript">alert(\'未成功修改密码\');window.location.href="'.site_url("usercenter/updatepwd/coid/$coid").'"</script>';	
						}
						$this->load->view('members/seccenter.html',$data);
				}
			}
			

			
		}
		//婚礼个人更新密码
		if(isset($wh['hid'])){
			$data['pwd']='passwd';
			$data['idname']='hid';
			$data['idvalue']=$wh['hid'];
			$data['phone']='tellphone';
			if($this->input->is_ajax_request()){
				$post=$this->input->post();
				$da['passwd']=md5($post['newpwd']);
				$hid=$post['hid'];
				$this->load->model('Human_model','human');
				$result=$this->human->renew($da,$hid);
				if($result>0){
						echo '1';
				}else{
						echo '0';
				}
			}else{
							//print_r($data);
				$this->load->library('form_validation');
				$this->form_validation->set_rules('passwd','原密码','required|callback_checkpwd');
				$this->form_validation->set_rules('newpwd','新密码','required|min_length[6]');
				$this->form_validation->set_rules('repwd','确认密码','required|matches[newpwd]');
				$res=$this->form_validation->run();
				if($res==false){
					$this->form_validation->set_error_delimiters('<span style="color:red;">','</span>');
					$this->load->view('members/seccenter.html',$data);	
					}else{
							$post=$this->input->post();
							$da['passwd']=md5($post['newpwd']);
							$hid=$post['hid'];
							$this->load->model('Human_model','human');
							$result=$this->human->renew($da,$hid);
							if($result>0){
									$data['msg']='<script type="text/javascript">alert(\'成功修改密码\');window.location.href="'.site_url("usercenter/updatepwd/hid/$hid").'"</script>';	
							}else{
									$data['msg']='<script type="text/javascript">alert(\'未成功修改密码\');window.location.href="'.site_url("usercenter/updatepwd/hid/$hid").'"</script>';	
							}
							$this->load->view('members/seccenter.html',$data);
					}
				
				
			}

		}
		//婚礼团队更新密码
		if(isset($wh['teamid'])){
			$data['pwd']='passwd';
			$data['idname']='teamid';
			$data['idvalue']=$wh['teamid'];
			$data['phone']='phone';
			if($this->input->is_ajax_request()){
				$post=$this->input->post();
				$da['passwd']=md5($post['newpwd']);
				$teamid=$post['teamid'];
				$this->load->model('Team_model','team');
				$result=$this->team->renew($da,$teamid);
				if($result>0){
						echo '1';
				}else{
						echo '0';
				}
				
			}else{
					//print_r($data);
					$this->load->library('form_validation');
					$this->form_validation->set_rules('passwd','原密码','required|callback_checkpwd');
					$this->form_validation->set_rules('newpwd','新密码','required|min_length[6]');
					$this->form_validation->set_rules('repwd','确认密码','required|matches[newpwd]');
					$res=$this->form_validation->run();
					if($res==false){
						$this->form_validation->set_error_delimiters('<span style="color:red;">','</span>');
						$this->load->view('members/seccenter.html',$data);	
						}else{
								$post=$this->input->post();
								$da['passwd']=md5($post['newpwd']);
								$teamid=$post['teamid'];
								$this->load->model('Team_model','team');
								$result=$this->team->renew($da,$teamid);
								if($result>0){
										$data['msg']='<script type="text/javascript">alert(\'成功修改密码\');window.location.href="'.site_url("usercenter/updatepwd/teamid/$teamid").'"</script>';	
								}else{
										$data['msg']='<script type="text/javascript">alert(\'未成功修改密码\');window.location.href="'.site_url("usercenter/updatepwd/teamid/$teamid").'"</script>';	
								}
								$this->load->view('members/seccenter.html',$data);
						}
				
			}
			
		}
		$this->output->enable_profiler(false);
	}
	/*回调函数，用于检查密码*/
	function checkpwd($pwd){
		$post=$this->input->post();
		//print_r($post);
		/*婚庆公司检查密码*/
		if(isset($post['coid'])&&isset($post['copwd'])){
			$this->load->model('Company_model','company');
			$fields=array('coid','copwd');
			$where=array('coid'=>$post['coid']);
			$row=$this->company->fetchone($fields,$where);
			if(!empty($row)){			
				$pwd=$this->input->post('copwd');
				$passwd=md5($pwd);
				if($passwd!=$row[0]['copwd']){
						$this->form_validation->set_message('checkpwd','%s 错误');
						return false;	
					}else{
							return true;
					}
					
			}
		}
		/*婚礼个人检查密码*/
		if(isset($post['hid'])&&isset($post['passwd'])){
			$this->load->model('Human_model','human');
			$fields=array('hid','passwd');
			$where=array('hid'=>$post['hid']);
			$row=$this->human->fetchone($fields,$where);
			if(!empty($row)){			
				$pwd=$this->input->post('passwd');
				$passwd=md5($pwd);
				if($passwd!=$row[0]['passwd']){
						$this->form_validation->set_message('checkpwd','%s 错误');
						return false;	
					}else{
							return true;
					}
					
			}
		}
	}
	/*更改手机号码*/
	function updatephone(){
		$post=$this->input->post();
		//公司更改手机号码
		if(isset($post['coid'])){
			if($this->input->is_ajax_request()){
				$da['cophone']=trim($post['newphone']);
				$coid=$post['coid'];
				$this->load->model('Company_model','company');
				$result=$this->company->renew($da,$coid);
				if($result>0){
					echo '1';
				}else{
					echo '0';
				}
			}
		}
		//个人更改手机号码
		if(isset($post['hid'])){
			if($this->input->is_ajax_request()){
				$da['tellphone']=trim($post['newphone']);
				$hid=$post['hid'];
				$this->load->model('Human_model','human');
				$result=$this->human->renew($da,$hid);
				if($result>0){
					echo '1';
				}else{
					echo '0';
				}
			}
		}
		//团队更改手机号码
		if(isset($post['teamid'])){
			if($this->input->is_ajax_request()){
				$da['phone']=trim($post['newphone']);
				$teamid=$post['teamid'];
				$this->load->model('Team_model','team');
				$result=$this->team->renew($da,$teamid);
				if($result>0){
					echo '1';
				}else{
					echo '0';
				}
			}
		}
	
	}
		/*我的收藏,只有公司和新人存在此模块*/
	function myfavo(){
		$default=array('coid','hid','teamid');
		$arr=$this->uri->uri_to_assoc(3,$default);
		//过滤掉空单元
		$this->load->model('Favorites_model','favorites');
		$wh=$this->favorites->screen($arr);
		if(isset($wh['coid'])){
			$coid=$wh['coid']+0;
			$pagesize=4;
			$offset=intval($this->uri->segment(5));
			//设置查询的字段，五表联查
			$fields=array('favorites.fatime','human.hid','human.hname','human.hlogo','team.teamid','team.tname','team.tlogo','teamlable.tlid','teamlable.tlmark','cate.cname');
			$where=array('favorites.coid'=>$coid);
			$res=$this->favorites->fany($fields,$where,$pagesize,$offset);
			$total=array_pop($res);
			//print_r($res);
			$data['res']=$res;
			//配置分页项目
			$config['base_url']=site_url("usercenter/myfavo/coid/$coid");
			$config['total_rows']=$total;
			$config['per_page']=$pagesize;
			$config['first_link']='首页';
			$config['num_links']=2;
			$config['prev_link']='上一页';
			$config['next_link']='下一页';
			$config['last_link']='尾页';
			$config['uri_segment']=5;
			$config['display_pages']=true;
			$this->load->library('pagination');
			$this->pagination->initialize($config);
			//创建链接
			$data['link']=$this->pagination->create_links();
			$data['total']=$config['total_rows']; 
			$this->load->view('members/collection.html',$data);
		}
		
		$this->output->enable_profiler(false);
	}
	/*发布案例*/
	function addworks(){
		$default=array('coid','hid','teamid');
		$arr=$this->uri->uri_to_assoc(3,$default);
		//print_r($arr);
		$this->load->model('Works_model','works');
		$wh=$this->works->screen($arr);
		//print_r($wh);
		//公司发布案例
		if(isset($wh['coid'])){
			$data['idname']='coid';
			$data['idvalue']=$wh['coid'];
			$this->load->library('form_validation');
			$this->form_validation->set_rules('wname','作品名称','required|callback_checkwname');
			$this->form_validation->set_rules('wintrol','作品简介','required');
			$this->form_validation->set_rules('wprice','作品价格','required|is_natural');
			$this->form_validation->set_rules('wcontent','作品内容','required');
			$res=$this->form_validation->run();
			if($res==false){
				$this->form_validation->set_error_delimiters('<em style="color:red;font-size:12px;">','</em>');
				$this->load->view('members/publicCase.html',$data);
			}else{
				//设置作品封面图片的最大宽度为800px，最大高度为600px，图片文件的最大值为850kb
				$config['max_width']='800';
				$config['max_heifht']='600';
				$config['max_size']='850';
				$config['upload_path']='data/upload/';
				$config['allowed_types']='gif|png|jpg|jpeg';
				$config['remove_spaces']=true;
				$config['encrypt_name']=true;
				$this->load->library('upload',$config);
				$result=$this->upload->do_upload('cover_org');
				if($result==false){
					$errorinfo=$this->upload->display_errors('<em style="color:red;font-size:12px;">','</em>');
					$data['error']=$errorinfo;
					//print_r($data['error']);
					//var_dump($result);
					$this->load->view('members/publicCase.html',$data);
				}else{
						$post=$this->input->post();
						$imginfo=$this->upload->data();
						//print_r($imginfo);echo '<hr>';
						//print_r($post);
						/*生成缩略图 宽300px 高200px*/
						$sourceimg=$imginfo['full_path'];
						$medconf['source_image']=$sourceimg;
						$medconf['new_image']='data/images/'.$imginfo['file_name'];
						$medconf['create_thumb']=true;
						$medconf['quelity']='80';
						$medconf['width']=370;
						$medconf['height']=260;
						$medconf['maintain_ratio']=true;
						$this->load->library('image_lib',$medconf);
						$this->image_lib->resize();
						$wname=$this->input->post('wname',true);
						$wprice=$this->input->post('wprice',true);
						$wintrol=$this->input->post('wintrol',true);
						$da['wname']=trim($wname);
						$da['wprice']=$wprice;
						$da['wintrol']=trim($wintrol);
						$da['wcontent']=$post['wcontent'];
						$da['addtime']=time();
						$da['coid']=$post['coid']+0;
						$da['cover_thumb']=$this->image_lib->full_dst_path;
						$da['cover_org']=$config['upload_path'].$imginfo['file_name'];
						//print_r($da);
						$this->load->model('Works_model','works');
						$result=$this->works->adddata($da);
						if($result){
							$date['idname']=$data['idname'];
							$date['idvalue']=$data['idvalue'];
							$date['msg']='1';
						}else{
							$date['msg']='0';
						}
							$this->load->view('members/publicCase.html',$date);
				}
		
			}
	
			
		}	
		//婚礼人发布案例
		if(isset($wh['hid'])){
			$data['idname']='hid';
			$data['idvalue']=$wh['hid'];
			//print_r($data);//exit;
			$this->load->library('form_validation');
			$this->form_validation->set_rules('wname','作品名称','required|callback_checkwname');
			$this->form_validation->set_rules('wintrol','作品简介','required');
			$this->form_validation->set_rules('wprice','作品价格','required|is_natural');
			$this->form_validation->set_rules('wcontent','作品内容','required');
			$res=$this->form_validation->run();
			if($res==false){
				$this->form_validation->set_error_delimiters('<em style="color:red;font-size:12px;">','</em>');
				$this->load->view('members/publicCase.html',$data);
			}else{
				//设置作品封面图片的最大宽度为800px，最大高度为600px，图片文件的最大值为850kb
				$config['max_width']='800';
				$config['max_heifht']='600';
				$config['max_size']='850';
				$config['upload_path']='data/upload/';
				$config['allowed_types']='gif|png|jpg|jpeg';
				$config['remove_spaces']=true;
				$config['encrypt_name']=true;
				$this->load->library('upload',$config);
				$result=$this->upload->do_upload('cover_org');
				if($result==false){
					$errorinfo=$this->upload->display_errors('<em style="color:red;font-size:12px;">','</em>');
					$data['error']=$errorinfo;
					//print_r($data['error']);
					//var_dump($result);
					$this->load->view('members/publicCase.html',$data);
				}else{
						$post=$this->input->post();
						$imginfo=$this->upload->data();
						//print_r($imginfo);echo '<hr>';
						//print_r($post);
						/*生成缩略图 宽300px 高200px*/
						$sourceimg=$imginfo['full_path'];
						$medconf['source_image']=$sourceimg;
						$medconf['new_image']='data/images/'.$imginfo['file_name'];
						$medconf['create_thumb']=true;
						$medconf['quelity']='80';
						$medconf['width']=370;
						$medconf['height']=260;
						$medconf['maintain_ratio']=true;
						$this->load->library('image_lib',$medconf);
						$this->image_lib->resize();
						$wname=$this->input->post('wname',true);
						$wprice=$this->input->post('wprice',true);
						$wintrol=$this->input->post('wintrol',true);
						$da['wname']=trim($wname);
						$da['wprice']=$wprice;
						$da['wintrol']=trim($wintrol);
						$da['wcontent']=$post['wcontent'];
						$da['addtime']=time();
						$da['hid']=$post['hid']+0;
						$da['cover_thumb']=$this->image_lib->full_dst_path;
						$da['cover_org']=$config['upload_path'].$imginfo['file_name'];
						//print_r($da);
						$this->load->model('Works_model','works');
						$result=$this->works->adddata($da);
						if($result){
							$date['idname']=$data['idname'];
							$date['idvalue']=$data['idvalue'];
							//echo $idname,$idvalue;
							$date['msg']='1';
						}else{
							$date['msg']='0';
						}
							$this->load->view('members/publicCase.html',$date);
				}
		
			}
	
			
		}	
		/*婚礼团队发布案例*/
		if(isset($wh['teamid'])){
			$data['idname']='teamid';
			$data['idvalue']=$wh['teamid'];
			//print_r($data);//exit;
			$this->load->library('form_validation');
			$this->form_validation->set_rules('wname','作品名称','required|callback_checkwname');
			$this->form_validation->set_rules('wintrol','作品简介','required');
			$this->form_validation->set_rules('wprice','作品价格','required|is_natural');
			$this->form_validation->set_rules('wcontent','作品内容','required');
			$res=$this->form_validation->run();
			if($res==false){
				$this->form_validation->set_error_delimiters('<em style="color:red;font-size:12px;">','</em>');
				$this->load->view('members/publicCase.html',$data);
			}else{
				//设置作品封面图片的最大宽度为800px，最大高度为600px，图片文件的最大值为850kb
				$config['max_width']='800';
				$config['max_heifht']='600';
				$config['max_size']='850';
				$config['upload_path']='data/upload/';
				$config['allowed_types']='gif|png|jpg|jpeg';
				$config['remove_spaces']=true;
				$config['encrypt_name']=true;
				$this->load->library('upload',$config);
				$result=$this->upload->do_upload('cover_org');
				if($result==false){
					$errorinfo=$this->upload->display_errors('<em style="color:red;font-size:12px;">','</em>');
					$data['error']=$errorinfo;
					//print_r($data['error']);
					//var_dump($result);
					$this->load->view('members/publicCase.html',$data);
				}else{
						$post=$this->input->post();
						$imginfo=$this->upload->data();
						//print_r($imginfo);echo '<hr>';
						//print_r($post);
						/*生成缩略图 宽300px 高200px*/
						$sourceimg=$imginfo['full_path'];
						$medconf['source_image']=$sourceimg;
						$medconf['new_image']='data/images/'.$imginfo['file_name'];
						$medconf['create_thumb']=true;
						$medconf['quelity']='80';
						$medconf['width']=370;
						$medconf['height']=260;
						$medconf['maintain_ratio']=true;
						$this->load->library('image_lib',$medconf);
						$this->image_lib->resize();
						$wname=$this->input->post('wname',true);
						$wprice=$this->input->post('wprice',true);
						$wintrol=$this->input->post('wintrol',true);
						$da['wname']=trim($wname);
						$da['wprice']=$wprice;
						$da['wintrol']=trim($wintrol);
						$da['wcontent']=$post['wcontent'];
						$da['addtime']=time();
						$da['teamid']=$post['teamid']+0;
						$da['cover_thumb']=$this->image_lib->full_dst_path;
						$da['cover_org']=$config['upload_path'].$imginfo['file_name'];
						//print_r($da);
						$this->load->model('Works_model','works');
						$result=$this->works->adddata($da);
						if($result){
							$date['idname']=$data['idname'];
							$datei['idvalue']=$data['idvalue'];
							//echo $idname,$idvalue;
							$date['msg']='1';
						}else{
							$date['msg']='0';
						}
							$this->load->view('members/publicCase.html',$date);
				}
		
			}
	
			
		}	

		$this->output->enable_profiler(false);
	}
	/*检查作品案例的名称是否重复*/
	function checkwname($wname){
		$wname=trim($wname);
		$this->load->model('Works_model','works');
		$fields='wname';
		$where=array('wname'=>$wname);
		$rs=$this->works->fetchone($fields,$where);
		if(!empty($rs)){
				$this->form_validation->set_message('checkwname','%s 已存在');
				return false;
		}else{
				return true;
		}

	}
	/*案例列表*/
	function workslist(){
		$default=array('coid','hid','teamid');
		$arr=$this->uri->uri_to_assoc(3,$default);
		//print_r($arr);
		$this->load->model('Works_model','works');
		$wh=$this->works->screen($arr);
		//print_r($wh);
		//公司案例列表
		if(isset($wh['coid'])){
			$coid=$wh['coid']+0;
			$pagesize=4;
			$offset=intval($this->uri->segment(5));
			//设置查询的字段，五表联查
			$fields=array('wid','wname','cover_thumb','wprice','addtime');
			$where=array('coid'=>$coid);
			$res=$this->works->wlimit($fields,$where,$pagesize,$offset);
			$total=array_pop($res);
			//print_r($res);
			$data['res']=$res;
			//配置分页项目
			$config['base_url']=site_url("usercenter/workslist/coid/$coid");
			$config['total_rows']=$total;
			$config['per_page']=$pagesize;
			$config['first_link']='首页';
			$config['num_links']=2;
			$config['prev_link']='上一页';
			$config['next_link']='下一页';
			$config['last_link']='尾页';
			$config['uri_segment']=5;
			$config['display_pages']=true;
			$this->load->library('pagination');
			$this->pagination->initialize($config);
			//创建链接
			$data['link']=$this->pagination->create_links();
			$data['total']=$config['total_rows']; 		
			/*构造超链接地址*/
			//控制器名
			$data['controller']='company';
			//方法名
			$data['func']='work';
			//第一个参数的名称
			$data['fieldone']='wid';
			//第二个参数的名称
			$data['fieldtwo']='coid';
			//第二个参数的值
			$data['fieldtwovalue']=$coid;
			//print_r($data['res']);
			$this->load->view('members/workslist.html',$data);
		}
		//婚礼个人案例列表
		if(isset($wh['hid'])){
			$hid=$wh['hid']+0;
			$pagesize=4;
			$offset=intval($this->uri->segment(5));
			//设置查询的字段，五表联查
			$fields=array('wid','wname','cover_thumb','wprice','addtime');
			$where=array('hid'=>$hid);
			$res=$this->works->wlimit($fields,$where,$pagesize,$offset);
			$total=array_pop($res);
			//print_r($res);
			$data['res']=$res;
			//配置分页项目
			$config['base_url']=site_url("usercenter/workslist/hid/$hid");
			$config['total_rows']=$total;
			$config['per_page']=$pagesize;
			$config['first_link']='首页';
			$config['num_links']=2;
			$config['prev_link']='上一页';
			$config['next_link']='下一页';
			$config['last_link']='尾页';
			$config['uri_segment']=5;
			$config['display_pages']=true;
			$this->load->library('pagination');
			$this->pagination->initialize($config);
			//创建链接
			$data['link']=$this->pagination->create_links();
			$data['total']=$config['total_rows']; 		
			$data['controller']='human';
			$data['func']='hwork';
			$data['fieldone']='wid';
			$data['fieldtwo']='hid';
			$data['fieldtwovalue']=$hid;
			//print_r($data['res']);
			$this->load->view('members/workslist.html',$data);
		}
		//婚礼团队案例列表
		if(isset($wh['teamid'])){
			$teamid=$wh['teamid']+0;
			$pagesize=4;
			$offset=intval($this->uri->segment(5));
			//设置查询的字段，五表联查
			$fields=array('wid','wname','cover_thumb','wprice','addtime');
			$where=array('teamid'=>$teamid);
			$res=$this->works->wlimit($fields,$where,$pagesize,$offset);
			$total=array_pop($res);
			//print_r($res);
			$data['res']=$res;
			//配置分页项目
			$config['base_url']=site_url("usercenter/workslist/teamid/$teamid");
			$config['total_rows']=$total;
			$config['per_page']=$pagesize;
			$config['first_link']='首页';
			$config['num_links']=2;
			$config['prev_link']='上一页';
			$config['next_link']='下一页';
			$config['last_link']='尾页';
			$config['uri_segment']=5;
			$config['display_pages']=true;
			$this->load->library('pagination');
			$this->pagination->initialize($config);
			//创建链接
			$data['link']=$this->pagination->create_links();
			$data['total']=$config['total_rows']; 		
			$data['controller']='human';
			$data['func']='twork';
			$data['fieldone']='wid';
			$data['fieldtwo']='teamid';
			$data['fieldtwovalue']=$teamid;
			//print_r($data['res']);
			$this->load->view('members/workslist.html',$data);
		}
		$this->output->enable_profiler(false);
	}
	/*编辑修改案例*/
	function editwork(){
		$wid=$this->uri->segment(3)+0;
		//echo $wid;
		$this->load->model('Works_model','works');
		$data['workinfo']=$this->works->fetchrow(array('wid','wname','cover_thumb','wintrol','wprice','wcontent'),$wid);
		//print_r($data['workinfo']);
		 //配置文件上传的参数
		$config['max_width']='800';
		$config['max_heifht']='600';
		$config['max_size']='850';
		$config['upload_path']='data/upload/';
		$config['allowed_types']='gif|png|jpg|jpeg';
		$config['remove_spaces']=true;
		$config['encrypt_name']=true;
		$this->load->library('upload',$config);
		//执行上传
		$result=$this->upload->do_upload('cover_org');
		//获取上传文件的信息
		$imginfo=$this->upload->data();
		//var_dump($imginfo);exit;
		if($this->input->post()==false){
			$this->load->view('members/editwork.html',$data);
		}else{
			$wid=$this->input->post('wid');
			$dat['wname']=trim($this->input->post('wname'));
			$dat['wintrol']=trim($this->input->post('wintrol'));
			$dat['wprice']=$this->input->post('wprice');
			$dat['wcontent']=trim($this->input->post('wcontent'));
			//$dat['addtime']=time();
			//表单被提交但是没有文件被上传
			if($imginfo['file_name']==''){
				//print_r($dat);exit;
				$rs=$this->works->renew($dat,$wid);
				if($rs>0){
					$data['msg']='1';	
				}else{
					$data['msg']='0';
				}
					$this->load->view('members/editwork.html',$data);
				}else if($result==false){//如果有文件被上传，则判断上传的文件是否符合要求
					$errorinfo=$this->upload->display_errors('<em style="color:red;font-size:12px;">','</em>');
					$data['error']=$errorinfo;
					$this->load->view('members/editwork.html',$data);
					}else{//即提交了表单又上传了文件，则提交全部的数据
					//把图片的相对路径和文件名存入数据库
								$dat['cover_org']=$config['upload_path'].$imginfo['file_name'];
								$sourceimg=$imginfo['full_path'];
								$medconf['source_image']=$sourceimg;
								$medconf['new_image']='data/images/'.$imginfo['file_name'];
								$medconf['create_thumb']=true;
								$medconf['quelity']='80';
								$medconf['width']=370;
								$medconf['height']=260;
								$medconf['maintain_ratio']=true;
								$this->load->library('image_lib',$medconf);
								$this->image_lib->resize();
								//把缩略图的地址插入数据库
								$dat['cover_thumb']=$this->image_lib->full_dst_path;
								
								$rs=$this->works->renew($dat,$wid);
								if($rs>0){
									$data['msg']='1';	
								}else{
									$data['msg']='0';
								}
									$this->load->view('members/editwork.html',$data);
							}
			
		}
		
		
	}
}
?>