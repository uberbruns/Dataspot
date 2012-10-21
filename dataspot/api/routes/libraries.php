<?

// GET INDEX
$app->get($base_route, function () {

	$query = DB::query("SELECT * FROM ds_libraries ORDER BY display_name");
	$query->execute();
	$libraries = ds_camelcase_multi($query->fetchAll(PDO::FETCH_ASSOC));

	print(json_encode($libraries));

});



// GET ENTITY
$app->get($entity_route, function ($library_id) {

	$query = DB::query("SELECT * FROM ds_libraries WHERE id = :library_id");
	$query->bindParam(':library_id', $library_id, PDO::PARAM_INT); 
	$query->execute();
	$library = ds_camelcase_keys($query->fetch(PDO::FETCH_ASSOC));

	print(json_encode($library));

});




// DELETE
$app->delete($entity_route, function ($library_id) {

	$query = DB::query("DELETE FROM ds_libraries WHERE id = :library_id");
	$query->bindParam(':library_id', $library_id, PDO::PARAM_INT); 
	$query->execute();

});




// POST
$app->post($base_route, function() use(&$app) {

	$post_data = $app->request()->post('library');

	$display_name = $post_data['displayName'];
	$name = ds_slugify($display_name);

	$query = DB::query("INSERT INTO ds_libraries (name, display_name) VALUES (:name, :display_name)");
	$query->bindParam(':name', $name, PDO::PARAM_STR, 255); 
	$query->bindParam(':display_name', $display_name, PDO::PARAM_STR, 255); 
	$query->execute();
	$library_id = DB::last_id();

	$name = 'General';
	$query = DB::query("INSERT INTO ds_sections (name, library_id) VALUES (:name, :library_id)");
	$query->bindParam(':library_id', $library_id, PDO::PARAM_INT); 
	$query->bindParam(':name', $name, PDO::PARAM_STR, 255); 
	$query->execute();
	$section_id = DB::last_id();

	for ($i=0; $i < 2; $i++) { 

		if ($i == 0) {
			$name = 'title';
			$display_name = 'Title';
			$default = 'Quick Fox';
			$label = 'title';
		} else {
			$name = 'text';
			$display_name = 'Text';
			$default = 'The quick brown fox jumps over the lazy dog.';
			$label = 'text';
		}

		$query = DB::query(
			"INSERT INTO ds_properties (`library_id`, `section_id`, `name`, `display_name`, `default`, `label`)
			VALUES (:library_id, :section_id, :name, :display_name, :default, :label)");

		$query->bindParam(':library_id', $library_id, PDO::PARAM_INT); 
		$query->bindParam(':section_id', $section_id, PDO::PARAM_INT); 
		$query->bindParam(':name', $name, PDO::PARAM_STR, 255);
		$query->bindParam(':display_name', $display_name, PDO::PARAM_STR, 255);
		$query->bindParam(':default', $default, PDO::PARAM_STR, 255);
		$query->bindParam(':label', $label, PDO::PARAM_STR, 255);
		$query->execute();

	}


	$query = DB::query("INSERT INTO ds_records (library_id) VALUES (:library_id)");
	$query->bindParam(':library_id', $library_id, PDO::PARAM_INT); 
	$query->execute();
	$record_id = DB::last_id();
	ds_sanitize_record($record_id);


});



// PUT
$app->put($entity_route, function($library_id) use(&$app) {

	$post_data = $app->request()->post('library');
	$display_name = $post_data['displayName'];
	$name = $post_data['name'];

	$query = DB::query("UPDATE ds_libraries SET
		`name` = :name,
		`display_name` = :display_name
		WHERE id = :library_id");

	$query->bindParam(':name', $name, PDO::PARAM_STR, 255); 
	$query->bindParam(':display_name', $display_name, PDO::PARAM_STR, 255); 
	$query->bindParam(':library_id',$library_id, PDO::PARAM_INT); 

	$query->execute();

});

?>