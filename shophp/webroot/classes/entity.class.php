<?php

abstract class Entity {
	private $id;

	public function getId(){
		return $this->id;
	}

	public function get($id) {
		global $db;
		$query = "SELECT ".implode(",",static::FIELDS)." FROM ".static::TABLE_NAME." WHERE id = ".intval($id);
		$res = $db->query($query)->fetch_assoc();
		if($res) {
			foreach(static::FIELDS as $field) {
				$this->{$field} = $res[$field];
			}
			return $this;
		}
		return null;
	}

	public function all() {
		global $db;
		$query = "SELECT ".implode(",",static::FIELDS)." FROM ".static::TABLE_NAME;
		$res = $db->query($query);
		$ret = [];
		while($line = $res->fetch_assoc()) {
			$ret[] = $line; 
		}
		return $ret;
	}

	public function create() {
		global $db;
		$fields = static::FIELDS;
		unset($fields[array_search('id',$fields)]);
		$fields_value = $this->__toArray();
		foreach($fields_value as $field_name => $field_value) {
			if(!in_array($field_name,$fields) || !is_string($field_value)) { unset($fields_value[$field_name]); continue;}
			$fields_value[$field_name] = "'".$db->real_escape_string($field_value)."'";
		}
		$query = "INSERT INTO ".static::TABLE_NAME." (".implode(",",$fields).") VALUES (".implode(",",$fields_value).")";
		$res = $db->query($query);
		$this->id = $db->insert_id;
		return $res;
	}

	public function save() {
		global $db;
		$fields = static::FIELDS;
		$fields_value = $this->__toArray();
		$id = $fields_value['id'];
		if(!is_null($id) && is_numeric($id) && $this->get($id)) {
			foreach($fields_value as $field_name => $field_value) {
				if(!in_array($field_name,$fields) || !is_string($field_value)) { unset($fields_value[$field_name]); continue;}
				$fields_value[$field_name] = $db->real_escape_string($field_value);
			}
			$cond = array();
			unset($fields_value['id']);
			foreach($fields_value as $field_name => $field_value) {
				$cond[] = "`$field_name` = '$field_value'";
			}
			$query = "UPDATE ".static::TABLE_NAME." SET ".implode(",",$cond)." WHERE id = ".intval($id);
			$res = $db->query($query);
		} else {
			$res = false;
		}

		$this->get($this->id);
		return $res;
	}

	public function __toArray() {
		return get_object_vars($this);
	}
	
}
