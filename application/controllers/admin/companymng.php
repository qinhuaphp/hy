<?php
class Companymng extends MY_Controller{
	function addcompany(){
		$this->checklogin();
		$this->load->model('Area_model','area');
		$this->load->library('form_validation');
		$data['areas']=$this->area->fetchall('aid,amark');
		//print_r($data['areas']);
		/*
			设置验证规则
			callback_* 为自定义的回调函数
		*/
		$this->form_validation->set_rules('coname','公司名称','required|callback_checkconame');
		$this->form_validation->set_rules('coaccount','银行账户','required|is_natural|min_length[16]|max_length[19]');
		$this->form_validation->set_rules('colocation','公司地址','required');
		$this->form_validation->set_rules('cophone','公司电话','required|callback_checkcophone');
		$this->form_validation->set_rules('style', '公司风格', 'required');
		$this->form_validation->set_rules('year', '年份', 'required');
		$this->form_validation->set_rules('month', '月份', 'required');
		$this->form_validation->set_rules('day', '日期', 'required');
		$this->form_validation->set_rules('loginname','登陆账号','required|callback_checkln');
		$this->form_validation->set_rules('copwd','登陆密码','required|min_length[6]');
		$this->form_validation->set_rules('repwd','确认密码','required|matches[copwd]');
		$this->form_validation->set_rules('cointrolvideo','介绍视频地址','required|callback_checkvideo');
		$this->form_validation->set_rules('cointrol','公司简介','required');
	
		$res=$this->form_validation->run();
		if($res==false){
			$this->form_validation->set_error_delimiters('<span>','</span>');
			$this->load->view('admin/addcompany.html',$data);
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
			$result=$this->upload->do_upload('cologo');
			if($result==false){
				$errorinfo=$this->upload->display_errors('<span>','</span>');
				$data['error']=$errorinfo;
				$this->load->view('admin/addcompany.html',$data);
			}else{
			$post=$this->input->post();
			$imginfo=$this->upload->data();
			//print_r($post);exit;
			//echo '<hr>';
			//print_r($imginfo);exit;
			$dat['coname']=trim($post['coname']);
			$dat['coaccount']=trim($post['coaccount']);
			$dat['colocation']=trim($post['colocation']);
			$dat['cophone']=trim($post['cophone']);
			$dat['loginname']=trim($post['loginname']);
			$dat['copwd']=md5($post['copwd']);
			$dat['isaudit']=$post['isaudit'];
			$dat['aid']=$post['aid'];
			$dat['isrecom']=$post['isrecom'];
			$dat['level']=$post['level'];
			$dat['cointrolvideo']=trim($post['cointrolvideo']);
			$dat['cointrol']=trim($post['cointrol']);
			$dat['coregtime']=time();
			$dat['style'] = trim($post['style']);
			$year = (string) $post['year'];
			$month = (string) $post['month'];
			$day = (string) $post['day'];
            $dat['esttime'] = $year.'/'.$month.'/'.$day;
           //print_r($dat);exit();
			//把图片的相对路径和文件名存入数据库
			$dat['cologo']=$config['upload_path'].$imginfo['file_name'];
			$this->load->model('Company_model','company');
			$qs=$this->company->adddata($dat);
			if($qs){
					$da['msg']='成功注册公司';
				}else{
					$da['msg']='注册失败';
					}
			$this->load->view('admin/msg.html',$da);
		
			}

		}

	}
	function checkconame($coname){
			$coname=trim($coname);
			$this->load->model('Company_model','company');
			$fields='coname';
			$where=array('coname'=>$coname);
			$row=$this->company->fetchone($fields,$where);
			if(!empty($row)){
				/*设置错误提示*/
				$this->form_validation->set_message('checkconame','%s 已存在');
				return false;
				}else{
						return true;
						}
	}
	function checkcophone($cophone){
			$cophone=trim($cophone);
			$rs=preg_match('/^18(\d{9})$|^15(\d{9})$|^13(\d{9})$|^147(\d{8})$|^0(\d{3,4})-(\d{7,8})$/',$cophone);
			if($rs==false){
				$this->form_validation->set_message('checkcophone','%s 格式不正确');
				return false;
			}else{
				$this->load->model('Company_model','company');
				$fields='cophone';
				$where=array('cophone'=>$cophone);
				$row=$this->company->fetchone($fields,$where);
				if(!empty($row)){
					/*设置错误提示*/
					$this->form_validation->set_message('checkcophone','%s 已存在');
					return false;
					}else{
							return true;
							}
			}
	}
	function checkln($loginname){
			$loginname=trim($loginname);
			$this->load->model('Company_model','company');
			$fields='loginname';
			$where=array('loginname'=>$loginname);
			$row=$this->company->fetchone($fields,$where);
			if(!empty($row)){
				/*设置错误提示*/
				$this->form_validation->set_message('checkln','%s 已存在');
				return false;
				}else{
						return true;
						}
	}
	 function checkvideo($cointrolvideo){
		$cointrolvideo=trim($cointrolvideo);
		//http://www.tudou.com/v/Qyiy4Lomy_4/&resourceId=0_04_05_99/v.swf
		$rs=preg_match('/(http:\/\/)[\w\/-]+[\bv\.swf\b\.\/-]+/',$cointrolvideo);
		//var_dump($rs);
		if($rs==false){
			$this->form_validation->set_message('checkvideo','%s 格式不正确');
			return false;
		}else{
			return true;
		}

	 }
	function companylist(){
		$this->checklogin();
		$this->load->model('Company_model','company');
		  $this->load->model('Area_model','area');
		  $data['areas']=$this->area->fetchall('aid,amark');		 
		  
		 //加载分页类
		  $this->load->library('pagination');
		  $post=$this->input->post();
		 //var_dump($post);
		  //var_dump(isset($post));
		  /*判断是否提交查询条件，若无提交或查询条件为空则查询全部的数据并分页显示
			如果提交了查询条件则以多条件复合查询处理
		  */
		if($post==false||($post['aid']==0&&$post['coname']==''&&$post['cophone']=='')){ 
			$pagesize=2;
			//获取偏移量
		  $offset=intval($this->uri->segment(4));
			$arrays=$this->company->fetchlimit($pagesize,$offset);
			// print_r($array);
			 //echo $arr;
			//查询每行数据中的cid对应的栏目名称
			//$arrays=$this->company->com_area($array);
			//print_r($arrays);
			 //把总行数传入分页类
			$config['total_rows']=$this->company->getnums();	
			//配置分页项目
			  $config['per_page']=$pagesize;
			  $config['base_url']=site_url('admin/companymng/companylist');
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
					$wh['company.aid']=$post['aid'];
					$wh['coname']=trim($post['coname']);
					$wh['cophone']=trim($post['cophone']);
					//print_r($post);
					//过滤掉where数组中的空的单元
					$where=$this->company->screen($wh);
					//print_r($where);
					$arrays=$this->company->com_area($where);
					//print_r($array);
		}
				//查询每行数据中的cid对应的栏目名称
				//$arrays=$this->company->com_area($array);
				//print_r($arrays);
				$data['res']=$arrays;
				$this->output->enable_profiler(false);
		  $this->load->view('admin/companylist.html',$data);
	
	}
	/*文件上传和编辑信息的表单结合在一起时，先判断是否提交表单，若未提交则显示编辑表单
		若提交了表单则有文件上传验证文件是否符合要求，若不符合要求则返回原界面
		若提交了表单但是没上传文件则不更新文件上传的字段
		若提交了表单也有文件上传则全部更新
	*/
	function editcompany(){	
		$this->checklogin();
		$this->load->model('Area_model','area');
		$this->load->model('Company_model','company');				
		$coid=$this->uri->segment(4);
		$fields='coid,coname,cointrolvideo,cologo,colocation,cophone,cointrol,style, esttime, level,volume,isaudit,isrecom,aid,coaccount';
		$data['cs']=$this->company->fetchrow($fields,$coid);
		$data['areas']=$this->area->fetchall('aid,amark');
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
		$result=$this->upload->do_upload('cologo');
		//获取上传文件的信息
		$imginfo=$this->upload->data();
		//var_dump($imginfo);exit;
		//判断是否提交编辑表单，如果没提交则显示编辑信息表单
		$post=$this->input->post();	
		if($post==false){
			$this->load->view('admin/editcompany.html',$data);
            
		}else{
		$coid=$post['coid'];
		$dat['coname']=trim($post['coname']);
		$dat['volume']=$post['volume'];
		$dat['coaccount']=trim($post['coaccount']);
		$dat['colocation']=trim($post['colocation']);
		$dat['cophone']=trim($post['cophone']);
		$dat['isaudit']=$post['isaudit'];
		$dat['aid']=$post['aid'];
		$dat['isrecom']=$post['isrecom'];
		$dat['level']=$post['level'];
		$dat['cointrolvideo']=trim($post['cointrolvideo']);
		$dat['cointrol']=trim($post['cointrol']);
		//表单被提交但是没有文件被上传
		if($imginfo['file_name']==''){
			$rs=$this->company->renew($dat,$coid);
				if($rs>0){
					$da['msg']='成功修改公司信息';	
				}else{
					$da['msg']='未修改公司信息';
				}
					$this->load->view('admin/msg.html',$da);
			}else if($result==false){//如果有文件被上传，则判断上传的文件是否符合要求
				$errorinfo=$this->upload->display_errors('<span>','</span>');
				$data['error']=$errorinfo;
				$this->load->view('admin/editcompany.html',$data);
			}else{//即提交了表单又上传了文件，则提交全部的数据
					//把图片的相对路径和文件名存入数据库
				$dat['cologo']=$config['upload_path'].$imginfo['file_name'];
				$rs=$this->company->renew($dat,$coid);
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
	function editcopwd(){
		$this->checklogin();
		$coid=$this->uri->segment(4);
		$this->load->model('Company_model','company');
		$lg=$this->company->fetchrow('coid,coname,loginname',$coid);
		//print_r($lg);
		$data['rs']=$lg;
		$this->load->library('form_validation');
		$this->form_validation->set_rules('copwd','新密码','required|min_length[6]');
		$this->form_validation->set_rules('repwd','确认密码','required|matches[copwd]');
		$res=$this->form_validation->run();
		if($res==false){
				$this->form_validation->set_error_delimiters('<span>','</span>');
				$this->load->view('admin/editcopwd.html',$data);	
		}else{
				$post=$this->input->post();
				$da['copwd']=md5($post['copwd']);
				$coid=$post['coid'];
				$result=$this->company->renew($da,$coid);
				if($result>0){
						$d['msg']='成功修改密码';
				}else{
						$d['msg']='未修改密码';
				}
				$this->load->view('admin/msg.html',$d);
		}
		
	}
	/*添加作品*/
	function addworks(){
		$this->checklogin();
		$coid=$this->uri->segment(4);
		$data['id']=$coid;
		$data['controller']='company';
		$data['field']='coid';
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
				$da['coid']=$post['coid']+0;
				$da['cover_thumb']=$this->image_lib->full_dst_path;
				$da['cover_org']=$config['upload_path'].$imginfo['file_name'];
				//print_r($da);
				$this->load->model('Works_model','work');
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