<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Favorites_model extends Parent_model{
	protected $table='favorites';
	protected $pk='faid';
	function fany($fields,$where,$pagesize,$offset){
		$res=$this->db->select($fields)->from($this->table)->where($where)->
			join('company','company.coid=favorites.coid','left outer')->join('human','human.hid=favorites.hid','left outer')->join('team','team.teamid=favorites.teamid','left outer')->
			join('teamlable','teamlable.tlid=team.tlid','left outer')->join('cate','cate.cid=human.cid','left outer')->
			limit($pagesize,$offset)->get()->result_array();
		$res['total']=$this->db->select('faid')->from($this->table)->where($where)->get()->num_rows();
		return $res;
	}
	
}
?>