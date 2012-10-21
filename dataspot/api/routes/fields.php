<?

// GET
$app->get($base_route, function($library_id, $record_id) {

	ds_sanitize_record($record_id);

	$field_query = DB::query(
		"SELECT ds_fields.id, ds_fields.property_id, ds_fields.value
		FROM ds_fields JOIN ds_properties
		ON ds_fields.property_id = ds_properties.id
		WHERE record_id = :record_id
		ORDER BY ds_properties.order, ds_properties.id");
	

	$field_query->bindParam(':record_id', $record_id, PDO::PARAM_INT); 
	$field_query->execute();
	$results = ds_camelcase_multi($field_query->fetchAll(PDO::FETCH_ASSOC));
	print(json_encode($results));

});



// GET ENTITY
$app->get($entity_route, function($library_id, $record_id, $field_id) {

	$query = DB::query("SELECT * FROM ds_fields WHERE id = :field_id;");
	$query->bindParam(':field_id', $field_id, PDO::PARAM_INT); 
	$query->execute();
	$result = $query->fetch(PDO::FETCH_ASSOC);

	print(json_encode(ds_camelcase_keys($result)));

});



// DELETE
$app->delete($entity_route, function($id) {


});



// POST
$app->post($base_route, function($library_id, $record_id) use(&$app) {


});



// PUT
$app->put($entity_route, function($library_id, $record_id, $field_id) use(&$app) {

	$put_data = $app->request()->put('field');
	$query = DB::query("UPDATE ds_fields SET value = :new_value WHERE id = :field_id");
	$query->bindParam(':field_id', $field_id, PDO::PARAM_INT); 
	$query->bindParam(':new_value', $put_data['value'], PDO::PARAM_STR); 
	$query->execute();

});


?>