<?php
include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'niftycloud.sdk.class.php');
include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'services'.DIRECTORY_SEPARATOR.'niftycloud.class.php');

$ret = @include('settings.php');
if ( !$ret ) {
	trigger_error('modify_instance_attribute - ~/samples/settings.php not found.', E_USER_ERROR);
}

if ( !isset($params) ) {
	trigger_error('modify_instance_attribute - no variable $params found in settings.php', E_USER_ERROR);
}

$api = new NiftyCloud($params);

$result = $api->modify_instance_attribute(array(
	'InstanceId'=>$params['instance_id'], 
	'Attribute'=>'disableApiTermination', 
	'Value' => false
));
if ( $api->isError() ) {
	echo "error\n";
} else {
	echo "return: {$result->return}\n";
}