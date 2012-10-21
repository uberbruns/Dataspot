<?php

// INFOS
// -----
define("DS_TITLE", "My Dataspot");



// DATABASE CONNECTION
// -------------------
define("DS_DB_DATASOURCE", "mysql:host=localhost;dbname=dataspot;charset=utf8");
define("DS_DB_USERNAME", "root");
define("DS_DB_PASSWORD", "root");



// JSON EXPORT
// -----------
define("DS_API_DIRECTORY", realpath(dirname(__FILE__)."/../")."/api"); // Important! Dont end with a '/'


?>