<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Article_model extends Parent_model{
	protected $table='article';
	protected $pk='arid';
	function fetchone($artitle){
		$row=$this->db->select('arid,artitle')->from($this->table)->where('artitle',$artitle)->get()->result_array();	
		//print_r($row);
		return $row;
	}
	function article_cate($offset,$per_page){
		$arr=array('article.arid','article.addtime','article.artitle','article.cid','article.ishot','article.isrecom','cate.cid','cate.cname ');
		/*联合查询并分页*/
		$array=$this->db->select($arr)->from($this->table)->join('cate','cate.cid=article.cid')->limit($per_page,$offset)->order_by('addtime','desc')->get()->result_array();
		return $array;	
	}
	function article_cates($where){
		$arr=array('article.arid','article.addtime','article.artitle','article.cid','article.ishot','article.isrecom','cate.cid','cate.cname ');
		/*如果存在cid字段则修改为表名.cid，避免引起歧义*/
		if(isset($where['cid'])){
			$wh=array('article.cid'=>$where['cid']);
			unset($where['cid']);
			$arra=array_merge($wh,$where);
		}else{
			$arra=$where;
		}
			//print_r($arra);//exit;
			/*根据where条件进行联合查询*/
			$array=$this->db->select($arr)->from($this->table)->join('cate','cate.cid=article.cid')->where($arra)->get()->result_array();
			return $array;
		
	}
	function fetchsom($fields){
		$array=$this->db->select($fields)->get($this->table)->result_array();
		return $array;
	}
	/*
		根据where具体条件查询数据
	*/
	function searchsom($fields,$where){
		//var_dump($where);
		$som=$this->db->select($fields)->where($where)->get($this->table)->result_array();
		//$som['count']=$this->db->select($fields)->where($where)->from($this->table)->count_all_results();
		//print_r($som);
		return $som;
	}
	function arlist($cid,$pagesize,$offset){
		//$arr=array();
		$fields=array('arid','artitle','addtime','ardesc','cover_thumb');
		$arr['rs']=$this->db->select($fields)->from($this->table)->where(array('cid'=>$cid))->order_by('addtime','desc')->limit($pagesize,$offset)->get()->result_array();
		$arr['rows']=$this->db->select($fields)->from($this->table)->where(array('cid'=>$cid))->get()->num_rows();
		return $arr;
	}
	function arrow($where){
		$fields=array('arid','artitle','author','keyword','addtime','cid','content','ardesc', 'laid');
		return $this->db->select($fields)->from($this->table)->where($where)->get()->result_array();
	}
	function arhot($where){
		$fields=array('arid','artitle','cid', 'cover_thumb');
		$arr=array('ishot'=>1);
		$where=array_merge($arr,$where);
		return $this->db->select($fields)->from($this->table)->where($where)->order_by('addtime','desc')->limit(10)->get()->result_array();
	}
	function arrecom($where,$limit){
		$fields=array('arid','artitle','cover_thumb','cid','addtime','ardesc');
		$arr=array('isrecom'=>1);
		$where=array_merge($arr,$where);
		$tmp=$this->db->select($fields)->from($this->table)->where($where)->order_by('addtime','desc')->limit($limit)->get()->result_array();
		return $tmp;
	}
    function getclassic ($cid) {
        $res = $this->db->select(array('arid', 'artitle', 'cid', 'cover_thumb'))->from('article')->where(array('cid'=>$cid))->order_by('addtime','desc')->limit(10)->get()->result_array();
        return $res;
    }
    //通过标签laid来取文章
    function get_index_article($laid){
        $sql = "SELECT arid, artitle, cover_thumb, cid, addtime, ardesc 
                FROM `hy_article`
                WHERE cid = 36 
                AND FIND_IN_SET('".$laid."', laid) 
                AND isrecom = 1 
                ORDER BY addtime DESC 
                LIMIT 0,12";
        $res = $this->db->query($sql)->result_array();
        return $res;
    }
	function get_lable_article($cid,$pagesize,$offset, $arr){
		$str = array();
        foreach ( $arr as $k => $v ) {
            $str[] = "FIND_IN_SET('".$v."', laid)";
        }
        $sql_str = implode(' AND ', $str)." AND ";
        if (!$arr) {
           $sql_str = "";
        }
        $sql1 = "SELECT arid, artitle, addtime, ardesc, laid, cover_thumb 
                 FROM `hy_article`
                 WHERE ".$sql_str." 
                 cid = ".$cid."  
                 ORDER BY addtime DESC LIMIT ".$offset." , ".$pagesize;
        $sql2 = "SELECT `arid`
                 FROM `hy_article`
                 WHERE ".$sql_str." 
                 cid = ".$cid;
        
        $res['rs'] = $this->db->query($sql1)->result_array();
        $res['rows'] = $this->db->query($sql2)->num_rows();
        return $res;
    }
    

	
}
?>