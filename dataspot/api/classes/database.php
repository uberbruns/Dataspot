<?

class DB {

	private static $datasource='mysql:host=localhost;dbname=dataspot;charset=utf8';
	private static $username='root';
	private static $password='root';
	private static $db;

	private function __construct(){}

	public static function db_connect() {

		if (!isset(self::$db)) {

			try {

				self::$db=new PDO(self::$datasource,self::$username,self::$password);

			} catch(PDOExceptin $e) {

				exit();

			}

		}

		return self::$db; 

	}

	public static function query($query) {

		$db_connection = self::db_connect();
		return self::$db->prepare($query);

	}


	public static function last_id() {

		$id = self::db_connect()->lastInsertId('id');
		return $id;

	}


	public static function errorInfo() {

		return self::db_connect()->errorInfo();

	}



}

?>