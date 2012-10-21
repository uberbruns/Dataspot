<?php

function ds_save_as_file($folder, $name, $data) {

	$json_str = json_encode($data);
	file_put_contents(ds_join_path($folder, $name), $json_str);

}



function ds_save_as_folder($folder, $name, $data) {

	$new_folder = ds_join_path($folder, $name);
	mkdir($new_folder);
	ds_save_as_file($new_folder,'index.json',$data);

	return $new_folder;

}


?>