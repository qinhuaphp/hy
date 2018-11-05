<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Parent_model extends CI_Model{
	protected $table='';
	protected $pk='';
	function __construct(){
		parent::__construct();
	}
	//获取表名
	function tablename(){
		return $this->table;
	}
	//获取主键
	function pk(){
		return $this->pk;
		
	}
	//入库
	function adddata($data){
		return $this->db->insert($this->table,$data);
	}
	//更新
	function  renew($data,$id){
		$this->db->where($this->pk,$id);
		$this->db->update($this->table,$data);
		return $this->db->affected_rows();
	}
	//查所有数据
	function fetchall($fields=0,$where=0){
		if($where==0&&$fields==0){
			$res=$this->db->get($this->table);
			return $res->result_array();
			}
		
		if(is_array($where)&&$fields!=''){
			$res=$this->db->select($fields)->from($this->table)->where($where)->get()->result_array();
			return $res;
			}
	}
	//根据主键查一行数据
	//$fields是要查询的字段，多个字段用逗号分隔，如"‘field1’，‘field2’，‘field3’"
	//$id是表的主键的值
	function fetchrow($fields,$id){
		$row=$this->db->select($fields)->from($this->table)->where($this->pk,$id)->get()->result_array();
		return $row;
	}
	/*根据字段查询一行数据*/
	function fetchone($fields,$where){
		$row=$this->db->select($fields)->from($this->table)->where($where)->get()->result_array();	
		//print_r($row);
		return $row;
	}
	//获取总行数
	//$this->db->count_all_result()是CI提供的获取总数的方法
	function getnums(){
		$rs=$this->db->count_all_results($this->table);
		return $rs;
	}
	//分页
	/*
		$fields 查询的字段名
		$offset  偏移量
		$pagesize  每页的行数
		返回的是数组
	*/
	function getlimit($fields,$offset,$pagesize,$where=''){
		//如果没有传入where条件则取单表的全部数据
		//如果传入where条件则取符合条件的数据,返回查询到的数据和总行数
		if($where==''){
		$rs=$this->db->select($fields)->get($this->table,$pagesize,$offset)->result_array();
		return $rs;
			}else{
					$rs=$this->db->select($fields)->where($where)->get($this->table,$pagesize,$offset)->result_array();
					$rs['count']=$this->db->select($fields)->where($where)->from($this->table)->count_all_results();
					return $rs;
			}
	}
	/*
		生成用于多条件复合查询的where条件数组
		传入的是含有空单元的where数组
		返回的是不含空单元的数组
	*/
	function screen($array){
		//过滤传入的数组中的空单元
		static $arr=array();
		foreach($array as $k=>$v){
			if(!empty($v)||$v!=0){
				$arr[$k]=$v;
			}
		}
		return $arr;
	}
    
    function getconsults(){
        $res = $this->db->select()->from('consult')->get()->result_array();
        return $res;
    }
}
?>