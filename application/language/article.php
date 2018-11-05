<?php
class Article extends MY_Controller{
	function index(){
		$default=array('cid');
		$arr=$this->uri->uri_to_assoc(3,$default);
		//print_r($arr);
		$this->load->model('Link_model','link');
		$this->load->model('Site_model','site');
		$this->load->model('Consult_model','consult');
		$this->load->model('Article_model','article');
		 //加载分页类
		$this->load->library('pagination');
		
		$offset=intval($this->uri->segment(5));
		$data['links']=$this->link->fetchall();
		$data['sites']=$this->site->fetchall();
		 
		if($arr['cid']==1){
            $pagesize=6;
			$cid=$arr['cid']+0;
			$array=$this->article->arlist($cid,$pagesize,$offset);
            $classic = $this->article->getclassic(2);
			$total=array_pop($array);
			//print_r($array);echo $total;
			//配置分页项目
			$config['total_rows']=$total;
			$config['per_page']=$pagesize;
			$config['first_link']='首页';
			$config['num_links']=2;
			$config['prev_link']='上一页';
			$config['next_link']='下一页';
			$config['last_link']='尾页';
			$config['uri_segment']=5;
			$config['display_pages']=true;
			$config['base_url']=site_url('article/index/cid/1');
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
		if($arr['cid']==2){
            $pagesize=30;
			$cid=$arr['cid']+0;
			$array=$this->article->arlist($cid,$pagesize,$offset);
			$total=array_pop($array);
			//print_r($array);echo $total;
			//配置分页项目
			$config['total_rows']=$total;
			$config['per_page']=$pagesize;
			$config['first_link']='首页';
			$config['num_links']=2;
			$config['prev_link']='上一页';
			$config['next_link']='下一页';
			$config['last_link']='尾页';
			$config['uri_segment']=5;
			$config['display_pages']=true;
			$config['base_url']=site_url('article/index/cid/2');
			$this->pagination->initialize($config);
			//创建链接
			$data['link']=$this->pagination->create_links();
			$data['total']=$total; 
			$data['res']=$array;
			$data['cid']=$cid;
            //print_r($data);exit();
			$this->output->enable_profiler(false);
			$this->load->view('header.html',$data);
			$this->load->view('caseslist.html');
			$this->load->view('footer.html');
			//$this->load->view('111.html');
		}
	
	}
	function detail(){
		$default=array('cid','arid');
		$warr=$this->uri->uri_to_assoc(3,$default);	
		$warr['cid']=$warr['cid']+0;
		$warr['arid']=$warr['arid']+0;
		$this->load->model('Link_model','link');
		$this->load->model('Site_model','site');
		$this->load->model('Team_model','team');
		$data['links']=$this->link->fetchall();
		$data['sites']=$this->site->fetchall();
        $this->load->model('Article_model','article');
        $res = $this->article->arrow($warr);
 
        
/*         $where = array('arid'=>$warr['arid']);
        $res = $this->db->select('artitle')->from('article')->where($where)->get()->result_array();
        if (empty($res) || !in_array($warr['cid'], array(1, 2))) {
            $this->load->view('header.html',$data);
            $this->load->view('error.html');
            $this->load->view('footer.html');
            return false;
        } */
        //$this->load->model('Article_model','article');
		$data['arinfo']= $res;
		if($warr['cid']==1){
			$where=array('cid'=>$warr['cid']);
			//取热门属性的10条数据
			$data['arhot']=$this->article->arhot($where);
			$data['perlink']=array('article/index/cid/1','婚礼学院');
            //print_r($data);exit();
            $this->load->view('header.html',$data);
            $this->load->view('articledetail.html');
            $this->load->view('footer.html');
		} elseif ($warr['cid']==2) {
			$where=array('cid'=>$warr['cid']);
            //取经典案例
             
            $classic = $this->article->getclassic(2);
            $data['classic'] = $classic;
			//取热门属性的10条数据  
			$data['arhot']=$this->article->arhot($where);
			$data['perlink']=array('article/index/cid/2','成功案例');
            $data['execteam'] = $this->team->getexec();
            //print_r($data);exit();
            $this->load->view('header.html',$data);
            $this->load->view('casesdetail.html');
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