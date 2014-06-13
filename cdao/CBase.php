<?php 

namespace cdao {
	
	/**
	 * Imitation of data base
	 * @author Marcaj
	 *
	 */
	class CBase
	{
		const SERIALIZATION_TYPE = 0;
		
		//config:
		private static $path = 'CBase/';
		
		/**
		 * @var string
		 */
		protected $_path;
		
		/**
		 * @var array
		 */
		protected $_data = array();
		
		/**
		 * @var boolean
		 */
		protected $_transaction = false;
		
		/**
		 * Imitation of data base
		 * @param string $path
		 * @throws Exceptions: "Can't lock file" and "Can't write to file"
		 */
		public function __construct($path)
		{
			$this->_path = self::$path . $path;
			if (!file_exists($this->_path)) {
				$file = fopen($this->_path, 'w');
				if(!flock($file,LOCK_EX)) throw new \Exception('Can\'t lock file!');
				if(!fwrite($file, serialize($this->_data))) throw new \Exception('Can\'t write to file!');
				flock($file,LOCK_UN);
				fclose($file);
			}
			$this->_load();
		}
		
		/**
		 * Returns row with specific id 
		 * @param int $id
		 * @return array 
		 */
		public function row($id)
		{
			if(!$this->_transaction) $this->_load();
			if(isset($this->_data[$id]))
				return $this->_data[$id];
			else return null;
		}
		
		/**
		 * Returns array of ids with column value
		 * @param string $key
		 * @param object $value
		 * @return array
		 */
		public function selectBy($key, $value)
		{
			if(!$this->_transaction) $this->_load();
			$return = array();
			foreach($this->_data as $id => $row) {
	 			if((isset($row[$key])) && ($row[$key]) == $value)
					$return[] = $id;
			}
			return $return;
		}
		
		/**
		 * Inserts a row and returns id
		 * @param array $row
		 * @return number
		 */
		public function insert(array $row)
		{
			if(!$this->_transaction) $this->_load();
			$this->_data[] = $row;
			if(!$this->_transaction) $this->_dump();
			return max(array_keys($this->_data));
		}
		
		/**
		 * Updates whole row with secific id
		 * @param int $id
		 * @param array $row
		 * @return boolean
		 */
		public function update($id, array $row)
		{
			if(!$this->_transaction) $this->_load();
			if(!isset($this->_data[$id]))
				return null;
			$this->_data[$id] = $row;
			if(!$this->_transaction) $this->_dump();
			return $id;
		}
		
		/**
		 * Delete row with specific id
		 * @param int $id
		 * @throws Exception "No such record"
		 * @return boolean
		 */
		public function delete($id)
		{
			if(!$this->_transaction) $this->_load();
			if(!isset($this->_data[$id])) throw new \Exception('No such record!');
			unset($this->_data[$id]);
			if(!$this->_transaction) $this->_dump();
			return true;
		}
		
		/**
		 * Returns number of rows
		 * @return number
		 */
		public function count()
		{
			if(!$this->_transaction) $this->_load();
			return count($this->_data);
		}
		
		/**
		 * Dump base to file
		 */
		public function save()
		{
			$this->_dump();
		}
		
		/**
		 * Start transaction
		 * @throws Exception "Can't lock file"
		 */
		public function transactionStart()
		{
			$file = fopen($this->_path, 'a');
			if(!flock($file,LOCK_EX)) throw new \Exception('Can\'t lock file!');
			$this->_transaction = true;
		}
		
		/**
		 * Commit transaction
		 * @return boolean
		 */
		public function commit()
		{
			if($this->_transaction) {
				$file = fopen($this->_path, 'a');
				$this->_dump();
				flock($file,LOCK_UN);
				$this->_transaction = false;
				return true;
			}
			return false;
		}
		
		/**
		 * Rollback transaction
		 * @return boolean
		 */
		public function rollback()
		{
			if($this->_transaction) {
				$file = fopen($this->_path, 'a');
				flock($file,LOCK_UN);
				$this->_transaction = false;
				return true;
			}
			return false;
		}
		
		/**
		 * Load content from file to base
		 */
		protected function _load()
		{
			unset($this->_data);
			if(CBase::SERIALIZATION_TYPE == 0)
				$this->_data = unserialize(file_get_contents($this->_path, FILE_USE_INCLUDE_PATH));
			if(CBase::SERIALIZATION_TYPE == 1)
				$this->_data = json_decode(file_get_contents($this->_path, FILE_USE_INCLUDE_PATH));
		}
		
		/**
		 * Dump base to file
		 * @throws Exception "Can't open file", "Can't lock file" and "Can't write to file"
		 */
		protected function _dump()
		{
			if(CBase::SERIALIZATION_TYPE == 0)
	 			$data = serialize($this->_data);
			if(CBase::SERIALIZATION_TYPE == 1)
				$data = json_encode($this->_data);
			$file = fopen($this->_path,'w');
			if($file == false) throw new \Exception('Can\'t open file!');
			if(!flock($file,LOCK_EX)) throw new \Exception('Can\'t lock file!');
			if(!fwrite($file, $data)) throw new \Exception('Can\'t write to file!');
			flock($file,LOCK_UN);
			fclose($file);
		}
		
		/**
		 * Var dump data in base
		 */
		public function __toString()
		{
			var_dump($this->_data);
		}
		
		/**
		 * Returns ids from all records in database
		 * @return multitype:integer
		 */
		public function getAllIds() {
			return array_keys($this->_data);
		}
		
		/**
		 * Returns array with all values in column $columnName
		 * @param string $columnName
		 * @return array $columnName => number 
		 */
		public function getColumn($columnName) {
			$result = array();
			if(!$this->_transaction) $this->_load();
			foreach($this->_data as $row) {
				if(isset($result[$row[$columnName]]))
					$result[$row[$columnName]]++;
				else
					$result[$row[$columnName]] = 1;
			}
			return $result;
		}
		
		
	}
}
