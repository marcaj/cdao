<?php

namespace cdao {
	
	/**
	 * Class ORM
	 * @author Marcaj
	 *
	 */
	abstract class CDAO
	{
		const DEBUG = false;
		
		/**
		 * @var model_model
		 */
		protected $_model;
		
		/**
		 * @var integer
		 */
		private $id;
		
		/**
		 * @var array
		 */
		private $_properties = array();
		
		private static $_buffer = array();
		
		/**
		 * Class ORM
		 */
		public function __construct() {
			$this->_model = self::getClassModel();
		}
		
		/**
		 * Returns object by given id
		 * @param integer $id
		 * @return self
		 */
		public static function getById($id) {
			$className = self::getClassName();
			if(isset(self::$_buffer[$className][$id])) 
				return self::$_buffer[$className][$id];
			$obj = new $className;
			$obj->getFromDatabase($id);
			self::$_buffer[$className][$id] = clone $obj;
			return $obj;
		}
		
		/**
		 * Returns all object from database
		 * @return multitype:self 
		 */
		public static function getAll() {
			$objs = array();
			$model = self::getClassModel();
			$ids = $model->getAllIds();
			return self::changIdsToObjs($ids);
		}
		
		/**
		 * Delete object from database
		 * @param integer $id
		 */
		public static function delete($id) {
			$model = self::getClassModel();
			$model->delete($id);
		}
		
		/**
		 * Load object from database
		 * @param integer $id
		 */
		protected function getFromDatabase($id) {
			$this->id = $id;
			$this->_properties = $this->_model->getRowById($id);
		}
		
		/**
		 * Saves object to database. Returns id of record in data base
		 * @return integer
		 */
		public function save() {
			if($this->id === null) {
				$this->id = $this->_model->insert($this->_properties);
				return $this->id;
			}
			else
				return $this->_model->update($this->id, $this->_properties);
			$className = self::getClassName();
			self::$_buffer[$className][$id] = clone $this;
		}
		
		/**
		 * Returns id of record
		 * @return number
		 */
		public function getId() {
			return $this->id;
		}
		
		/**
		 * Changes array of ids to array of objects
		 * @param array $ids
		 * @return array:CDAO 
		 */
		protected static function changIdsToObjs(array $ids) {
			$objs = array();
			foreach($ids as $id)
				$objs[] = self::getById($id);
			return $objs;
		}
		
		/**
		 * Returns class name
		 * @return string
		 */
		protected static function getClassName() {
			return get_called_class();
		}
		
		/**
		 * Returns model class connected with current class
		 * @return model_model
		 */
		protected static function getClassModel() {
			$modelName = 'model_'.strtolower(self::getClassName());
			return new $modelName;
		}
	
		
		/******************************** Magic Methods *****************************/
		
		public function __isset($name) {
			return isset($this->_properties[$name]);
		}
		
		public function __unset($name) {
			unset($this->_properties[$name]);
		}
		
		public function __call($name, $arguments)
		{
			if(substr($name, 0, 3) == 'get') {
				if(isset($this->$name)) 
					return $this->$name;
				else if(is_array($this->_properties) && array_key_exists(substr(strtolower($name), 3), $this->_properties))
					return $this->_properties[substr(strtolower($name), 3)];
				else {
					if(CDAO::DEBUG) {
						$trace = debug_backtrace();
						throw new \Exception('Undefined property: ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line']);
					}
					else return '';
				}
			}
			elseif(substr($name, 0 ,3) == 'set') {
				if(!isset($arguments[0])) {
					$trace = debug_backtrace();
					throw new \Exception('No argument for function '.$name.' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line']);
				}
				if(isset($this->$name))
					$this->$name = $arguments[0];
				else
					$this->_properties[substr(strtolower($name), 3)] = $arguments[0];
			}
			else {
				$trace = debug_backtrace();
				throw new \Exception('There is no method '.$name.' in '.$trace[0]['file'].' on line '.$trace[0]['line']);
			}
		}
		
	}
}
