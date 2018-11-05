<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Human_model extends Parent_model{
	protected $table='human';
	protected $pk='hid';
	function fetchhaclimit($pagesize,$offset){
		$fields=array('human.hid','human.hname','human.tellphone','human.sex','human.hprice','human.level','human.volume','human.ischeck','human.isrecom','human.aid','human.cid','human.teamid','area.amark','cate.cname','team.tname');
		$res=$this->db->select($fields)->from($this->table)->join('cate','human.cid=cate.cid','left outer')->join('area','human.aid=area.aid','left outer')->join('team','human.teamid=team.teamid','left outer')->order_by('hid','desc')->limit($pagesize,$offset)->get()->result_array();
		return $res;
	}
	function fetchsom($where){
		$fields=array('human.hid','human.hname','human.tellphone','human.sex','human.hprice','human.level','human.volume','human.ischeck','human.isrecom','human.aid','human.cid','human.teamid','area.amark','cate.cname','team.tname');
		$res=$this->db->select($fields)->from($this->table)->join('cate','human.cid=cate.cid','left outer')->join('area','human.aid=area.aid','left outer')->join('team','human.teamid=team.teamid','left outer')->where($where)->order_by('hid','desc')->get()->result_array();
		return $res;
	}
	function getless($cid){
		$fields=array('human.hid','human.hname','human.hlogo');
		$res=$this->db->select($fields)->from($this->table)->where(array('ischeck'=>1,'isrecom'=>1,'cid'=>$cid))->limit(20)->get()->result_array();
		return $res;
	}
	function hlist($cid,$pagesize,$offset,$order){
		 $fields=array('hid','hname','sex','hprice','level','volume','hlogo');
		 $field=array('wid','wname','cover_thumb');
		 $where=array('ischeck'=>1,'cid'=>$cid, 'teamid'=>0);
		 $rs=$this->db->select($fields)->from($this->table)->where($where)->order_by($order[0],$order[1])->limit($pagesize,$offset)->get()->result_array();
		 foreach($rs as $k=>$v){
			$ws=$this->db->select($field)->from('works')->where(array('hid'=>$v['hid']))->order_by('addtime','desc')->limit(3)->get()->result_array();
			//print_r($ws);
			$rs[$k]['works']=$ws;
		}
		$rs['total']=$this->db->select($fields)->from($this->table)->where($where)->get()->num_rows();
		 return $rs;
	}
	function hworks($hid){
		$fields=array('hid','hname','hlogo','hprice','level','volume','hintrol','hintrolvideo','sex','cid', 'age');
        $service_fields = array('sid', 'type', 'sprice', 'explains');
        $query['service'] = $this->db->select($service_fields)->from('service')->where(array('hid'=>$hid))->get()->result_array();
		$query['hinfo']=$this->db->select($fields)->from($this->table)->where(array('hid'=>$hid))->get()->result_array();
		$field=array('wid','wname','wprice','cover_thumb');
		$this->load->model('Works_model','works');
		$this->load->model('Cate_model','cate');
		$query['hinfo'][0]['works']=$this->works->fetchall($field,array('hid'=>$hid));
		$query['hinfo'][0]['cname']=$this->cate->fetchrow('cname',$query['hinfo'][0]['cid']);
		return $query;
	}
	function hwork($array){
		$fields=array('human.hid','human.hname','human.sex','human.hprice','human.volume','human.cid','human.hlogo','human.level','human.hintrol','human.hintrolvideo','human.volume','works.wid','works.wname','works.wcontent','works.addtime');
		$res=$this->db->select($fields)->from($this->table)->join('works','works.hid=human.hid','left outer')->where($array)->get()->result_array();
		$this->load->model('Cate_model','cate');
		$arr=$this->cate->fetchrow('cname',$res[0]['cid']);
		$res=array_merge($res[0],$arr[0]);
		return $res;
	}
	function hsearch($fields,$like){
		$query=$this->db->select($fields)->from($this->table)->where(array('ischeck'=>1))->like($like)->get();
		$res['rs']=$query->result_array();
		$res['total']=$query->num_rows();
		return $res;
	}
    function get_human_order($human_id) {
        $order_fields = array('oid', 'onum', 'omark', 'address', 'perprice', 'addtime', 'odate', 'otime', 'oauthor', 'ophone');
        $res = $this->db->select($order_fields)->from('order')->where(array('hid'=>$human_id, 'ischeck'=>1))->limit(7)->get()->result_array();
        return $res;
    }
}
?>