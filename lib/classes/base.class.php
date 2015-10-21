<?php
/**
 * Base Class
 *
 * Sets up DB connection
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @author     traviswachendorf@iheartmedia.com
 * @copyright  2014
 * @version    1.0
 */

class BaseClass {

	protected $db;

	public function __construct() {
		try {
			$db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME .";port=" . DB_PORT,DB_USER,DB_PASS);
			$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
			$db->exec("SET NAMES 'utf8'");
			$this->db = $db;
		}
		catch (Exception $e) {
			echo "Could not connect to the database. Please contact website admin.";
			exit;
		}
	}

}