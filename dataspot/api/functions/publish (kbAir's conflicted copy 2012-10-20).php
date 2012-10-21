<?php

function ds_save_as_file($folder, $name, $data) {

	$json_str = json_encode($data);

	$path = ds_join_path($folder, $name);

	if (!file_exists($path)) {
		mkdir(dirname($path));
	}
	
	file_put_contents($path, $json_str);

	return $path;

}



function ds_save_as_folder($folder, $name, $data) {

	$new_folder = ds_join_path($folder, $name);

	if (!file_exists($new_folder)) {
		mkdir($new_folder);
	}

	ds_save_as_file($new_folder,'index.json',$data);

	return $new_folder;

}


define('DS_LOG_OK', 0);
define('DS_LOG_PUBLISHED', 1);
define('DS_LOG_CREATED', 2);
define('DS_LOG_WRITE_OK', 3);

function ds_log($state, $text, $filename='') {

	$ouput_state = '';
	$ouput_text = $text;

	switch ($state) {

		case DS_LOG_PUBLISHED :
			$ouput_state = 'Published';
			break;

		case DS_LOG_CREATED :
			$ouput_state = 'Created';
			break;

		case DS_LOG_WRITE_OK :
			$ouput_state = 'Write Okay';
			break;

		default:
			$ouput_state = 'Okay';
			break;

	}

	echo '<li><span class="state">'.$ouput_state.'</span><span class="text">'.$ouput_text.'</span><span class="filename">'.$filename.'</span></li>';

}
 ?>