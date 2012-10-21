<?

// GET
$app->get($base_route, function ($library_id) {

	ds_sanitize_properties($library_id);

	$query = DB::query("SELECT * FROM ds_properties WHERE library_id = :library_id;");
	$query->bindParam(':library_id', $library_id, PDO::PARAM_INT); 
	$query->execute();

	$results = ds_camelcase_multi($query->fetchAll(PDO::FETCH_ASSOC));
	print(json_encode($results));

});



// DELETE
$app->delete($entity_route, function ($library_id, $property_id) {

	$query = DB::query("DELETE FROM ds_properties WHERE id = :property_id");
	$query->bindParam(':property_id', $property_id, PDO::PARAM_INT); 
	$query->execute();

});



// POST
$app->post($base_route, function ($library_id) use(&$app) {

	$post_data = $app->request()->post('property');
	$name = $post_data['name'];
	$display_name = $post_data['displayName'];
	$type = $post_data['type'];
	$section_id = $post_data['sectionId'];

	$query = DB::query("INSERT INTO ds_properties (library_id, name, display_name, section_id) VALUES (:library_id, :name, :display_name, :section_id)");
	$query->bindParam(':library_id', $library_id, PDO::PARAM_INT); 
	$query->bindParam(':name', $name, PDO::PARAM_STR, 255); 
	$query->bindParam(':display_name', $display_name, PDO::PARAM_STR, 255); 
	$query->bindParam(':section_id', $section_id, PDO::PARAM_INT); 
	$query->execute();


});



// PUT
$app->put($entity_route, function($library_id, $property_id) use(&$app) {

	$put_data = $app->request()->put('property');

	$query = DB::query("UPDATE ds_properties SET
		`name` = :name,
		`display_name` = :display_name,
		`default` = :default,
		`label` = :label,
		`insert` = :insert,
		`append` = :append,
		`prepend` = :prepend,
		`default` = :default,
		`type` = :type,
		`section_id` = :section_id,
		`label_order` = :label_order,
		`order` = :order,
		`auto_name` = :auto_name,
		`index` = :index
		WHERE id = :property_id");

	$query->bindParam(':name', $put_data['name'], PDO::PARAM_STR, 255); 
	$query->bindParam(':display_name', $put_data['displayName'], PDO::PARAM_STR, 255); 
	$query->bindParam(':default', $put_data['default'], PDO::PARAM_STR, 255); 
	$query->bindParam(':label', $put_data['label'], PDO::PARAM_STR, 255); 
	$query->bindParam(':insert', $put_data['insert'], PDO::PARAM_STR, 255); 
	$query->bindParam(':label', $put_data['label'], PDO::PARAM_STR, 255); 
	$query->bindParam(':append', $put_data['append'], PDO::PARAM_STR, 255); 
	$query->bindParam(':prepend', $put_data['prepend'], PDO::PARAM_STR, 255); 
	$query->bindParam(':type', $put_data['type'], PDO::PARAM_STR, 255); 
	$query->bindParam(':order', $put_data['order'], PDO::PARAM_INT); 
	$query->bindParam(':label_order', $put_data['labelOrder'], PDO::PARAM_INT); 
	$query->bindParam(':section_id', $put_data['sectionId'], PDO::PARAM_INT); 
	$query->bindParam(':auto_name', $put_data['autoName'], PDO::PARAM_INT); 
	$query->bindParam(':index', $put_data['index'], PDO::PARAM_INT); 
	$query->bindParam(':property_id',$property_id, PDO::PARAM_INT); 

	$query->execute();

});



?>