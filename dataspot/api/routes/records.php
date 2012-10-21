<?

// GET
$app->get($base_route, function ($library_id) {

	$record_query = DB::query("SELECT * FROM ds_records WHERE library_id = :library_id;");
	$record_query->bindParam(':library_id', $library_id, PDO::PARAM_INT); 
	$record_query->execute();
	$records = $record_query->fetchAll(PDO::FETCH_ASSOC);

	for ($i=0; $i < count($records) ; $i++) { 

		$record = $records[$i];

		$label_query = DB::query(
			"SELECT p.id, p.truncate, f.value
			FROM ds_properties p, ds_fields f
			WHERE p.library_id = :library_id
			AND f.record_id = :record_id
			AND f.property_id = p.id
			AND p.label != 'hidden'
			AND p.label != ''
			ORDER BY p.label_order;");


		$label_query->bindParam(':record_id', $record['id'], PDO::PARAM_INT); 
		$label_query->bindParam(':library_id', $record['library_id'], PDO::PARAM_INT); 
		$label_query->execute();
		$labels = $label_query->fetchAll(PDO::FETCH_ASSOC);

		$records[$i]['labels'] = array();
		foreach ($labels as $label) {
			$truncated = false;
			$value = ds_truncate($label['value'], $label['truncate'], $truncated);
			$records[$i]['labels'][] = array('id' => $label['id'], 'value' => $value, 'truncate' => $label['truncate'], 'truncated' => $truncated);
		}
		
	}

	$records = ds_camelcase_multi($records);
	print(json_encode($records));

});



// GET ENTITY
$app->get($entity_route, function ($library_id, $record_id) {

	$query = DB::query("SELECT * FROM ds_records WHERE id = :record_id;");
	$query->bindParam(':record_id', $record_id, PDO::PARAM_INT); 
	$query->execute();

	$result = $query->fetch(PDO::FETCH_ASSOC);
	$result = ds_camelcase_keys($result);

	print(json_encode($result));

});



// DELETE
$app->delete($entity_route, function ($library_id, $record_id) {

	$records_query = DB::query("delete from ds_records where id = :record_id");
	$records_query->bindParam(':record_id', $record_id, PDO::PARAM_INT); 
	$records_query->execute();

	$fields_query = DB::query("delete from ds_fields where record_id = :record_id");
	$fields_query->bindParam(':record_id', $record_id, PDO::PARAM_INT); 
	$fields_query->execute();

});



// POST
$app->post($base_route, function ($library_id) {

	$query = DB::query("insert into ds_records (library_id) values (:library_id)");
	$query->bindParam(':library_id', $library_id, PDO::PARAM_INT); 
	$query->execute();

	$record_id = DB::last_id();
	ds_sanitize_record($record_id);


});



// PUT
$app->put($entity_route, function($library_id) use(&$app) {


});

/*
SELECT ds_properties.*
FROM ds_properties, ds_libraries
WHERE ds_libraries.properties_id = ds_properties.id
AND ds_libraries.id = :library_id;

*/


?>