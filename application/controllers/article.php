<?php
class Article extends MY_Controller{
	function index(){
        $data["controller_name"] = $this->uri->segment(1);
		$default=array('cid');
		$arr=$this->uri->uri_to_assoc(3,$default);
		//print_r($arr);exit();
		$this->load->model('Link_model','link');
		$this->load->model('Site_model','site');
		$this->load->model('Consult_model','consult');
		$this->load->model('Article_model','article');
        $this->load->model('Lable_model','lable');

		 //加载分页类
		$this->load->library('pagination');
		$data['links']=$this->link->fetchall();
		$data['sites']=$this->site->fetchall();
		$offset=intval($this->uri->segment(5));
        $arr1 = array();
		if($arr['cid']==1){
            $lable_id = 38;
            $data["lables"] = $this->lable->get_Tree_Array(38);
            //print_r($data["lables"]);exit();
            $pagesize = 12;
			$cid=$arr['cid']+0;
            //print_r($data["lables"]);exit();
            $num = 5;
            $offset=intval($this->uri->segment($num));
            $url_str = "article/index/cid/1";
            $data['laid'] = '';
            if (isset($arr['laid']) && $arr['laid'] != "") {
                $num = $num + 2;
                $laid = (string)$arr['laid'];
                $arr1[] = $laid;
                $data['laid'] = $laid;
                
                $url_str .= '/laid/'.$laid;
                
            }
            $array = $this->article->get_lable_article($cid,$pagesize,$offset, $arr1);
            
            $total = array_pop($array);
            $classic = $this->article->getclassic(36);
			//print_r($array);echo $total;
			//配置分页项目
			$config['total_rows']=$total;
			$config['per_page']=$pagesize;
			$config['first_link']='首页';
			$config['num_links']=2;
			$config['prev_link']='上一页';
			$config['next_link']='下一页';
			$config['last_link']='尾页';
			$config['uri_segment']= $num;
			$config['display_pages']=true;
            $config['base_url']=site_url($url_str);
			$this->pagination->initialize($config);
			//创建链接
			$data['link']=$this->pagination->create_links();
			$data['total']=$total; 
			$data['res']=$array;
            $data['classic'] = $classic;
			$data['cid']=$cid;
			$this->output->enable_profiler(false);
            //print_r($data);exit();
			$this->load->view('header.html',$data);
			$this->load->view('articlelist.html');
			$this->load->view('footer.html');
		}
        if ($arr['cid']==37) {
            $pagesize = 18;
			$cid=$arr['cid']+0;
            $num = 5;
            $offset=intval($this->uri->segment($num));
            $url_str = "article/index/cid/".$cid;
            $array = $this->article->arlist($cid,$pagesize,$offset);
            $total = array_pop($array);
			$config['total_rows']=$total;
			$config['per_page']=$pagesize;
			$config['first_link']='首页';
			$config['num_links']=2;
			$config['prev_link']='上一页';
			$config['next_link']='下一页';
			$config['last_link']='尾页';
			$config['uri_segment']= $num;
			$config['display_pages']=true;
            $config['base_url']=site_url($url_str);
			$this->pagination->initialize($config);
			//创建链接
			$data['link']=$this->pagination->create_links();
			$data['total']=$total;
			$data['res']=$array;
			$data['cid']=$cid;
            //print_r($data);exit();
            $this->load->view('header.html', $data);
			$this->load->view('teachlist.html');
			$this->load->view('footer.html');
        }
		if($arr['cid']==36){
            $lable_id = 39;
            $lable = $this->lable->get_Tree_Array(39);
           //print_r($lable);exit();
            foreach ($lable as $k => $v) {
                if ($v['laname'] == '婚礼色彩') {
                    $data["lables"]['son']['婚礼色彩'] = $v;
                }
                if ($v['laname'] == '婚礼风格') {
                    $data["lables"]['son']['婚礼风格'] = $v;
                }
                if ($v['laname'] == '婚礼地点') {
                    $data["lables"]['son']['婚礼地点'] = $v;
                }
            }
            //print_r($data["lables"]);exit();
            $pagesize=30;
			$cid=$arr['cid']+0;
            $num = 5;
            $data['color_url'] = $data['style_url'] = $data['point_url'] = "";
            $data['color_id'] = $data['style_id'] = $data['point_id'] = "";
            if (isset($arr['color']) && $arr['color'] != "") {
                $num = $num+2;
                $color = (string)$arr['color'];
                $data['color_url'] = "/color/".$color;
                $data['color_id'] = $color;
                $arr1[] = $color;
            }
            if (isset($arr['style']) && $arr['style'] != "") {
                $num = $num+2;
                $style = (string)$arr['style'];
                $data['style_url'] = "/style/".$style;
                $data['style_id'] = $style;
                $arr1[] = $style;
            }
            if (isset($arr['point']) && $arr['point'] != "") {
                $num = $num+2;
                $point = (string)$arr['point'];
                $data['point_url']  = "/point/".$point;
                $data['point_id'] = $point;
                $arr1[] = $point;
            }
            if ($arr1) {
                $data['lable_name'] = $this->lable->getLableName($arr1);
            }
            $url_str = "article/index/cid/".$cid."".$data['color_url'].$data['style_url'].$data['point_url'];
            $offset=intval($this->uri->segment($num));
            $array = $this->article->get_lable_article($cid,$pagesize,$offset, $arr1);
            $total = array_pop($array);
			//print_r($total);exit();
			//配置分页项目
			$config['total_rows']=$total;
			$config['per_page']=$pagesize;
			$config['first_link']='首页';
			$config['num_links']=2;
			$config['prev_link']='上一页';
			$config['next_link']='下一页';
			$config['last_link']='尾页';
			$config['uri_segment']=$num;
			$config['display_pages']=true;
			$config['base_url']=site_url($url_str);
			$this->pagination->initialize($config);
			//创建链接
			$data['link']=$this->pagination->create_links();
			$data['total']=$total; 
			$data['res']=$array;
            
			$data['cid']=$cid;
            
			$this->output->enable_profiler(false);
			$this->load->view('header.html',$data);
			$this->load->view('caseslist.html');
			$this->load->view('footer.html');
			//$this->load->view('111.html');
		}
	
	}
	function detail(){
        $data["controller_name"] = $this->uri->segment(1);
		$default=array('cid','arid');
		$warr=$this->uri->uri_to_assoc(3,$default);	
		$warr['cid']=$warr['cid']+0;
		$warr['arid']=$warr['arid']+0;
		$this->load->model('Link_model','link');
		$this->load->model('Site_model','site');
		$this->load->model('Team_model','team');
		$this->load->model('Lable_model','lable');
		$data['links']=$this->link->fetchall();
		$data['sites']=$this->site->fetchall();
        $this->load->model('Article_model','article');
        $res = $this->article->arrow($warr);
        $laids = explode(',', $res[0]['laid']);
        $fields = array('laid', 'laname');
        foreach ($laids as $k => $v) {
            $res[0]['lables'][] = $this->lable->fetchrow($fields, $v);
        }
        //$this->load->model('Article_model','article');
		$data['arinfo']= $res;
        $data["cid"] = $warr['cid'];
		if($warr['cid']==1){
			$where=array('cid'=>$warr['cid']);
			//取热门属性的10条数据
			$data['arhot']=$this->article->arhot($where);
			$data['perlink']=array('article/index/cid/1','婚礼学院');
            
            $this->load->view('header.html',$data);
            $this->load->view('articledetail.html');
            $this->load->view('footer.html');
		}
        if ($warr['cid']== 36) {
			$where=array('cid'=>$warr['cid']);
            //取经典案例
             
            $classic = $this->article->getclassic(36);
            $data['classic'] = $classic;
			//取热门属性的10条数据  
			$data['arhot']=$this->article->arhot($where);
			$data['perlink']=array('article/index/cid/36','成功案例');
            $data['execteam'] = $this->team->getexec();
            
            $this->load->view('header.html',$data);
            $this->load->view('casesdetail.html');
            $this->load->view('footer.html');
			
		}
        if ($warr['cid']==37) {
             
            //取经典案例
            $classic = $this->article->getclassic(36);
            $data['classic'] = $classic;
			//取热门属性的10条数据  
			 
            $this->load->view('header.html',$data);
            $this->load->view('teachdetail.html');
            $this->load->view('footer.html');
        }
	}
    //页面底部相关资讯
	function information(){
		$arid=$this->uri->segment(3)+0;
		//echo $arid;
		$this->load->model('Link_model','link');
		$this->load->model('Site_model','site');
		$this->load->model('Article_model','article');
		//$this->load->model('Consult_model','consult');
		$data['links']=$this->link->fetchall();
		$data['sites']=$this->site->fetchall();
		//$data['consults']=$this->consult->fetchall();
		$data['article']=$this->article->fetchrow(array('arid','artitle','content'),$arid);
		//print_r($data['article']);
		$this->output->enable_profiler(false);
		$this->load->view('header.html',$data);
		$this->load->view('xgsm.html');
		$this->load->view('footer.html');
	} 

}
?>