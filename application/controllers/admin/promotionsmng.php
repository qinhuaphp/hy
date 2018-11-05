<?php
class Promotionsmng extends MY_Controller{
    function addpro(){
        $this->checklogin();
        $default = array("coid", "teamid");
        $array = $this->uri->uri_to_assoc(4, $default);
        $this->load->model('Promotions_model', 'pro');
        $arr = $this->pro->screen($array);
        if(array_key_exists('coid', $arr)){
            $data['paramname'] = 'coid';
            $data["paramvalue"] = $arr['coid'];
        }
        if(array_key_exists('teamid', $arr)){
            $data['paramname'] = 'teamid';
            $data["paramvalue"] = $arr['teamid'];
        }
        $this->load->library('form_validation');
        $this->form_validation->set_rules('paddress', '活动地址', 'required');
        $this->form_validation->set_rules('pcontains', '活动内容', 'required');
        $res = $this->form_validation->run();
		if ($res == false) {
			$this->form_validation->set_error_delimiters('<span>', '</span>');
			$this->load->view('admin/addpro.html', $data);
        } else {
            $post = $this->input->post();
            $dat['ptime'] = (string) $post['startyear']."/".$post['startmonth']."/".$post['startday'].'/'.$post['stopyear']."/".$post['stopmonth']."/".$post['stopday'];  
            $dat['paddress'] = trim($post['paddress']);
            $dat['pcontains'] = trim($post['pcontains']);
            $dat['isdisplay'] = $post['isdisplay'];
            if (array_key_exists('coid', $post)) {
                $dat['coid'] = $post['coid'];
            }
            if (array_key_exists('teamid', $post)) {
                $dat['teamid'] = $post['teamid'];
            }
            $result = $this->pro->adddata($dat);
			if ($result) {
				$da['msg'] = '成功添加活动';
				$this->load->view('admin/msg.html', $da);
			}
        }
    }
    function editpro(){
        $this->checklogin();
        $this->load->model('Promotions_model', "pro");
        $default = array("prid");
        $array = $this->uri->uri_to_assoc(4, $default);
        $arr = $this->pro->screen($array);
        $fields = array('prid', 'ptime', 'paddress', 'pcontains', 'isdisplay');
        $dat['res'] = $this->pro->fetchrow($fields, $arr['prid']);
        $this->load->view("admin/editpro.html", $dat);
         
    }
    
    function edit(){
        $this->load->model('Promotions_model', 'pro');
        $post = $this->input->post();
        $dat['ptime'] = (string) $post['startyear']."/".$post['startmonth']."/".$post['startday'].'/'.$post['stopyear']."/".$post['stopmonth']."/".$post['stopday'];  
        $dat['paddress'] = trim($post['paddress']);
        $dat['pcontains'] = trim($post['pcontains']);
        $dat['isdisplay'] = $post['isdisplay'];
        $prid = $post['prid'];
        $row = $this->pro->renew($dat, $prid);
        if ($row > 0) {
            $da['msg'] = '编辑成功！';
            $this->load->view('admin/msg.html', $da);
        }
    }
    
    function prolist(){
        $this->checklogin();
        $default = array("coid", "teamid");
        $array = $this->uri->uri_to_assoc(4, $default);
        $this->load->model('Promotions_model', "pro");
        $arr = $this->pro->screen($array);
        if(array_key_exists('coid', $arr)){
            $data['paramname'] = 'coid';
            $data["paramvalue"] = $arr['coid'];
        }
        if(array_key_exists('teamid', $arr)){
            $data['paramname'] = 'teamid';
            $data["paramvalue"] = $arr['teamid'];
        }
        $fields = array('prid', 'ptime', 'paddress', 'isdisplay', 'pcontains');
        $data['res'] = $this->pro->fetchall($fields, $arr);
        $this->load->view('admin/prolist.html', $data);
    }
}
?>