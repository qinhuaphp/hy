<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Order_model extends Parent_model{
	protected $table='order';
	protected $pk='oid';
	function getorderlimit($pagesize,$offset,$where=array()){
		if(isset($where['ischeck'])){
			$where['order.ischeck']=$where['ischeck'];
			unset($where['ischeck']);
		}
		$field=array('order.oid','order.onum','order.perprice','order.addtime','order.oauthor','order.ophone','order.odate','order.otime','order.ispay','order.ischeck');
		$fileds=array('order.oid','order.onum','order.perprice','order.addtime','order.oauthor','order.ophone','order.odate','order.otime','order.ispay','order.ischeck',
							'company.coname','human.hname','team.tname','member.mname','');
		$query=$this->db->select($fileds)->from($this->table)->where($where)->join('company','company.coid=order.coid','left outer')->
					join('human','human.hid=order.hid','left outer')->join('team','team.teamid=order.teamid','left outer')->join('member','member.mid=order.mid','left outer');
		$res=$query->limit($pagesize,$offset)->order_by('addtime','desc')->get()->result_array();
		$res['total']=$this->db->select($field)->from($this->table)->where($where)->get()->num_rows();
		return $res;
	}
	/*
		生成用于多条件复合查询的where条件数组
		传入的是含有空单元的where数组
		返回的是不含空单元的数组
	*/
	function oscreen($array){
		//过滤传入的数组中的空单元
		static $arr=array();
		foreach($array as $k=>$v){
			if(''!=$v&&'-1'!=$v){
				$arr[$k]=$v;
			}
		}
		return $arr;
	}
	function getoneorder($fields,$oid){
		$res=$this->db->select($fields)->from($this->table)->where(array('order.oid'=>$oid))->join('company','company.coid=order.coid','left outer')->
					join('human','human.hid=order.hid','left outer')->join('team','team.teamid=order.teamid','left outer')->join('member','member.mid=order.mid','left outer')->
					get()->result_array();
		return $res;
	}
	function fetchob($fields,$where,$table,$jointable,$join){
		return $this->db->select($fields)->from($table)->where($where)->join($jointable,$join)->get()->result_array();
	}
	function getoblimit($pagesize,$offset,$ident,$name){
		switch ($ident){
			case 'company';
			$fields=array('order.oid','order.onum','order.odate','order.otime','order.perprice','order.ispay','order.ischeck','order.addtime','company.coid','human.hname','human.hlogo');
			$res=$this->db->select($fields)->from('company')->where(array('company.loginname'=>$name))->join('order','order.coid=company.coid')->join('human','human.hid=order.hid')->
				order_by('order.addtime','desc')->limit($pagesize,$offset)->get()->result_array();
			$res['total']=$this->db->select(array('order.coid','company.coid'))->from($this->table)->where(array('company.loginname'=>$name))->join('company','order.coid=company.coid')->
				get()->num_rows();
			return $res;
			break;
			case 'member';
			$fields=array('order.oid','order.onum','order.odate','order.otime','order.perprice','order.ispay','order.ischeck','order.addtime','member.mid','human.hname','human.hlogo');
			$res=$this->db->select($fields)->from('member')->where(array('member.mname'=>$name))->join('order','order.mid=member.mid')->join('human','human.hid=order.hid')->
				order_by('order.addtime','desc')->limit($pagesize,$offset)->get()->result_array();
			$res['total']=$this->db->select(array('order.mid','member.mid'))->from($this->table)->where(array('member.mname'=>$name))->join('member','order.mid=member.mid')->
				get()->num_rows();
			return $res;
			break;
			case 'human';
			$fields=array('order.oid','order.onum','order.odate','order.otime','order.perprice','order.ispay','order.ischeck','order.addtime','member.mid','member.mname','company.coname','company.cologo');
			$res=$this->db->select($fields)->from('human')->where(array('human.loginname'=>$name))->join('order','order.hid=human.hid')->join('member','order.mid=member.mid','left outer')->join('company','company.coid=order.coid','left outer')->
				order_by('order.addtime','desc')->limit($pagesize,$offset)->get()->result_array();
			$res['total']=$this->db->select(array('order.hid','human.hid'))->from($this->table)->where(array('human.loginname'=>$name))->join('human','order.hid=human.hid')->
				get()->num_rows();
			return $res;
			break;
		}
		
	}
	
}
?>