<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Team_model extends Parent_model{
	protected $table='team';
	protected $pk='teamid';
	function fetchlimit($pagesize,$offset){
		 $fields=array('team.teamid','team.tname','team.phone','team.level','team.volume','team.ischeck','team.isrecom','team.aid','team.tlid','area.amark','teamlable.tlmark');
		 $res=$this->db->select($fields)->from($this->table)->join('area','area.aid=team.aid','left outer')->join('teamlable','teamlable.tlid=team.tlid','left outer')->order_by('teamid','desc')->limit($pagesize,$offset)->get()->result_array();
		 return $res;
	}
	function fetchtat($where){
		$fields=array('team.teamid','team.tname','team.phone','team.level','team.volume','team.ischeck','team.isrecom','team.aid','team.tlid','area.amark','teamlable.tlmark');
		$res=$this->db->select($fields)->from($this->table)->join('area','area.aid=team.aid','left outer')->join('teamlable','teamlable.tlid=team.tlid','left outer')->order_by('teamid','desc')->get()->result_array();
		return $res;
	}
  function tlist($tlid,$pagesize,$offset,$order){
		 $fields=array('teamid','tname','tlogo','level','volume','tlid');
		 $field=array('wid','wname','cover_thumb','teamid');
		 $fd=array('hid','hname','hlogo','cid');
		 $where=array('ischeck'=>1,'tlid'=>$tlid);
		 $rs=$this->db->select($fields)->from($this->table)->where($where)->order_by($order[0],$order[1])->limit($pagesize,$offset)->get()->result_array();
		 foreach($rs as $k=>$v){
			$ws=$this->db->select($field)->from('works')->where(array('teamid'=>$v['teamid']))->order_by('addtime','desc')->limit(2)->get()->result_array();
			$rs[$k]['works']=$ws;
			$rs[$k]['thuman']=$this->db->select($fd)->from('human')->where(array('teamid'=>$v['teamid'],'ischeck'=>1))->order_by('hid','desc')->limit(8)->get()->result_array();
			
		}

		//print_r($rs);//exit;
		$rs['total']=$this->db->select($fields)->from($this->table)->where($where)->get()->num_rows();
		 return $rs;
	}
  function tworks($teamid){
	  	$fields=array('teamid','tname','tlogo','level','volume','tlid','tintrol','tintrolvideo');
		$field=array('wid','wname','wprice','cover_thumb');
		$fd=array('hid','hname','hlogo','cid','volume','level', 'age', 'sex');
        $service_fields = array('sid', 'type', 'sprice', 'explains');
        $promotion_fields = array('ptime', 'paddress', 'pcontains', 'prid');
		$query=$this->db->select($fields)->from($this->table)->where(array('teamid'=>$teamid))->get()->result_array();
		//print_r($query);
		$rs=$query[0];
		$ws=$this->db->select($field)->from('works')->where(array('teamid'=>$query[0]['teamid']))->get()->result_array();
		//print_r($ws);
		$rs['works']=$ws;
		//print_r($rs);
		$hs=$this->db->select($fd)->from('human')->where(array('teamid'=>$query[0]['teamid']))->get()->result_array();
		$rs['hs']=$hs;
        $services = $this->db->select($service_fields)->from('service')->where(array('teamid'=>$query[0]['teamid']))->get()->result_array();
        $rs['service'] = $services;
        $promotions = $this->db->select($promotion_fields)->from('promotions')->where(array('teamid'=>$query[0]['teamid'], 'isdisplay'=>1))->get()->result_array();
        $rs['promotion'] = $promotions;
 		return $rs;
  }
	function twork($array){
		$fields=array('team.teamid','team.tname','team.volume','team.tlogo','team.level','team.tintrol','team.tintrolvideo','team.volume','teamlable.tlid','teamlable.tlmark','works.wid','works.wname','works.wcontent','works.addtime');
		$res=$this->db->select($fields)->from($this->table)->join('works','works.teamid=team.teamid')->join('teamlable','teamlable.tlid=team.tlid')->where($array)->get()->result_array();
		return $res;
	}
    
    function getexec() {
        $res = $this->db->select(array('teamid', 'level', 'volume', 'tname', 'tlogo', 'tlid'))->from('team')->where(array('isexec'=> 1, 'ischeck'=>1))->order_by('volume','desc')->limit(5)->get()->result_array();
        return $res;
    }
}

?>