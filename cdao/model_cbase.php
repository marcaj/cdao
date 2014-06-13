<?php

namespace cdao {
	require_once 'CBase.php';
	require_once 'model_model.php';
	
	/**
	 * Model to handle imitation of database called CBase. Implements model_model.
	 * @author Marcaj
	 *
	 */
	abstract class model_cbase implements model_model {
		
		/**
		 * @var CBase
		 */
		private $base;
		
		/**
		 * @var array:CBase
		 */
		private static $instances = array();
		
		/**
		 * Loads content of database to memory
		 */
		public function __construct() {
			$ref = new \ReflectionClass($this);
			$name = explode('_', $ref->getName());
			if(!isset(model_cbase::$instances[$name[1]])) {
				model_cbase::$instances[$name[1]] = new CBase($name[1]);
			}
			$this->base = model_cbase::$instances[$name[1]];
		}
		
		public function getRowById($id) {
			return $this->base->row($id);
		}
		
		public function select($by, $value) {
			return $this->base->selectBy($by, $value);
		}
		
		public function insert(array $row) {
			return $this->base->insert($row);
		}
		
		public function update($id, array $row) {
			return $this->base->update($id, $row);
		}
		
		public function updateProperty($id, $property, $value) {
			$row = $this->base->row($id);
			$row[$property] = $value;
			return $this->base->update($id, $row);
		}
		
		public function delete($id) {
			return $this->base->delete($id);
		}
		
		public function getAllIds() {
			return $this->base->getAllIds();
		}
		
		/**
		 * Selects column $propery from records where property $by equals $value.
		 * @param string $by
		 * @param mixed $value
		 * @param string $property
		 * @return array:mixed 
		 */
		public function selectProperty($by, $value, $property) {
			$ids = $this->base->selectBy($by, $value);
			$rows = array();
			foreach($ids as $id) {
				$row = $this->base->row($id);
				$rows[] = $row[$property];
			}
			return $rows;
		}
		
		/**
		 * Returns array with all values in column $columnName
		 * @param string $columnName
		 * @return array $columnName => number 
		 */
		public function getColumn($columnName) {
			return $this->base->getColumn($columnName);
		}
		
		/**
		 * Starts transaction
		 */
		public function transactionStart() {
			$this->base->transactionStart();
		}
		
		/**
		 * Commits transaction
		 */
		public function commit() {
			$this->base->commit();
		}
		
		/**
		 * Undo transaction
		 */
		public function rollback() {
			$this->base->rollback();
		}
	}
	
}
