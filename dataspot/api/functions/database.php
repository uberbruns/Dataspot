<?php

	function ds_sanitize_record($record_id) {

		$properties_query = DB::query(
			"SELECT p.id, p.library_id, p.default
			FROM ds_properties p
			WHERE library_id = (SELECT library_id FROM ds_records WHERE id = :record_id)");

		$properties_query->bindParam(':record_id', $record_id, PDO::PARAM_INT); 
		$properties_query->execute();
		$properties_results = $properties_query->fetchAll(PDO::FETCH_ASSOC);
		$property_ids = array_map(function($p) { return $p['id']; }, $properties_results);

		$field_query = DB::query("SELECT * FROM ds_fields WHERE record_id = :record_id");
		$field_query->bindParam(':record_id', $record_id, PDO::PARAM_INT); 
		$field_query->execute();
		$field_results = $field_query->fetchAll(PDO::FETCH_ASSOC);
		$field_property_ids = array_map(function($f) { return $f['property_id']; }, $field_results);


		foreach ($properties_results as $property) {

			if (!in_array($property['id'], $field_property_ids)) {

				$query = DB::query("INSERT INTO ds_fields (record_id, property_id, value) values (:record_id, :property_id, :value)");
				$query->bindParam(':record_id', $record_id, PDO::PARAM_INT); 
				$query->bindParam(':property_id', $property['id'], PDO::PARAM_INT); 
				$query->bindParam(':value', $property['default'], PDO::PARAM_STR); 
				$query->execute();

			}

		}

		foreach ($field_results as $field) {

			if (!in_array($field['property_id'], $property_ids)) {

				$query = DB::query("DELETE FROM ds_fields WHERE id = :field_id");
				$query->bindParam(':field_id', $field['id'], PDO::PARAM_INT); 
				$query->execute();

			}

		}

	}



	function ds_sanitize_properties($library_id) {

		$properties_query = DB::query("SELECT id FROM ds_properties WHERE library_id = :library_id ORDER BY `order`;");
		$properties_query->bindParam(':library_id', $library_id, PDO::PARAM_INT); 
		$properties_query->execute();

		$properties_results = $properties_query->fetchAll(PDO::FETCH_ASSOC);
		$order = 2;

		foreach ($properties_results as $property) {

				$query = DB::query("UPDATE ds_properties SET `order` = :order WHERE id = :property_id");
				$query->bindParam(':order',$order, PDO::PARAM_INT);
				$query->bindParam(':property_id',$property['id'], PDO::PARAM_INT); 
				$query->execute();

				$order += 2;

		}


	}



	function ds_sanitize_sections($library_id) {

		$section_query = DB::query("SELECT id FROM ds_sections WHERE library_id = :library_id ORDER BY `order`");
		$section_query->bindParam(':library_id', $library_id, PDO::PARAM_INT); 
		$section_query->execute();
		$section_results = $section_query->fetchAll(PDO::FETCH_ASSOC);
		$section_ids = array_map(function($s) { return $s['id']; }, $section_results);

		if (empty($section_ids)) return;

		$order = 2;

		foreach ($section_results as $section) {

				$query = DB::query("UPDATE ds_sections SET `order` = :order WHERE id = :id");
				$query->bindParam(':order',$order, PDO::PARAM_INT);
				$query->bindParam(':id',$section['id'], PDO::PARAM_INT); 
				$query->execute();
				$order += 2;

		}

		$order = 999;
		$properties_query = DB::query("UPDATE `ds_properties`
			SET `section_id` = :last_section_id,
			`section_id` = :last_section_id
			WHERE `library_id` = :library_id
			AND `section_id`
			NOT IN (". implode(', ', $section_ids) .")");

		$properties_query->bindParam(':last_section_id', $section_ids[count($section_ids)-1], PDO::PARAM_INT); 
		$properties_query->bindParam(':order',$order, PDO::PARAM_INT);
		$properties_query->bindParam(':library_id', $library_id, PDO::PARAM_INT); 
		$properties_query->execute();



	}

























?>