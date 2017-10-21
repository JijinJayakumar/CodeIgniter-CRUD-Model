<?php
/**
* @author 		Muhammad Faqih Zulfikar
* @copyright 	Copyright (c) 2017 FaqZul (https://github.com/FaqZul/CodeIgniter-CRUD-Model)
* @license 		https://opensource.org/licenses/MIT 	MIT License
* @link 		https://www.facebook.com/DorkSQLi
* @version 		development
*/
defined('BASEPATH') or exit('No direct script access allowed');

class Crud extends CI_Model {

	/**
	* Data will be deleted permanently if the value is TRUE;
	* To save Your data but not to display, set it to FALSE & add the following fields in each table:
	*	$TableName_delete_date	datetime 	DEFAULT NULL;
	*	$TableName_delete_ip	varchar(15)	DEFAULT NULL;
	**/
	protected $delete_record;

	public function __construct() {
		parent::__construct();
		$this->delete_record = (is_bool($this->config->item('delete_record'))) ? $this->config->item('delete_record'): TRUE;
	}

	public function createData($table, $data) {
		$data[$table . '_create_date'] = date('Y-m-d H:i:s');
		$data[$table . '_create_ip'] = $this->input->ip_address();
		$this->db->insert($table, $data);
		return $this->db->error();
	}

	public function readData($select, $from, $where, $joinTable, $groupBy, $order, $orderBy) {
		$this->db->select('SQL_CALC_FOUND_ROWS ' . $select, FALSE);
		$this->db->from($from);
		$this->db->where($where);
		if (count($joinTable > 0)) {
			foreach ($joinTable as $join) { $this->db->join($join['table'], $join['relation'], 'LEFT'); }
		}
		if ($groupBy !== '') {$this->db->group_by($groupBy); }
		$this->db->order_by($order, $orderBy);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function updateData($table, $data, $where) {
		$data[$table . '_update_date'] = date('Y-m-d H:i:s');
		$data[$table . '_update_ip'] = $this->input->ip_address();
		$this->db->where($where);
		$this->db->update($table, $data);
		return $this->db->error();
	}

	public function deleteData($table, $where) {
		if ($this->delete_record === FALSE) {
			$data[$table . '_delete_date'] = date('Y-m-d H:i:s');
			$data[$table . '_delete_ip'] = $this->input->ip_address();
			$this->db->where($where);
			$this->db->update($table, $data);
		}
		else {
			$this->db->where($where);
			$this->db->delete($table);
		}
		return $this->db->error();
	}

}