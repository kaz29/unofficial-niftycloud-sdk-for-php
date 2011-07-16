<?php
include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'niftycloud.sdk.class.php');
include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'services'.DIRECTORY_SEPARATOR.'niftycloud.class.php');

$ret = @include('settings.php');
if ( !$ret ) {
	trigger_error('stop_instances - ~/samples/settings.php not found.', E_USER_ERROR);
}

if ( !isset($params) ) {
	trigger_error('stop_instances - no variable $params found in settings.php', E_USER_ERROR);
}

$api = new NiftyCloud($params);

$result = $api->stop_instances($params['instance_id']);
if ( $api->isError() ) {
	echo "error\n";
} else {
	echo "{$result->instancesSet->item->instanceId}: {$result->instancesSet->item->previousState->name}=>{$result->instancesSet->item->currentState->name}\n";
}