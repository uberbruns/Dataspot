<?php

require_once '../config.php';
require_once '../libs/slim/Slim.php';
require_once 'classes/database.php';
require_once 'functions/helpers.php';
require_once 'functions/database.php';


$app = new Slim();
$app->contentType('application/json');


$base_route = '/libraries';
$entity_route = $base_route . '/:library_id';
include 'routes/libraries.php';


$base_route = '/libraries/:library_id/properties';
$entity_route = $base_route . '/:properties_id';
include 'routes/properties.php';


$base_route = '/libraries/:library_id/sections';
$entity_route = $base_route . '/:section_id';
include 'routes/sections.php';


$base_route = '/libraries/:library_id/records';
$entity_route = $base_route . '/:record_id';
include 'routes/records.php';


$base_route = '/libraries/:library_id/records/:record_id/fields';
$entity_route = $base_route . '/:field_id';
include 'routes/fields.php';


$app->run();



