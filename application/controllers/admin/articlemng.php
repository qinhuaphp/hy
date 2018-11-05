<?php
class Articlemng extends MY_Controller{
	function addarticle(){
		$this->checklogin();
		$this->load->model('Lable_model','lable');
		$this->load->model('Cate_model','cate');
		$data['lables']=$this->lable->getLable();
		$data['cates']=$this->cate->gettree();
		
		$this->output->enable_profiler(false);
		$this->load->library('form_validation');
		/*
			设置文章标题的验证规则为非空或不能重复，
			callback_checktitle 为自定义的回调函数
		*/
		$this->form_validation->set_rules('artitle','文章标题','required|callback_checktitle');
		$this->form_validation->set_rules('keyword','关键字','required');
		$this->form_validation->set_rules('ardesc','摘要','required');
		$this->form_validation->set_rules('content','内容','required');
		$res=$this->form_validation->run();
		if($res==false){
			$this->form_validation->set_error_delimiters('<span>','</span>');
			$this->load->view('admin/addarticle.html',$data);
			}else{
				//设置文章封面原始图片的最大宽度为370px，最大高度为260px，图片文件的最大值为200kb
					$config['max_width']='370';
					$config['max_heifht']='260';
					$config['max_size']='200';
					$config['upload_path']='data/upload/';
					$config['allowed_types']='gif|png|jpg|jpeg';
					$config['remove_spaces']=true;
					$config['encrypt_name']=true;
					$this->load->library('upload',$config);
					$result=$this->upload->do_upload('convertitle');
					if($result==false){
						$errorinfo=$this->upload->display_errors('<span>','</span>');
						$data['error']=$errorinfo;
						$this->load->view('admin/addarticle.html',$data);
					}else if($result){
							$imginfo=$this->upload->data();
							
							$da['artitle']=trim($this->input->post('artitle'));
							$da['keyword']=trim($this->input->post('keyword'));
							$da['author']=trim($this->input->post('author'));
							$da['author']=trim($this->input->post('author'));
							$da['ardesc']=trim($this->input->post('ardesc'));
							$da['cid']=trim($this->input->post('cid'));
							$da['ishot']=trim($this->input->post('ishot'));
							$da['isrecom']=trim($this->input->post('isrecom'));
							$da['isclassic']= $this->input->post('isclassic');
                            if ($da['cid'] == 1) {
                                $laid = $this->input->post('a_laid');
                                $laid = implode(",", $laid);
                            }
                            if ($da['cid'] == 36){
                                $laid = $this->input->post('a_laid').','.$this->input->post('b_laid').','.$this->input->post('c_laid');
                            }
                            if ($da['cid'] == 37) {
                                $laid = 45;
                            }
                            $da['laid'] = $laid;
							$da['content']=$this->input->post('content');
							$da['addtime']=time();
							/*生成缩略图 宽240px 高170px*/
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
							//把原始图片的相对路径和文件名存入数据库
							$da['onvertitle']=$config['upload_path'].$imginfo['file_name'];
							//把缩略图的地址插入数据库
							$da['cover_thumb']=$this->image_lib->full_dst_path;
							//print_r($da);exit;
							//执行入库操作
							$this->load->model('Article_model','article');
							$result=$this->article->adddata($da);
							if($result){
										$date['msg']='成功发布文章';
										$this->load->view('admin/msg.html',$date);
									}
					}
			}
	}
    

	/*回调函数，用于检查文章标题是否重复*/
	function checktitle($artitle){
			$artitle=trim($artitle);
			$this->load->model('Article_model','article');
			$row=$this->article->fetchone($artitle);
			if(!empty($row)){
				/*设置错误提示*/
				$this->form_validation->set_message('checktitle','%s 已存在');
				return false;
				}else{
						return true;
						}
		}
        
    function ajax_lable(){
        if ($_SERVER['REQUEST_METHOD'] == "POST" && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"])=="xmlhttprequest") {
            $data['real_laid'] = array();
            if (isset($_POST['real_laid'])) {
                $data['real_laid'] = explode(',', $this->input->post('real_laid'));
            }
            $laid = trim($this->input->post('laid'));
            $this->load->model('Lable_model','lable');
            $data["lable"] = $this->lable->get_Tree_Array($laid);
            //print_r($data);exit();
            $this->load->view('admin/ajax_lable.html',$data);
        }
    }
	function articlelist(){
		  $this->checklogin();
		  $this->load->model('Article_model','article');
		  $this->load->model('Cate_model','cate');
		  $data['cates']=$this->cate->gettree();		 
		  //$fields='arid,artitle,addtime,cid,ishot,isrecom';
		 //加载分页类
		  $this->load->library('pagination');
		  $post=$this->input->post();
		 // var_dump($post);
		  //var_dump(isset($post));
		  /*判断是否提交查询条件，若无提交或查询条件为空则查询全部的数据并分页显示
			如果提交了查询条件则以多条件复合查询处理
		  */
		if($post==false||($post['cid']==0&&$post['artitle']=='')){ 
			$per_page=30;
			//获取偏移量
			$offset=intval($this->uri->segment(4));
			$arrays=$this->article->article_cate($offset,$per_page);
			 //print_r($arrays);
			 //echo $arr;
			//查询每行数据中的cid对应的栏目名称
			//$arrays=$this->article->article_cate();
			//print_r($arrays);
			 //把总行数传入分页类
			$config['total_rows']=$this->article->getnums();	
			//配置分页项目
			  $config['per_page']=$per_page;
			  $config['base_url']=site_url('admin/articlemng/articlelist');
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
					$wh['cid']=$post['cid'];
					$wh['artitle']=trim($post['artitle']);
					//print_r($post);
					//过滤掉where数组中的空的单元
					$where=$this->article->screen($wh);
					//print_r($where);
					//$where=$this->article->searchsom($fields,$where);
					//print_r($where);
					//查询每行数据中的cid对应的栏目名称
					$arrays=$this->article->article_cates($where);
					//print_r($arrays);//exit;
		}
				//print_r($arrays);
				$data['res']=$arrays;
				/*在页面的底部显示程序的执行状态*/
				$this->output->enable_profiler(false);
				$this->load->view('admin/articlelist.html',$data);
	}
	function editarticle(){
		$this->checklogin();
		$arid=$this->uri->segment(4);+0;
		$this->load->model('Lable_model','lable');
		$this->load->model('Cate_model','cate');
		$this->load->model('Article_model','article');
		$data['lables']=$this->lable->fetchall();
		$data['cates']=$this->cate->gettree();
		$fields='arid,artitle,cid,author,keyword,cover_thumb,isclassic, ishot,isrecom,laid,ardesc,content';
		$data['article']=$this->article->fetchrow($fields,$arid);
		//print_r($data['article']);
		/*
		print_r($data['lables']);
		print_r($data['article'][0]['laid']);
		exit;
		print_r($data);*/
		 //配置文件上传的参数
		$config['max_width']='370';
		$config['max_heifht']='270';
		$config['max_size']='200';
		$config['upload_path']='data/upload/';
		$config['allowed_types']='gif|png|jpg|jpeg';
		$config['remove_spaces']=true;
		$config['encrypt_name']=true;
		$this->load->library('upload',$config);
		//执行上传
		$result=$this->upload->do_upload('onvertitle');
		//获取上传文件的信息
		$imginfo=$this->upload->data();
		//print_r($data);exit();
		$post=$this->input->post();
        
        
		if($post==false){
			$this->load->view('admin/editarticle.html',$data);
		}else{
                      
				$arid=$this->input->post('arid');
				$dat['artitle']=trim($this->input->post('artitle'));
				$dat['keyword']=trim($this->input->post('keyword'));
				$dat['author']=trim($this->input->post('author'));
				$dat['cid']=$this->input->post('cid');
				$dat['ishot']=$this->input->post('ishot');
				$dat['isrecom']=$this->input->post('isrecom');
                $dat['isclassic']= $this->input->post('isclassic');
				$dat['ardesc']=trim($this->input->post('ardesc'));
				$dat['content']=$this->input->post('content');
				$dat['addtime']=time();
                if ($dat['cid'] == 1) {
                    $laid = $this->input->post('a_laid');
                    $laid = implode(",", $laid);
                    
                }
                if ($dat['cid'] == 36){
                    $laid = $this->input->post('a_laid').','.$this->input->post('b_laid').','.$this->input->post('c_laid');
                }
                if ($dat['cid'] == 37) {
                    $laid = 45;
                }
                $dat['laid'] = $laid;
				//表单被提交但是没有文件被上传
				if($imginfo['file_name']==''){
					//print_r($dat);exit();
					$rs=$this->article->renew($dat,$arid);
						if($rs>0){
							$da['msg']='成功修改公司信息';	
						}else{
							$da['msg']='未修改公司信息';
						}
							$this->load->view('admin/msg.html',$da);
					}else if($result==false){//如果有文件被上传，则判断上传的文件是否符合要求
								$errorinfo=$this->upload->display_errors('<span>','</span>');
								$data['error']=$errorinfo;
								$this->load->view('admin/editarticle.html',$data);
					}else{//即提交了表单又上传了文件，则提交全部的数据
					//把图片的相对路径和文件名存入数据库
								$dat['onvertitle']=$config['upload_path'].$imginfo['file_name'];
								$sourceimg=$imginfo['full_path'];
								$medconf['source_image']=$sourceimg;
								$medconf['new_image']='data/images/'.$imginfo['file_name'];
								$medconf['create_thumb']=true;
								$medconf['quelity']='70';
								$medconf['width']=240;
								$medconf['height']=170;
								$medconf['maintain_ratio']=true;
								$this->load->library('image_lib',$medconf);
								$this->image_lib->resize();
								//把缩略图的地址插入数据库
								$dat['cover_thumb']=$this->image_lib->full_dst_path;
								
								$rs=$this->article->renew($dat,$arid);
								if($rs>0){
									$da['msg']='成功修改公司信息';	
								}else{
									$da['msg']='未修改公司信息';
								}
									$this->load->view('admin/msg.html',$da);
							}
		}
		
	}
	/**
	function edit(){
		$arid=$this->input->post('arid');
		$data['artitle']=trim($this->input->post('artitle'));
		$data['keyword']=trim($this->input->post('keyword'));
		$data['author']=trim($this->input->post('author'));
		$data['cid']=$this->input->post('cid');
		$data['ishot']=$this->input->post('ishot');
		$data['isrecom']=$this->input->post('isrecom');
		$data['ardesc']=trim($this->input->post('ardesc'));
		$data['content']=$this->input->post('content');
		$laid=$this->input->post('laid');
		//print_r($data);exit;
		if(!empty($laid)){
			$data['laid']=implode(',',$laid);
		}
		$data['addtime']=time();
		$this->load->model('Article_model','article');
		$result=$this->article->renew($data,$arid);	
		if($result>0){
			$dat['msg']='成功更新文章';
		}else{
			$dat['msg']='为更新文章';
		}
		$this->load->view('admin/msg.html',$dat);
	}
	
	
		多条件复合查询
	
	function search(){
		$this->load->model('Article_model','article');
		$this->load->model('Cate_model','cate');
		$fields='arid,artitle,addtime,cid,ishot,isrecom';
		$data['cates']=$this->cate->gettree();
		$post['cid']=$this->input->post('cid');
		$post['artitle']=trim($this->input->post('artitle'));
		//print_r($post);
		//过滤掉where数组中的空的单元
		$where=$this->article->screen($post);
		$a=$this->article->searchsom($fields,$where);
		//查询每行数据中的cid对应的栏目名称
		$arrays=$this->article->article_cate($a);
		//print_r($arrays);
		$data['res']=$arrays;
		//print_r($data);
		$this->load->view('admin/searcharticle.html',$data);
	}
**/
}
?>