<?php

	function ds_join_path() {

		$args = func_get_args();
		$first_char = (substr($args[0], 0, 1) == "/") ? "/" : "";

		$components = array_map(function($comp) {

			return trim($comp,'/\\');

		}, func_get_args());

		return $first_char.implode(DIRECTORY_SEPARATOR, $components);

	}


	function ds_slugify($string) {

		$input_string = $string;

		$string = utf8_decode($string);
		$string = html_entity_decode($string);
		
		$a = 'ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ';
		$b = 'AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn';
		$string = strtr($string, utf8_decode($a), $b);
		
		$ponctu = array("?", ".", "!", ",");
		$string = str_replace($ponctu, "", $string);
		
		$string = trim($string);
		$string = preg_replace('/([^a-z0-9]+)/i', '-', $string);
		$string = strtolower($string);
		
		if (empty($string)) return md5($input_string);
		
		return utf8_encode($string);

	}


	function ds_truncate($string, $limit, &$truncated) {

		$new_string = $string;

		if (strlen($string) > $limit) {
			$new_string = substr($new_string, 0, strrpos(substr( $new_string, 0, $limit), ' ' ));
		}

		$truncated = ($string != $new_string);
		return $new_string;

	}


	function ds_camelcase_keys($array) {

		$return = array();

		foreach ($array as $key => $value) {

			$key = lcfirst(implode('', array_map(function($str) { return ucfirst($str); }, explode('_', $key))));
			$return[$key] = $value;

		}

		return $return;

	}


	function ds_camelcase_multi($multi_array) {

		return array_map(function($array) { return ds_camelcase_keys($array); }, $multi_array);

	}


?>