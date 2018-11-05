<?php
class Invitation extends MY_Controller{
    function index(){
        $this->load->model('Invitation_model','invitation');
        $data["res"] = $this->invitation->fetchall();
        $this->load->view('invitationlist.html', $data);
    }
    
    function detail(){
        $default=array("inid");
		$inid = intval($this->uri->segment(4));
        $this->load->model('Invitation_model','invitation');
        $fileds = array('inid', 'inurl', "qr_code");
        $data["res"] = $this->invitation->fetchrow($fileds, $inid);
        $this->load->view('invitationdetail.html', $data);
    }



}