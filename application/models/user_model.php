<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class User_model extends Parent_model{
	protected $table='user';
	protected $pk='uid';
	function user_group(){
		/*
				foreach($array as $k=>$v){
				$a=$this->group->fetchrow('gmarke',$v['gid']);
				//var_dump($a);
				//$array[$k]=array_merge($v,$a);
				foreach($a as $val){
					$array[$k]=array_merge($v,$val);
						}
			}
			*/$this->load->model('Group_model','group');
		$arr=array('user.uid','user.uname','user.umarke','user.status','group.gmarke','group.gid');
		$array=$this->db->select($arr)->from($this->table)->where(array('uname !='=>'qh'))->join($this->group->tablename(),'group.gid=user.gid')->get()->result_array();
		return $array;
	}

}
?>