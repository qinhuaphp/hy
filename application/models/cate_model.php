<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Cate_model extends Parent_model{
	protected $pk='cid';
	protected $table='cate';
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
				$this->gettree($v['cid'],$lev+1);
			}
		}
		return $tree;
	}
	function getfamtree($cid){
		static $data=array();
		if(empty($data)){
		$data=$this->fetchall();
		}
		static $famtree=array();
		foreach($data  as  $k=>$v){
			if($v['cid']==$cat_id){
				array_unshift($famtree,$v);
				if($v['pid']>0){
					$this->getfamtree($v['pid']);
				}
			}
		}
		return $famtree;
	}
	//'查询下级栏目
	function getson($cid){
		$sql="select cid,cname,pid from hy_$this->table where pid=$cid";
		$rs=$this->db->query($sql)->result_array();
		return $rs;
	}

}
?>