<html>
<head>
	<title>Dataspot - JSON Publish</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>
<pre>
<?

require_once 'api/classes/database.php';
require_once 'api/functions/helpers.php';
require_once 'api/functions/database.php';
require_once 'api/functions/publish.php';


$api_folder = '../api/';

$library_query = DB::query("SELECT id, name FROM ds_libraries ORDER BY display_name");
$library_query->execute();
$libraries = ds_camelcase_multi($library_query->fetchAll(PDO::FETCH_ASSOC));
ds_save_as_file($api_folder, 'index.json', $libraries);

foreach ($libraries as $library) {

	$record_query = DB::query("SELECT id FROM ds_records WHERE library_id = :library_id;");
	$record_query->bindParam(':library_id', $library['id'], PDO::PARAM_INT); 
	$record_query->execute();
	$records = $record_query->fetchAll(PDO::FETCH_ASSOC);

	$library_folder = ds_save_as_folder($api_folder, $library['name'], $records);

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
		print_r($data);

	}

}

?>
</pre>
</body>
</html>
