<html>
<head>
	<title>Dataspot - JSON Publish</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="css/styles-publish.css">
</head>
<body>
<ul>
<?

require_once 'api/classes/database.php';
require_once 'api/functions/helpers.php';
require_once 'api/functions/database.php';
require_once 'api/functions/publish.php';



$api_folder = '../api_new/';
$api_folder_final = '../api/';
$api_folder_killme = '../api_killme/';


$library_query = DB::query("SELECT id, name FROM ds_libraries ORDER BY display_name");
$library_query->execute();
$libraries = ds_camelcase_multi($library_query->fetchAll(PDO::FETCH_ASSOC));

$index_path = ds_save_as_file($api_folder, 'index.json', $libraries);
ds_log(DS_LOG_CREATED, 'New API Directory', realpath($index_path));
ds_log(DS_LOG_WRITE_OK, 'Database Index', realpath($index_path));



foreach ($libraries as $library) {

	$record_query = DB::query("SELECT id FROM ds_records WHERE library_id = :library_id;");
	$record_query->bindParam(':library_id', $library['id'], PDO::PARAM_INT); 
	$record_query->execute();
	
	$records = $record_query->fetchAll(PDO::FETCH_ASSOC);
	$index_records = array();
	$complete_records = array();


	for ($i=0; $i < count($records) ; $i++) { 

		// Index

		$index_record = array("id" => $records[$i]["id"]);
		$complete_record = array("id" => $records[$i]["id"]);

		$value_query = DB::query(
			"SELECT f.value, p.name, p.index
			FROM ds_properties p, ds_fields f
			WHERE p.library_id = :library_id
			AND f.record_id = :record_id
			AND f.property_id = p.id
			ORDER BY p.label_order;");
		$value_query->bindParam(':record_id', $index_record['id'], PDO::PARAM_INT); 
		$value_query->bindParam(':library_id', $library['id'], PDO::PARAM_INT); 
		$value_query->execute();
		$values = $value_query->fetchAll(PDO::FETCH_ASSOC);

		foreach ($values as $value) {

			if ($value["index"] == 1) {
				$index_record[$value['name']] = $value['value'];
			}

			$complete_record[$value['name']] = $value['value'];


		}

		$index_records[] = $index_record;
		$complete_records[] = $complete_record;

	}

	$library_folder = ds_save_as_folder($api_folder, $library['name'], $index_records);
	ds_log(DS_LOG_WRITE_OK, 'Library', realpath($library_folder));

	foreach ($complete_records as $complete_record) {
		$record_folder = ds_save_as_folder($library_folder, $complete_record['id'], $complete_record);
		ds_log(DS_LOG_WRITE_OK, 'Record', realpath($record_folder));
	}

	/*


	foreach ($records as $record) {

		$data_query = DB::query("SELECT p.name, f.value FROM ds_properties p, ds_fields f
			WHERE p.library_id = :library_id AND f.record_id = :record_id AND f.property_id = p.id;");

		$data_query->bindParam(':record_id', $record['id'], PDO::PARAM_INT); 
		$data_query->bindParam(':library_id', $library['id'], PDO::PARAM_INT); 
		$data_query->execute();
		$raw_data = $data_query->fetchAll(PDO::FETCH_ASSOC);
		$data = array();

		foreach ($raw_data as $kv_pair) {
			$data[$kv_pair['name']] = $kv_pair['value'];
		}

		$record_folder = ds_save_as_folder($library_folder, $record['id'], $data);
		ds_log(DS_LOG_WRITE_OK, 'Record', realpath($record_folder));

	}

	*/

}


ds_log(DS_LOG_PUBLISHED, 'Database', realpath($api_folder_final));


?>
</ul>
</body>
</html>
