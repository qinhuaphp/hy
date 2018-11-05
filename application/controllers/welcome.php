<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		//echo date('Y-m-d','1431619200');
		$this->load->view('welcome_message');
	}
	function test(){
		//$get=$this->input->server('name',true);
		//echo $get;
		$default=array('name','age','height');
		$arr=$this->uri->uri_to_assoc(3,$default);
		echo $this->uri->segment(4);
		//$array=array('name'=>'www','age'=>'111');
		//$str=$this->uri->assoc_to_uri($array);
		//echo $str;
		$fields['name']=$this->uri->segment(3,0);
		$fields['height']=$this->uri->segment(4,0);
		$fields['age']=$this->uri->segment(5,0);
		print_r($fields);echo '<hr>';
		var_dump($arr);
		//$arra=$this->uri->rsegment_array(); 
		//print_r($arra);
	}


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
?>