<?php
class Servicemng extends MY_Controller{
    function addservice(){
        $this->checklogin();
        $default = array("coid", "teamid", "hid");
        $array = $this->uri->uri_to_assoc(4, $default);
        $this->load->model('Service_model', 'ser');
        $arr = $this->ser->screen($array);
        if(array_key_exists('coid', $arr)){
            $data['paramname'] = 'coid';
            $data["paramvalue"] = $arr['coid'];
        }
        if(array_key_exists('teamid', $arr)){
            $data['paramname'] = 'teamid';
            $data["paramvalue"] = $arr['teamid'];
        }
        if(array_key_exists('hid', $arr)){
            $data['paramname'] = 'hid';
            $data["paramvalue"] = $arr['hid'];
        }   
        $this->load->library("form_validation");
        $this->form_validation->set_rules('type', '服务类型', 'required');
        $this->form_validation->set_rules('sprice', '服务单价', 'required');
        $this->form_validation->set_rules('explains', '服务说明', 'required');
        $res = $this->form_validation->run();
         
        if ($res == false) {
            $this->form_validation->set_error_delimiters('<span>', '</span>');
            $this->load->view('admin/addservice.html', $data);
        } else {
            $post = $this->input->post();
            $dat['type'] = trim($post['type']);
            $dat['sprice'] = trim($post['sprice']);
            $dat['explains'] = trim($post['explains']);
            if (array_key_exists('coid', $post)) {
                $dat['coid'] = $post['coid'];
            }
            if (array_key_exists('teamid', $post)) {
                $dat['teamid'] = $post['teamid'];
            }      
            if (array_key_exists('hid', $post)) {
                $dat['hid'] = $post['hid'];
            }
            //print_r($dat);exit();
            $res = $this->ser->adddata($dat);
            if ($res) {
                $da['msg'] = "成功添加服务说明";
                $this->load->view('admin/msg.html', $da);
            }
        }
    }
    
    function servicelist(){
        $this->checklogin();
        $default = array("coid", "teamid", "hid");
        $array = $this->uri->uri_to_assoc(4, $default);
        $this->load->model('Service_model', 'ser');
        $arr = $this->ser->screen($array);
        $fields = array('sid', 'sprice', 'type', 'explains');
        $data['res'] = $this->ser->fetchall($fields, $arr);
        //print_r($data);exit();
        $this->load->view('admin/servicelist.html', $data);
    }
    
    function editservice(){
        $this->checklogin();
        $default = array("sid");
        $array = $this->uri->uri_to_assoc(4, $default);
        $this->load->model('Service_model', 'ser');
        $arr = $this->ser->screen($array);
        $fields = array('sid', 'type', 'sprice', 'explains');
        $data['res'] = $this->ser->fetchrow($fields, $arr['sid']);
        $this->load->view('admin/editservice.html', $data);
    }
    
    function edit(){
        $this->load->model('Service_model', 'ser');
        $post = $this->input->post();
        $data['type'] = trim($post['type']);
        $data['sprice'] = trim($post['sprice']);
        $data['explains'] = trim($post['explains']);
        $sid = $post['sid'];
        $row = $this->ser->renew($data, $sid);
        if ($row > 0) {
            $dat['msg'] = "编辑成功";
            $this->load->view('admin/msg.html', $dat);
        }
    }
    
}
?>