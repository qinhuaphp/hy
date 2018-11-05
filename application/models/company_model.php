<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Company_model extends Parent_model{
	protected $table='company';
	protected $pk='coid';
	function com_area($where){
		$fields=array('company.coid','company.coname','company.cophone','company.level','company.volume','company.isaudit','company.isrecom','company.aid','area.amark','area.aname');
		$res=$this->db->select($fields)->from($this->table)->join('area','area.aid=company.aid')->where($where)->order_by('coregtime','desc')->get()->result_array();
		return $res;
	}
	function  fetchlimit($pagesize,$offset){
		$fields=array('company.coid','company.coname','company.cophone','company.level','company.volume','company.isaudit','company.isrecom','company.aid','area.amark','area.aname');
		$res=$this->db->select($fields)->from($this->table)->join('area','area.aid=company.aid')->order_by('coregtime','desc')->limit($pagesize,$offset)->get()->result_array();
		return $res;
	}
		//根据条件查询并分页
	function clist($pagesize,$offset,$order=array()){
		$fields=array('company.coid','company.coname','company.cologo','company.level','company.volume');
		$field=array('works.wname','works.cover_thumb');
		$array=array('isaudit'=>'1');
		if(empty($order)){
			$arr=array('coregtime','desc');
		}else{
			$arr=$order;
		}
		$query=$this->db->select($fields)->from($this->table)->where($array)->order_by($arr[0],$arr[1])->limit($pagesize,$offset)->get()->result_array();
		//print_r($query);
		foreach($query as $k=>$v){
		$query[$k]['works']=$this->db->select($field)->from('works')->where(array('coid'=>$v['coid']))->order_by('addtime','desc')->limit(2)->get()->result_array();
		}
		$data=$query;
		$data['rows']=$this->db->select($fields)->from($this->table)->where($array)->get()->num_rows();
		return $data;
	}
	function coworks($coid){
        
        $promotion_fields = array('prid', 'ptime', 'paddress', 'pcontains');
        $query['promotion'] = $this->db->select($promotion_fields)->from('promotions')->where(array('coid'=>$coid, 'isdisplay'=>1))->get()->result_array();
		$fields=array('coid','coname','cologo','level','volume','cointrol','cointrolvideo', 'esttime', 'style');
		$query['coinfo']=$this->db->select($fields)->from($this->table)->where(array('coid'=>$coid))->get()->result_array();
		$field=array('wid','wname','wprice','cover_thumb');
		$this->load->model('Works_model','works');
		$query['works']=$this->works->fetchall($field,array('coid'=>$coid));
		return $query;
	}
	function cwork($array){
		$fields=array('company.coid','company.coname','company.cologo','company.level','company.cointrol','company.cointrolvideo','company.volume','works.wid','works.wname','works.wcontent','works.addtime');
		 return $this->db->select($fields)->from($this->table)->join('works','works.coid=company.coid','left outer')->where($array)->get()->result_array();
	}
	function cless(){
		$fields=array('company.coid','company.coname','company.cointrol','works.wid','works.wname','works.wprice','works.cover_thumb');
		//按照公司id分组并左外连接和按照作品添加时间查询公司和作品表，取前八个
		$arr=$this->db->select($fields)->from($this->table)->join('works','company.coid=works.coid')->where(array('isrecom'=>1))->order_by('addtime','desc')->group_by('coid')->limit(8)->get()->result_array();
		return $arr;
	}
    function get_company_order($company_id) {
        $order_fields = array('oid', 'onum', 'omark', 'address', 'perprice', 'addtime', 'odate', 'otime', 'oauthor', 'ophone', '');
        $res = $this->db->select($order_fields)->from('order')->where(array('coid'=>$company_id, 'ischeck'=>1))->get()->result_array();
        return $res;
    }

}
?>