<?php
class Teammng extends MY_Controller{
	function addteam(){
		$this->checklogin();
		$this->load->model('Area_model','area');
		$this->load->model('Teamlable_model','tl');
		$this->load->library('form_validation');
		$data['areas']=$this->area->fetchall('aid,amark');
		$data['tls']=$this->tl->fetchall('tlid,tlname');
		//print_r($data);
		/*
			设置验证规则
			callback_* 为自定义的回调函数
		*/
		$this->form_validation->set_rules('tname','团队名称','required|callback_checktname');
		$this->form_validation->set_rules('phone','团队电话','required|callback_checkphone');
		$this->form_validation->set_rules('loginname','登陆账号','required|callback_checkln');
		$this->form_validation->set_rules('passwd','登陆密码','required|min_length[6]');
		$this->form_validation->set_rules('repwd','确认密码','required|matches[passwd]');
		$this->form_validation->set_rules('tintrolvideo','介绍视频地址','required|callback_checkvideo');
		$this->form_validation->set_rules('tintrol','团队简介','required');
        $res=$this->form_validation->run();
		if($res==false){
			$this->form_validation->set_error_delimiters('<span>','</span>');
			$this->load->view('admin/addteam.html',$data);
		}else{
			//设置公司logo图片的最大宽度为140px，最大高度为140px，图片文件的最大值为20kb
			$config['max_width']='160';
			$config['max_heifht']='160';
			$config['max_size']='30';
			$config['upload_path']='data/upload/';
			$config['allowed_types']='gif|png|jpg|jpeg';
			$config['remove_spaces']=true;
			$config['encrypt_name']=true;
			$this->load->library('upload',$config);
			$result=$this->upload->do_upload('tlogo');
			if($result==false){
				$errorinfo=$this->upload->display_errors('<span>','</span>');
				$data['error']=$errorinfo;
				$this->load->view('admin/addteam.html',$data);
			}else{
			$post=$this->input->post();
			$imginfo=$this->upload->data();
			//print_r($post);exit;
			//echo '<hr>';
			//print_r($imginfo);exit;
			$dat['tname']=trim($post['tname']);
			$dat['phone']=trim($post['phone']);
			$dat['loginname']=trim($post['loginname']);
			$dat['passwd']=md5($post['passwd']);
			$dat['ischeck']=$post['ischeck'];
			$dat['aid']=$post['aid'];
			$dat['tlid']=$post['tlid'];
			$dat['isrecom']=$post['isrecom'];
			$dat['isexec'] = $post['isexec'];
			$dat['level']=$post['level'];
			$dat['tintrolvideo']=trim($post['tintrolvideo']);
			$dat['tintrol']=trim($post['tintrol']);
			//把图片的相对路径和文件名存入数据库
			$dat['tlogo']=$config['upload_path'].$imginfo['file_name'];
			$this->load->model('Team_model','team');
			$qs=$this->team->adddata($dat);
			if($qs){
					$da['msg']='成功注册公司';
				}else{
					$da['msg']='注册失败';
					}
			$this->load->view('admin/msg.html',$da);
		
			}

		}

	}
function checktname($tname){
			$tname=trim($tname);
			$this->load->model('Team_model','team');
			$fields='tname';
			$where=array('tname'=>$tname);
			$row=$this->team->fetchone($fields,$where);
			if(!empty($row)){
				/*设置错误提示*/
				$this->form_validation->set_message('checktname','%s 已存在');
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
function checkln($loginname){
			$loginname=trim($loginname);
			$this->load->model('Team_model','team');
			$fields='loginname';
			$where=array('loginname'=>$loginname);
			$row=$this->team->fetchone($fields,$where);
			if(!empty($row)){
				/*设置错误提示*/
				$this->form_validation->set_message('checkln','%s 已存在');
				return false;
				}else{
						return true;
						}
	}

 function checkvideo($tintrolvideo){
		$tintrolvideo=trim($tintrolvideo);
		//http://www.tudou.com/v/Qyiy4Lomy_4/&resourceId=0_04_05_99/v.swf
		$rs=preg_match('/^(http:\/\/)\w*\.\w+/',$tintrolvideo);
		//var_dump($rs);
		if($rs==false){
			$this->form_validation->set_message('checkvideo','%s 格式不正确');
			return false;
		}else{
			return true;
		}

	 }
 function teamlist(){
	$this->checklogin();
	$this->load->model('Team_model','team');
	$this->load->model('Area_model','area');
	$this->load->model('Teamlable_model','tl');
	$data['areas']=$this->area->fetchall('aid,amark');		 
	$data['tls']=$this->tl->fetchall('tlid,tlmark');		
	 //加载分页类
	$this->load->library('pagination');
	$post=$this->input->post();
	//var_dump($post);
	//var_dump(isset($post));
	/*判断是否提交查询条件，若无提交或查询条件为空则查询全部的数据并分页显示
		如果提交了查询条件则以多条件复合查询处理
	*/
	if($post==false||($post['aid']==0&&$post['tname']==''&&$post['phone']==''&&$post['tlid']==0)){ 
			$pagesize=1;
			//获取偏移量
			$offset=intval($this->uri->segment(4));
			$arrays=$this->team->fetchlimit($pagesize,$offset);
			//print_r($arrays);
			 //把总行数传入分页类
			$config['total_rows']=$this->team->getnums();	
			//配置分页项目
			  $config['per_page']=$pagesize;
			  $config['base_url']=site_url('admin/teammng/teamlist');
			  $config['first_link']='<<';
			  $config['num_links']=2;
			  $config['prev_link']='<';
			  $config['next_link']='>';
			  $config['last_link']='>>';
			  $config['uri_segment']=4;
			  $config['display_pages']=true;
			  $this->pagination->initialize($config);
			 //创建链接
			  $data['link']=$this->pagination->create_links();
			  $data['total']=$config['total_rows']; 
		}else{
					/*多条件复合查询*/
					$wh['team.aid']=$post['aid'];
					$wh['team.tlid']=$post['tlid'];
					$wh['team.tname']=trim($post['tname']);
					$wh['team.phone']=trim($post['phone']);
					//print_r($post);
					//过滤掉where数组中的空的单元
					$where=$this->team->screen($wh);
					//print_r($where);
					$arrays=$this->team->fetchtat($where);
					//print_r($array);
		}
			$data['res']=$arrays;
			$this->output->enable_profiler(false);
			$this->load->view('admin/teamlist.html',$data);	
	  }
	
	function editteam(){
		$this->checklogin();
		$this->load->model('Area_model','area');
		$this->load->model('Team_model','team');				
		$this->load->model('Teamlable_model','tl');				
		$teamid=$this->uri->segment(4);
		$fields='teamid,tname,tintrolvideo,tlogo,phone,tintrol,isexec, level,volume,ischeck,isrecom,aid,tlid';
		$data['cs']=$this->team->fetchrow($fields,$teamid);
		$data['areas']=$this->area->fetchall('aid,amark');
		$data['tls']=$this->tl->fetchall('tlid,tlname');
		//配置文件上传的参数
		$config['max_width']='160';
		$config['max_heifht']='160';
		$config['max_size']='30';
		$config['upload_path']='data/upload/';
		$config['allowed_types']='gif|png|jpg|jpeg';
		$config['remove_spaces']=true;
		$config['encrypt_name']=true;
		$this->load->library('upload',$config);
		//执行上传
		$result=$this->upload->do_upload('tlogo');
		//获取上传文件的信息
		$imginfo=$this->upload->data();
		//var_dump($imginfo);exit;
		//判断是否提交编辑表单，如果没提交则显示编辑信息表单
		$post=$this->input->post();	
		if($post==false){
			$this->load->view('admin/editteam.html',$data);
		}else{
		$teamid=$post['teamid'];
		$dat['tname']=trim($post['tname']);
		$dat['volume']=$post['volume'];
		$dat['phone']=trim($post['phone']);
		$dat['ischeck']=$post['ischeck'];
		$dat['aid']=$post['aid'];
		$dat['tlid']=$post['tlid'];
		$dat['isrecom']=$post['isrecom'];
		$dat['isexec']=$post['isexec'];
		$dat['level']=$post['level'];
		$dat['tintrolvideo']=trim($post['tintrolvideo']);
		$dat['tintrol']=trim($post['tintrol']);
		//表单被提交但是没有文件被上传
		if($imginfo['file_name']==''){
			$rs=$this->team->renew($dat,$teamid);
				if($rs>0){
					$da['msg']='成功修改公司信息';	
				}else{
					$da['msg']='未修改公司信息';
				}
					$this->load->view('admin/msg.html',$da);
			}else if($result==false){//如果有文件被上传，则判断上传的文件是否符合要求
				$errorinfo=$this->upload->display_errors('<span>','</span>');
				$data['error']=$errorinfo;
				$this->load->view('admin/editteam.html',$data);
			}else{//即提交了表单又上传了文件，则提交全部的数据
					//把图片的相对路径和文件名存入数据库
				$dat['tlogo']=$config['upload_path'].$imginfo['file_name'];
				$rs=$this->team->renew($dat,$teamid);
				if($rs>0){
					$da['msg']='成功修改公司信息';	
				}else{
					$da['msg']='未修改公司信息';
				}
					$this->load->view('admin/msg.html',$da);
			}
		}
	
	}
	function edittpwd(){
		$this->checklogin();
		$teamid=$this->uri->segment(4);
		$this->load->model('Team_model','team');
		$lg=$this->team->fetchrow('teamid,tname,loginname',$teamid);
		//print_r($lg);
		$data['rs']=$lg;
		$this->load->library('form_validation');
		$this->form_validation->set_rules('passwd','新密码','required|min_length[6]');
		$this->form_validation->set_rules('repwd','确认密码','required|matches[passwd]');
		$res=$this->form_validation->run();
		if($res==false){
				$this->form_validation->set_error_delimiters('<span>','</span>');
				$this->load->view('admin/edittpwd.html',$data);	
		}else{
				$post=$this->input->post();
				$da['passwd']=md5($post['passwd']);
				$teamid=$post['teamid'];
				$result=$this->team->renew($da,$teamid);
				if($result>0){
						$d['msg']='成功修改密码';
				}else{
						$d['msg']='未修改密码';
				}
				$this->load->view('admin/msg.html',$d);
		}

	}
/*发布作品*/
function addworks(){
		$this->checklogin();
		$teamid=$this->uri->segment(4);
		$data['id']=$teamid;
		$data['controller']='team';
		$data['field']='teamid';
		$this->load->library('form_validation');
		$this->form_validation->set_rules('wname','作品名称','required|callback_checkwname');
		$this->form_validation->set_rules('wintrol','作品简介','required');
		$this->form_validation->set_rules('wprice','作品价格','required|is_natural');
		$this->form_validation->set_rules('wcontent','作品内容','required');
		$res=$this->form_validation->run();
		if($res==false){
			$this->form_validation->set_error_delimiters('<span>','</span>');
			$this->load->view('admin/addworks.html',$data);
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
				$errorinfo=$this->upload->display_errors('<span>','</span>');
				$data['error']=$errorinfo;
				$this->load->view('admin/addworks.html',$data);
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
				$medconf['quelity']='70';
				$medconf['width']=300;
				$medconf['height']=200;
				$medconf['maintain_ratio']=true;
				$this->load->library('image_lib',$medconf);
				$this->image_lib->resize();
				$da['wname']=trim($post['wname']);
				$da['wprice']=$post['wprice'];
				$da['wintrol']=trim($post['wintrol']);
				$da['wcontent']=$post['wcontent'];
				$da['addtime']=time();
				$da['teamid']=$post['teamid']+0;
				$da['cover_thumb']=$this->image_lib->full_dst_path;
				$da['cover_org']=$config['upload_path'].$imginfo['file_name'];
				//print_r($da);
				$this->load->model('Works_model','works');
				$result=$this->works->adddata($da);
				if($result){
					$d['msg']='发布成功';
				}else{
					$d['msg']='发布失败';
				}
					$this->load->view('admin/msg.html',$d);
			}
	
		}
	}
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

}
?>