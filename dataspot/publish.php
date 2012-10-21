<html>
<head>
	<title>Dataspot - JSON Publish</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="css/styles-publish.css">
</head>
<body>
<ul>
<?

require_once 'config.php';
require_once 'api/classes/database.php';
require_once 'api/functions/helpers.php';
require_once 'api/functions/database.php';
require_once 'api/functions/publish.php';



$api_folder_final = DS_API_DIRECTORY;
$api_folder = DS_API_DIRECTORY.".new";
$api_folder_killme = DS_API_DIRECTORY.".old";
$api_stop_publish = FALSE;

$library_query = DB::query("SELECT id, name, display_name FROM ds_libraries ORDER BY name");
$library_query->execute();
$libraries = ds_camelcase_multi($library_query->fetchAll(PDO::FETCH_ASSOC));


$index_path = ds_save_as_file($api_folder, 'index.json', $libraries);

if ($index_path === FALSE) {
	$api_stop_publish = TRUE;
	ds_log(DS_LOG_WRITE_FAILED, 'Database Index', realpath($index_path));
} else {
	ds_log(DS_LOG_WRITE_OK, 'Database Index', realpath($index_path));
}



foreach ($libraries as $library) if (!$api_stop_publish) {

	$record_query = DB::query("SELECT id FROM ds_records WHERE library_id = :library_id;");
	$record_query->bindParam(':library_id', $library['id'], PDO::PARAM_INT); 
	$record_query->execute();
	
	$records = $record_query->fetchAll(PDO::FETCH_ASSOC);
	$index_records = array();
	$complete_records = array();


	for ($i=0; $i < count($records) ; $i++) { 

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
			if ($value['value']) {
				if ($value["index"] == 1) {
					$index_record[$value['name']] = $value['value'];
				}
				$complete_record[$value['name']] = $value['value'];
			}
		}

		$index_records[] = $index_record;
		$complete_records[] = $complete_record;

	}


	// Write Library and Index File
	$library_folder = ds_save_as_folder($api_folder, $library['name'], $index_records);
	if ($library_folder === FALSE) {
		ds_log(DS_LOG_WRITE_FAILED, 'Library: '.$library['displayName'], realpath($library_folder));
		$api_stop_publish = TRUE;
	} else {
		ds_log(DS_LOG_WRITE_OK, 'Library: '.$library['displayName'], realpath($library_folder));
	}


	// Write Complete Record
	foreach ($complete_records as $complete_record) if (!$api_stop_publish) {
		$record_folder = ds_save_as_folder($library_folder, $complete_record['id'], $complete_record);
		if ($record_folder === FALSE) {
			ds_log(DS_LOG_WRITE_FAILED, 'Record #'.$complete_record['id'], realpath($record_folder));
			$api_stop_publish = TRUE;
		} else {
			ds_log(DS_LOG_WRITE_OK, 'Record #'.$complete_record['id'], realpath($record_folder));
		}
	}


}


// Publis new API Directory
if (!$api_stop_publish) {
	if (file_exists($api_folder_killme)) ds_recursive_remove_directory($api_folder_killme);
	if (is_dir($api_folder_final)) rename($api_folder_final, $api_folder_killme);
	if (is_dir($api_folder)) rename($api_folder, $api_folder_final);
	if (is_dir($api_folder_killme)) ds_recursive_remove_directory($api_folder_killme);
	ds_log(DS_LOG_PUBLISHED, 'Database', realpath($api_folder_final));
} else {
	ds_log(DS_LOG_PUBLISHING_ERROR, 'Database', realpath($api_folder_final));
}




?>
</ul>
</body>
</html>
