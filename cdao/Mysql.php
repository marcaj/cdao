<?php

namespace cdao {
	/**
	 * Class to get mysql connection
	 * @author Marcaj
	 *
	 */
	class Mysql
	{
		//Mysql config:
		private static $_dbHost = '';
		private static $_dbBase = '';
		private static $_dbUser = '';
		private static $_dbPass = '';
		private static $_db;
	
		/**
		 * Get connection to mysql base
		 * @throws Exception
		 * @return mysql connection
		 */
		public static function factory() {
			if(Mysql::$_db != null)
				return Mysql::$_db;
			else {
				Mysql::$_db = mysql_connect(Mysql::$_dbHost, Mysql::$_dbUser, Mysql::$_dbPass);
				if (!Mysql::$_db) throw new \Exception('Mysql error: ' . mysql_error());
				mysql_select_db(Mysql::$_dbBase, Mysql::$_db);
			}
		}
	}
}
