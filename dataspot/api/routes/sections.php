<?

// GET
$app->get($base_route, function($library_id) {

	$query = DB::query("SELECT * FROM ds_sections WHERE library_id = :library_id ORDER BY `order` ASC;");
	$query->bindParam(':library_id', $library_id, PDO::PARAM_INT); 
	$query->execute();

	$results = ds_camelcase_multi($query->fetchAll(PDO::FETCH_ASSOC));
	print(json_encode($results));

});



// DELETE
$app->delete($entity_route, function($library_id, $section_id) {

	$query = DB::query("DELETE FROM ds_sections WHERE id = :section_id");
	$query->bindParam(':section_id', $section_id, PDO::PARAM_INT); 
	$query->execute();

	ds_sanitize_sections($library_id);

});



// POST
$app->post($base_route, function($library_id) use(&$app) {

	$post_data = $app->request()->post('section');
	$name = $post_data['name'];
	$order = $post_data['order'];

	$query = DB::query("INSERT INTO ds_sections (`library_id`, `name`, `order`) VALUES (:library_id, :name, :order)");
	$query->bindParam(':library_id', $library_id, PDO::PARAM_INT); 
	$query->bindParam(':name', $name, PDO::PARAM_STR, 255); 
	$query->bindParam(':order', $order, PDO::PARAM_INT); 
	$query->execute();

	print(json_encode($post_data));
	ds_sanitize_sections($library_id);

});


// PUT
$app->put($entity_route, function($library_id, $section_id) use(&$app) {

	$put_data = $app->request()->put('section');
	$query = DB::query("UPDATE ds_sections SET `name` = :name, `order` = :order WHERE id = :section_id");

	$query->bindParam(':name', $put_data['name'], PDO::PARAM_STR, 255); 
	$query->bindParam(':order', $put_data['order'], PDO::PARAM_INT); 
	$query->bindParam(':section_id',$section_id, PDO::PARAM_INT);

	$query->execute();

	ds_sanitize_sections($library_id);
	// print(json_encode($put_data));

});



?>