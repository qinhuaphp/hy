<?php
class Humanmng extends MY_Controller{
	function addhuman(){
		$this->checklogin();
		$this->load->model('Area_model','area');
		$this->load->model('Cate_model','cate');
		$this->load->model('Team_model','team');
		$this->load->library('form_validation');
		$data['areas']=$this->area->fetchall('aid,amark');
		$data['cates']=$this->cate->getson(14);
		$data['teams']=$this->team->fetchall('teamid,tname');
		//print_r($data['areas']);
		/*
			设置验证规则
			callback_* 为自定义的回调函数
		*/
		$this->form_validation->set_rules('hname','真实名称','required');
		$this->form_validation->set_rules('haccount','银行账户','required|is_natural|min_length[16]|max_length[19]|callback_checkacc');
		$this->form_validation->set_rules('hprice','个人报价','required');
		$this->form_validation->set_rules('tellphone','手机号码','required|callback_checkphone');
		$this->form_validation->set_rules('loginname','登陆账号','required|callback_checkln');
		$this->form_validation->set_rules('passwd','登陆密码','required|min_length[6]');
		$this->form_validation->set_rules('repwd','确认密码','required|matches[passwd]');
		$this->form_validation->set_rules('age','年龄','required');
		$this->form_validation->set_rules('hintrolvideo','介绍视频地址','required|callback_checkvideo');
		$this->form_validation->set_rules('hintrol','公司简介','required');
		$res=$this->form_validation->run();
		if($res==false){
			$this->form_validation->set_error_delimiters('<span>','</span>');
			$this->load->view('admin/addhuman.html',$data);
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
					$result=$this->upload->do_upload('hlogo');
					if($result==false){
						$errorinfo=$this->upload->display_errors('<span>','</span>');
						$data['error']=$errorinfo;
						$this->load->view('admin/addhuman.html',$data);
					}else{
					$post=$this->input->post();
					$imginfo=$this->upload->data();
					//print_r($post);exit;
					//echo '<hr>';
					//print_r($imginfo);
					$dat['hname']=trim($post['hname']);
					$dat['haccount']=trim($post['haccount']);
					$dat['hprice']=trim($post['hprice']);
					$dat['tellphone']=trim($post['tellphone']);
					$dat['loginname']=trim($post['loginname']);
					$dat['passwd']=md5($post['passwd']);
					$dat['ischeck']=$post['ischeck'];
					$dat['aid']=$post['aid'];
					$dat['cid']=$post['cid'];
					$dat['teamid']=$post['teamid'];
					$dat['isrecom']=$post['isrecom'];
					$dat['level']=$post['level'];
					$dat['hintrolvideo']=trim($post['hintrolvideo']);
					$dat['hintrol']=trim($post['hintrol']);
					$dat['sex']=$post['sex'];
					$dat['teamid']=$post['teamid'];
					$dat['age'] = trim($post['age']);
                    //print_r($dat);exit();
					//把图片的相对路径和文件名存入数据库
					$dat['hlogo']=$config['upload_path'].$imginfo['file_name'];
					$this->load->model('Human_model','human');
					$qs=$this->human->adddata($dat);
					if($qs){
							$da['msg']='成功注册个人';
					}else{
							$da['msg']='注册失败';
					}
					$this->load->view('admin/msg.html',$da);
				
				}

		}

	}

	function checkphone($tellphone){
			$tellphone=trim($tellphone);
			$rs=preg_match('/^18(\d{9})$|^12(\d{9})$|^17(\d{9})$|^15(\d{9})$|^13(\d{9})$|^147(\d{8})$/',$tellphone);
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
	function checkln($loginname){
			$loginname=trim($loginname);
			$this->load->model('Human_model','human');
			$fields='loginname';
			$where=array('loginname'=>$loginname);
			$row=$this->human->fetchone($fields,$where);
			if(!empty($row)){
				/*设置错误提示*/
				$this->form_validation->set_message('checkln','%s 已存在');
				return false;
				}else{
						return true;
						}
	}
	function checkvideo($hintrolvideo){
		$hintrolvideo=trim($hintrolvideo);
		//http://www.tudou.com/v/Qyiy4Lomy_4/&resourceId=0_04_05_99/v.swf
		$rs=preg_match('/^(http:\/\/)\w*\.\w+/',$hintrolvideo);
		//var_dump($rs);
		if($rs==false){
			$this->form_validation->set_message('checkvideo','%s 格式不正确');
			return false;
		}else{
			return true;
		}

	 }
 function checkacc($haccount){
			$haccount=trim($haccount);
			$this->load->model('Human_model','human');
			$fields='haccount';
			$where=array('haccount'=>$haccount);
			$row=$this->human->fetchone($fields,$where);
			if(!empty($row)){
				/*设置错误提示*/
				$this->form_validation->set_message('checkacc','%s 已存在');
				return false;
				}else{
						return true;
						}


	}
 function humanlist(){
		$this->checklogin();
		  $this->load->model('Human_model','human');
		  $this->load->model('Area_model','area');
		  $this->load->model('Cate_model','cate');
		  $this->load->model('Team_model','team');
		  $data['areas']=$this->area->fetchall('aid,amark');		
		  $data['cates']=$this->cate->getson(14);
		  $data['teams']=$this->team->fetchall('teamid,tname');
		 //加载分页类
		  $this->load->library('pagination');
		  $post=$this->input->post();
		 //var_dump($post);
		  //var_dump(isset($post));
		  /*判断是否提交查询条件，若无提交或查询条件为空则查询全部的数据并分页显示
			如果提交了查询条件则以多条件复合查询处理
		  */
		if($post==false||($post['hname']==''&&$post['tellphone']==''&&$post['aid']==0&&$post['aid']==0&&$post['teamid']==0&&$post['ischeck']=='')){ 
			$pagesize=20;
			//获取偏移量
		  $offset=intval($this->uri->segment(4));
			$arrays=$this->human->fetchhaclimit($pagesize,$offset);
			//print_r($arrays);exit;
			 //把总行数传入分页类
			$config['total_rows']=$this->human->getnums();	
			//配置分页项目
			  $config['per_page']=$pagesize;
			  $config['base_url']=site_url('admin/humanmng/humanlist');
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
					$wh['human.aid']=$post['aid'];
					$wh['human.cid']=$post['cid'];
					$wh['human.teamid']=$post['teamid'];
					$wh['hname']=trim($post['hname']);
					$wh['tellphone']=trim($post['tellphone']);
					//过滤掉where数组中的空的单元
					$where=$this->human->screen($wh);
					//print_r($post['ischeck']);
					if($post['ischeck']!=''){
						$whe['human.ischeck']=$post['ischeck'];
						$where=array_merge($whe,$where);
					}
					//print_r($where);
					$arrays=$this->human->fetchsom($where);
					//print_r($array);
		}
				$data['res']=$arrays;
			/*在页面的底部显示程序的执行状态*/
		$this->output->enable_profiler(false);
		  $this->load->view('admin/humanlist.html',$data);
  }
  function edithuman(){
	  $this->checklogin();
		  $hid=$this->uri->segment(4);
		  $this->load->model('Human_model','human');
		  $this->load->model('Area_model','area');
		  $this->load->model('Cate_model','cate');
		  $this->load->model('Team_model','team');
		  $data['areas']=$this->area->fetchall('aid,amark');
		  $data['cates']=$this->cate->getson(14);
		  $data['teams']=$this->team->fetchall('teamid,tname');
		  $fields='hid,hname,haccount,hprice,tellphone,level,age, volume,sex,ischeck,isrecom,aid,cid,teamid,hintrolvideo,hintrol,hlogo';
         $data['cs']=$this->human->fetchrow($fields,$hid);
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
		$result=$this->upload->do_upload('hlogo');
		//获取上传文件的信息
		$imginfo=$this->upload->data();
		//var_dump($imginfo);exit;
		$post=$this->input->post();	
		if($post==false){
			$this->load->view('admin/edithuman.html',$data);
		}else{
		$hid=$post['hid'];
		$dat['hname']=trim($post['hname']);
		$dat['volume']=$post['volume'];
		$dat['haccount']=trim($post['haccount']);
		$dat['sex']=trim($post['sex']);
		$dat['tellphone']=trim($post['tellphone']);
		$dat['ischeck']=$post['ischeck'];
		$dat['aid']=$post['aid'];
		$dat['cid']=$post['cid'];
        $dat['age']=$post['age'];
		$dat['hprice']=$post['hprice'];
		$dat['isrecom']=$post['isrecom'];
		$dat['level']=$post['level'];
		$dat['hintrolvideo']=trim($post['hintrolvideo']);
		$dat['hintrol']=trim($post['hintrol']);
		$dat['teamid']=$post['teamid'];
		//表单被提交但是没有文件被上传
		if($imginfo['file_name']==''){
			$rs=$this->human->renew($dat,$hid);
				if($rs>0){
					$da['msg']='成功修改公司信息';	
				}else{
					$da['msg']='未修改公司信息';
				}
					$this->load->view('admin/msg.html',$da);
			}else if($result==false){//如果有文件被上传，则判断上传的文件是否符合要求
				$errorinfo=$this->upload->display_errors('<span>','</span>');
				$data['error']=$errorinfo;
				$this->load->view('admin/edithuman.html',$data);
			}else{//即提交了表单又上传了文件，则提交全部的数据
					//把图片的相对路径和文件名存入数据库
				$dat['hlogo']=$config['upload_path'].$imginfo['file_name'];
				$rs=$this->human->renew($dat,$hid);
				if($rs>0){
					$da['msg']='成功修改公司信息';	
				}else{
					$da['msg']='未修改公司信息';
				}
					$this->load->view('admin/msg.html',$da);
			}
	}
  }
  /*修改密码*/
  function edithpwd(){
	  $this->checklogin();
		$hid=$this->uri->segment(4);
		$this->load->model('Human_model','human');
		$lg=$this->human->fetchrow('hid,hname,loginname',$hid);
		//print_r($lg);
		$data['rs']=$lg;
		$this->load->library('form_validation');
		$this->form_validation->set_rules('passwd','新密码','required|min_length[6]');
		$this->form_validation->set_rules('repwd','确认密码','required|matches[passwd]');
		$res=$this->form_validation->run();
		if($res==false){
				$this->form_validation->set_error_delimiters('<span>','</span>');
				$this->load->view('admin/edithpwd.html',$data);	
		}else{
				$post=$this->input->post();
				$da['passwd']=md5($post['passwd']);
				$hid=$post['hid'];
				$result=$this->human->renew($da,$hid);
				if($result>0){
						$d['msg']='成功修改密码';
				}else{
						$d['msg']='未修改密码';
				}
				$this->load->view('admin/msg.html',$d);
		}
		
	}
/*添加个人作品*/
function addworks(){
	$this->checklogin();
		$hid=$this->uri->segment(4);
		$data['id']=$hid;
		$data['controller']='human';
		$data['field']='hid';
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
				$medconf['quelity']='80';
				$medconf['width']=365;
				$medconf['height']=265;
				$medconf['maintain_ratio']=true;
				$this->load->library('image_lib',$medconf);
				$this->image_lib->resize();
				$da['wname']=trim($post['wname']);
				$da['wprice']=$post['wprice'];
				$da['wintrol']=trim($post['wintrol']);
				$da['wcontent']=$post['wcontent'];
				$da['addtime']=time();
				$da['hid']=$post['hid']+0;
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