<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Node_model extends Parent_model{
	protected $table='node';
	protected $pk='nid';
	function tree($pid=0){
		static $data=array();
		if(empty($data)){
			$data=$this->fetchall();
		}
		/*$data=$this->fetchall();*/
		static $tree=array();
		foreach($data  as  $k=>$v){
			if($v['pid']==$pid){
				$tree[]=$v;
				$this->tree($v['nid']);
			}
		}
		return $tree;
	}
	/*把nid组成的字符串转换为数组，使用where in条件查询*/
	function fetchnodes($fields,$array){
		$res=$this->db->select($fields)->from($this->table)->where_in($this->pk,$array)->get()->result_array();
		return $res;
	}





}
?>