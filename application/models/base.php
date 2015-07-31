<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Base extends CI_Model{
	function __construct(){
		parent::__construct();
		$this->load->database();
	}
	public function insertData($table,$data){
		$this->db->insert($table, $data);
		return $this->db->affected_rows();
	}
	public function insertDataReturnId($table,$data){
		$this->db->insert($table, $data);
		return $this->db->insert_id();
	}
	public function deleteData($condition){
		if(isset($condition['where'])){
			foreach($condition['where'] as $key=>$value){
				$this->db->where($key,$value);
			}
		}
		$this->db->delete($condition['table']);
		return $this->db->affected_rows();
	}
	public function  updateData($condition){
		if(isset($condition['where'])){
			foreach($condition['where'] as $key=>$value){
				$this->db->where($key,$value);
			}
		}
		$this->db->update($condition['table'],$condition['data']);
		return $this->db->affected_rows();
	}
	public function selectData($condition){
		$this->db->from($condition['table']);
		if(isset($condition['join'])){
			foreach($condition['join'] as $key=>$value){
				//Example:$this->db->join('comments', 'comments.id = blogs.id');
				$this->db->join($key,$value);
			}
		}
		if(isset($condition['where'])){
			foreach($condition['where'] as $key=>$value){
				$this->db->where($key,$value);
			}
		}
		if(isset($condition['or_where'])){
			foreach($condition['or_where'] as $key=>$value){
				$this->db->or_where($key,$value);
			}
		}
		if(isset($condition['where_in'])){
			foreach($condition['where_in'] as $key=>$value){
				$this->db->where_in($key,$value);
			}
		}
		if(isset($condition['or_where_in'])){
			foreach($condition['or_where_in'] as $key=>$value){
				$this->db->or_where_in($key,$value);
			}
		}
		if(isset($condition['like'])){
			foreach($condition['like'] as $key=>$value){
				$this->db->like($key,$value);
			}
		}
		if(isset($condition['or_like'])){
			foreach($condition['or_like'] as $key=>$value){
				$this->db->or_like($key,$value);
			}
		}
		if(isset($condition['limit'])) $this->db->limit($condition['limit']['limit'],$condition['limit']['offset']);
		//Example: $this->db->group_by("title"); OR $this->db->group_by(array("title", "date")); 
		if(isset($condition['group_by'])) $this->db->group_by($condition['group_by']);
		if(isset($condition['order_by'])){
			foreach($condition['order_by'] as $key=>$value){
				$this->db->order_by($key,$value);
			}
		}
		if(isset($condition['select'])) $this->db->select($condition['select']);
	 	if($condition['result']=="data") return $this->db->get()->result();
	 	elseif($condition['result']=="count") return $this->db->count_all_results();
	}
	public function custom_query($sql){
		$this->db->query($sql);
		return $this->db->get()->result();
	}
}
?>
