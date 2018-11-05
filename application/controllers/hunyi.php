<?php
class Hunyi extends MY_Controller{
	function index(){
		//print_r($_SESSION);
        $data["controller_name"] = $this->uri->segment(1);
        
		$this->load->model('Link_model','link');
		$this->load->model('Site_model','site');
		$this->load->model('Company_model','company');
		$this->load->model('Article_model','article');
		$this->load->model('Human_model','human');

		$data['links']=$this->link->fetchall();
		$data['sites']=$this->site->fetchall();
		//$data['areas']=$this->area->fetchall();
		//取出八个推荐的公司
		$data['cless']=$this->company->cless();
		//取出成功案例的前6个推荐的数据
		$where=array('cid'=>36);
		$anli =$this->article->arrecom($where,12);
        $data['anli'] = array_chunk($anli, 6);
		$data['zcr']=$this->human->getless(27);
		$data['hzs']=$this->human->getless(28);
		$data['sys']=$this->human->getless(29);
		$data['sxs']=$this->human->getless(30);
		//取出婚礼学院的前8个推荐的数据
		$where=array('cid'=>1);
		$xy=$this->article->arrecom($where,28);
		//将取出的数组按照每7个为一组拆分成多二维数组
		$data['xy']=array_chunk($xy,7);
		$this->load->view('header.html',$data);
		$this->load->view('index.html');
		$this->load->view('footer.html');
	}
	
	function customform(){	
		$this->load->library('form_validation');
		$this->form_validation->set_rules('name','姓名','required');
		$this->form_validation->set_rules('phone','手机号','required|callback_checkphone');
		$this->form_validation->set_rules('address','地点','required');
		$this->form_validation->set_rules('marrydate','婚期','required');
		$this->form_validation->set_rules('budget','预算','required');
		$rs=$this->form_validation->run();
		if($rs==false){
			$this->form_validation->set_error_delimiters('<span style="color:red;font-size:12px;">','</span>');
			$this->load->view('customform.html');
			}
			else{
				$post=$this->input->post();
				//print_r($post['marrydate']);echo '<br>';
				$data['name']=trim($post['name']);
				$data['phone']=trim($post['phone']);
				$data['address']=trim($post['address']);
				$data['marrydate']=strtotime($post['marrydate']);
				$data['budget']=trim($post['budget']);
				//echo date('Y-m-d',$data['marrydate']);echo '<br>';
				//print_r($data);
				$this->load->model('Custom_model','custom');
				$res=$this->custom->adddata($data);
				if($res){
					$dat['msg']='<script type="text/javascript">alert(\'发布定制信息成功!请耐心等待工作人员的电话回复\');window.location.href='.'"'.site_url('hunyi/customform').'"</script>';
					$this->load->view('customform.html',$dat);
				}
			}	
			
		}
	function checkphone($phone){
		$phone=trim($phone);
		$rs=preg_match('/^18(\d{9})$|^15(\d{9})$|^13(\d{9})$|^147(\d{8})$/',$phone);
		if($rs==false){
				$this->form_validation->set_message('checkphone','%s 格式不正确');
				return false;
			}else{
				return true;
			}
	}
    
    function article_ajax(){
        if ($this->input->is_ajax_request()) {
            $laid = trim($this->input->post('id'));
            $this->load->model('Article_model','article');
            $anli =$this->article->get_index_article($laid);
            $data['anli'] = array_chunk($anli, 6);
            $this->load->view('index_article_ajax.html', $data);
        }
    }

}


















?>