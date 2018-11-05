<?php
class Invitationmng extends MY_Controller{
    function addinvitation(){
        $this->checklogin();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('intitle','请柬标题','required|callback_checktitle');
 		$this->form_validation->set_rules('ori_price','原价','required');
		$this->form_validation->set_rules('pre_price','现价','required');
		$this->form_validation->set_rules('url','请柬链接','required');  
		$res=$this->form_validation->run();
        if($res == false){
			$this->form_validation->set_error_delimiters('<span>','</span>');
			$this->load->view('admin/addinvitation.html');
		} else {
            $this->load->library('upload');
            for ($i = 1; $i < 3; $i++) {
                //设置请柬封面原始图片的最大宽度为370px，最大高度为260px，图片文件的最大值为300kb
                $config['max_width'] = $i == 1?'290':"200";
                $config['max_heifht'] = $i == 1?'515':"200";
                $config['max_size'] = '300';
                $config['upload_path'] = 'data/upload/';
                $config['allowed_types'] = 'gif|png|jpg|jpeg';
                $config['remove_spaces'] = true;
                $config['encrypt_name'] = true;
                $this->upload->initialize($config);
                $input_name = $i == 1?"cover_thumb":"qr_code";
                $result[$input_name] = $this->upload->do_upload($input_name);
                if ($result[$input_name] == false) {
                    $data[$input_name.'_error'] = $this->upload->display_errors('<span>', '</span>');
                } else {
                    $imginfo[$input_name] = $this->upload->data();
                    $da[$input_name] = $config['upload_path'].$imginfo[$input_name]['file_name'];
                }
            }
            if ($result["cover_thumb"] == false || $result["qr_code"] == false) {
                //print_r($data);exit();
                $this->load->view('admin/addinvitation.html',$data);
            } else {
                $da['intitle'] = trim($this->input->post('intitle'));
                $da['ori_price'] = trim($this->input->post('ori_price'));
                $da['pre_price'] = trim($this->input->post('pre_price'));
                $da['inurl'] = trim($this->input->post('url'));
                 //print_r($da);exit();
                $this->load->model('Invitation_model','invitation');
                $result = $this->invitation->adddata($da);
                if ($result) {
                    $date['msg']='成功添加请柬';
                    $this->load->view('admin/msg.html',$date);
				}
            }
		}
    }

	/*回调函数，用于标题是否重复*/
	function checktitle($intitle){
        $title=trim($intitle);
        $this->load->model('Invitation_model','invitation');
        $row = $this->invitation->fetchone("inid", array("intitle" => $title));
        if(!empty($row)){
            /*设置错误提示*/
            $this->form_validation->set_message('checktitle','%s 已存在');
            return false;
            }
    }
	
    function invitationlist(){
        $this->load->model('Invitation_model','invitation');
        $data["invitations"] = $this->invitation->fetchall();
        $this->load->view('admin/invitationlist.html',$data);
    }
    
    function editinvitation(){
        $this->checklogin();
		$inid = (string)$this->uri->segment(5);
        
        $this->load->model('Invitation_model','invitation');
        $fields='inid, intitle, cover_thumb, ori_price, pre_price, inurl, qr_code';
		$data['res'] = $this->invitation->fetchrow($fields,$inid);
       
        $this->load->view('admin/editinvitation.html',$data);
    }
    
    function edit(){
        $this->checklogin();
        $inid = trim($this->input->post('inid'));
        $data['intitle'] = trim($this->input->post('intitle'));
        //$data['cover_thumb'] = trim($this->input->post('cover_thumb'));
        $data['ori_price'] = trim($this->input->post('ori_price'));
        $data['pre_price'] = trim($this->input->post('pre_price'));
        $data['inurl'] = trim($this->input->post('url'));
        //$data['qr_code'] = trim($this->input->post("qr_code"));
        
        $this->load->library('upload');
        for ($i = 1; $i < 3; $i++) {
            //设置请柬封面原始图片的最大宽度为370px，最大高度为260px，图片文件的最大值为300kb
            $config['max_width'] = $i == 1?'290':"200";
            $config['max_heifht'] = $i == 1?'515':"200";
            $config['max_size'] = '300';
            $config['upload_path'] = 'data/upload/';
            $config['allowed_types'] = 'gif|png|jpg|jpeg';
            $config['remove_spaces'] = true;
            $config['encrypt_name'] = true;
            $this->upload->initialize($config);
            $input_name = $i == 1?"cover_thumb":"qr_code";
            $result[$input_name] = $this->upload->do_upload($input_name);
            if ($result[$input_name] != false) {
                $imginfo[$input_name] = $this->upload->data();
                $data[$input_name] = $config['upload_path'].$imginfo[$input_name]['file_name'];
            }
        }
        $this->load->model('Invitation_model','invitation');
        $result = $this->invitation->renew($data, $inid);
        if ($result) {
            $date['msg']='成功修改请柬';
            $this->load->view('admin/msg.html',$date);
        } else {
            $date['msg']='修改请柬失败';
            $this->load->view('admin/msg.html',$date);
        }
    }
}