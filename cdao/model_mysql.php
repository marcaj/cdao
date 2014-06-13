<?php

namespace cdao {
	require_once 'model_model.php';
	require_once 'Mysql.php';
	
	/**
	 * Model to handle mysql database. Implements model_model.
	 * @author Marcaj
	 *
	 */
	class model_mysql implements model_model
	{
		/**
		 * @var Mysql
		 */
		private static $db;
		
		/**
		 * @var string
		 */
		private $tableName;
			
		/**
		 * Loads content of database to memory
		 */
		public function __construct($table_name = false) {
			if(model_mysql::$db == null) {
				Mysql::initialize();
				model_mysql::$db = Mysql::factory();
			}
			
			if(!$table_name) {
				$ref = new \ReflectionClass($this);
				$name = str_replace('model_', '', $ref->getName());
				$this->tableName = $name;
			} else {
				$this->tableName = $table_name;
			}
			$this->initialize();
		}
		
		/**
		 * Checks if table exist. In future could create table if not
		 * @throws Exception
		 */
		private function initialize() {
			if(mysql_num_rows(mysql_query("SHOW TABLES LIKE '".$this->tableName."'")) != 1) 
				throw new \Exception('No table: '.$this->tableName);
		}
		
		public function getRowById($id) {
			$r = mysql_query('SELECT * FROM '.$this->tableName.' WHERE id = \''.$id.'\'');
			if (!$r) echo 'Blad zapytania: ' . mysql_error();
			$result = mysql_fetch_array($r, MYSQL_ASSOC);
			unset($result['id']);
			return $result;
		}
		
		public function select($by, $value) {
			$r = mysql_query('SELECT id FROM '.$this->tableName.' WHERE '.$by.' = \''.$value.'\'');
			if (!$r) echo 'Blad zapytania: ' . mysql_error();
			$result = array();
			while ($row = mysql_fetch_array($r, MYSQL_NUM)) {
				$result[] = $row[0];
			}
			return $result;
		}
		
		public function insert(array $row) {
			$query = 'INSERT INTO '.$this->tableName.' (';
			$values = ' VALUES (';
			foreach($row as $key => $cell) {
				$query .= $key . ',';
				$values .= '\''.$cell.'\',';
			}
			$query[strlen($query)-1] = ')';
			$values[strlen($values)-1] = ')';
			$query .= $values;
			$r = mysql_query($query);
			if (!$r) echo 'Blad zapytania: ' . mysql_error();
			return mysql_insert_id();
		}
		
		public function update($id, array $row) {
			$query = 'UPDATE '.$this->tableName.' SET ';
			foreach($row as $key => $value)
				$query .= $key . '= \'' . $value . '\',';
			$query[strlen($query)-1] = ' ';
			$query .= 'WHERE id='.$id;
			$r = mysql_query($query);
			if (!$r) echo 'Blad zapytania: ' . mysql_error();
			return $id;
		}
		
		public function updateProperty($id, $property, $value) {
			$query = 'UPDATE '.$this->tableName.' SET '.$property.'=\''.$value.'\' WHERE id='.$id;
			$r = mysql_query($query);
			if (!$r) echo 'Blad zapytania: ' . mysql_error();
			return $id;
		}
		
		public function delete($id) {
			$r = mysql_query('DELETE FROM '.$this->tableName.' WHERE id='.$id);
			if (!$r) echo 'Blad zapytania: ' . mysql_error();
			return true;
		}
		
		public function getAllIds() {
			$r = mysql_query('SELECT id FROM '.$this->tableName);
			if (!$r) echo 'Blad zapytania: ' . mysql_error();
			$result = array();
			while ($row = mysql_fetch_array($r, MYSQL_NUM)) {
				$result[] = $row[0];
			}
			return $result;
		}
	
		public function getColumn($columnName) {
			$r = mysql_query('SELECT '.$columnName.', count(*) FROM '.$this->tableName.' GROUP BY '.$columnName);
			if(!$r) echo 'Blad zapytania: ' . mysql_error();
			$result = array();
			while ($row = mysql_fetch_array($r, MYSQL_NUM)) {
				$result[$row[0]] = $row[1];
			}
			return $result;
		}
		
	}
}
