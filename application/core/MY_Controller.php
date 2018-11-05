<?php
class MY_Controller extends CI_Controller{
	function __construct(){
		parent::__construct();
		session_start();
        
       $consult = $this->Parent_model->getconsults();
       $_SESSION['consult'] = $consult;
		//$this->checklogin();
	}
	function checklogin(){
		if(!isset($_SESSION['user'])||$_SESSION['user']==''){
					redirect('admin');			
		}
	}
    


}
?>