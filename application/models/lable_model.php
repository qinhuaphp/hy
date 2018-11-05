<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Lable_model extends Parent_model{
	  protected $table='lable';
	  protected $pk='laid';
      
    function getLable() {
        $data = $this->db->select()->from($this->table)->where(array("pid"=>0))->get()->result_array();
        foreach ( $data as $k => $v ) {
            $data[$k]['cid'] = $this->db->select( array("cid") )->from("cate")->where(array("cname"=>$v["laname"]))->get()->result_array();
        }
        return $data;
    } 

    function getLableName($arr) {
        $data = array();
        foreach ($arr as $k => $v) {
            $data[] = $this->db->select(array('laname', 'laid'))->from($this->table)->where(array("laid"=>$v))->get()->result_array();
        }
        return $data;
    }
    
    function getAjaxLable($laid){
        $data1 = $this->db->select()->from($this->table)->where(array("pid"=>$laid))->get()->result_array();
        //print_r($data1);exit();
        foreach ( $data1 as $k => $v ) {
            $data2[$v['laname']] = $this->db->select()->from($this->table)->where(array("pid"=>$v["laid"]))->get()->result_array();
        }
        return $data2;
    }
    
    function gettree($pid=0,$lev=0){
		static $data=array();
		if(empty($data)){
            $data=$this->fetchall();
		}
        
		static $tree=array();
		foreach($data  as  $k=>$v){
			if($v['pid']==$pid){
				$v['lev']=$lev;
				$tree[]=$v;
				$this->gettree($v['laid'],$lev+1);
			}
		}
		return $tree;
	}


    //以数组的形式遍历无限级分类
    function get_Tree_Array($laid){
		static $data=array();
		if(empty($data)){
            $data=$this->fetchall();
		}
		$tree = array();
		foreach($data  as  $k=>$v){
			if($v['pid']==$laid){
				$v['son'] =$this->get_Tree_Array($v['laid']);
                $tree[]=$v;
			}
		}
		return $tree;
    }
	
}
?>