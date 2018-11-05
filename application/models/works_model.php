<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Works_model extends Parent_model{
	protected $table='works';
	protected $pk='wid';
	function getall($fields,$where){
		if(isset($where['coid'])||!empty($where['coid'])){
			$coid=$where['coid'];
			$fields=array_merge($fields,array('company.coname'));
			$res=$this->db->select($fields)->from($this->table)->join('company','company.coid=works.coid')->where(array('company.coid'=>$coid))->get()->result_array();
			foreach($res as $k=>$v){
				$res[$k]['owr']=$v['coname'];
			}
			return $res;
		}
		if(isset($where['hid'])&&!empty($where['hid'])){
			$hid=$where['hid'];
			$fields=array_merge($fields,array('human.hname'));
			$res=$this->db->select($fields)->from($this->table)->join('human','human.hid=works.hid')->where(array('human.hid'=>$hid))->get()->result_array();
			foreach($res as $k=>$v){
				$res[$k]['owr']=$v['hname'];
			}
			return $res;
		}
		if(isset($where['teamid'])&&!empty($where['teamid'])){
			$teamid=$where['teamid'];
			$fields=array_merge($fields,array('team.tname'));
			$res=$this->db->select($fields)->from($this->table)->join('team','team.teamid=works.teamid')->where(array('team.teamid'=>$teamid))->get()->result_array();
			foreach($res as $k=>$v){
				$res[$k]['owr']=$v['tname'];
			}
			return $res;
		}
		
	}
	function wlimit($fields,$where,$pagesize,$offset){
		$res=$this->db->select($fields)->from($this->table)->where($where)->order_by('addtime','desc')->limit($pagesize,$offset)->get()->result_array();
		$res['total']=$this->db->select($fields)->from($this->table)->where($where)->get()->num_rows();
		return $res;
	}
}
?>