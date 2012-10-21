<?php

function ds_save_as_file($folder, $name, $data) {

	$json_str = json_encode($data);
	$path = ds_join_path($folder, $name);
	$dirname = dirname($path); 

	if (!file_exists($dirname)) {
		mkdir($dirname);
	}
	
	$bytes_written = file_put_contents($path, $json_str);

	return ($bytes_written === FALSE) ? FALSE : $path;

}



function ds_save_as_folder($folder, $name, $data) {

	$new_folder = ds_join_path($folder, $name);

	if (!file_exists($new_folder)) {
		mkdir($new_folder);
	}

	$path = ds_save_as_file($new_folder,'index.json',$data);

	return ($path === FALSE) ? FALSE : $new_folder;

}



function ds_recursive_remove_directory($directory) {

	// unlink rmdir trigger_error

    foreach(scandir($directory) as $file) {

    	if ($file !== ".." && $file !== ".") {

    		$file = ds_join_path($directory, $file);

	        if(is_dir($file)) { 
	            ds_recursive_remove_directory($file);
	        } else {
		        unlink($file);
	        }

        }

    }

    rmdir($directory);

}


define('DS_LOG_OK', 0);
define('DS_LOG_WRITE_OK', 1);
define('DS_LOG_WRITE_FAILED', 2);
define('DS_LOG_PUBLISHED', 3);
define('DS_LOG_PUBLISHING_ERROR', 4);



function ds_log($state, $text, $filename='') {

	$ouput_state = '';
	$ouput_text = $text;

	switch ($state) {

		case DS_LOG_PUBLISHED :
			$ouput_state = 'Published';
			break;

		case DS_LOG_PUBLISHING_ERROR :
			$ouput_state = 'Publishing API Failed!';
			break;

		case DS_LOG_WRITE_OK :
			$ouput_state = 'Write Okay';
			break;

		case DS_LOG_WRITE_FAILED :
			$ouput_state = 'Write Failed!';
			break;

		default:
			$ouput_state = 'Okay';
			break;

	}

	echo '<li><span class="state">'.$ouput_state.'</span><span class="text">'.$ouput_text.'</span><span class="filename">'.$filename.'</span></li>';

}
 ?>