<?php

namespace cdao {
	
	/**
	 * @author Marcaj
	 *
	 */
	interface model_model {
		
		/**
		 * Returns row from database
		 * @param integer $id
		 * @return array
		 */
		public function getRowById($id);
		
		/**
		 * Returns array of records ids from database, where record column $by has value $value; 
		 * @param string $by
		 * @param mixed $value
		 * @return array:integer
		 */
		public function select($by, $value);
		
		/**
		 * Inserts row to databse
		 * @param array $row
		 * @return integer
		 */
		public function insert(array $row);
		
		/**
		 * Updates record in database by given id
		 * @param integer $id
		 * @param array $row
		 * @return integer
		 */
		public function update($id, array $row);
		
		/**
		 * Updates column $property in row with id $id by value $value
		 * @param integer $id
		 * @param string $property
		 * @param mixed $value
		 * @return integer
		 */
		public function updateProperty($id, $property, $value);
		
		/**
		 * Deletes row with id $id from database
		 * @param integer $id
		 * @return boolean
		 */
		public function delete($id);
		
		/**
		 * Returns all ids stored in database
		 * @return array:integer
		*/
		public function getAllIds();
		
	}
}
		