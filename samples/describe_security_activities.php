<?php
include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'niftycloud.sdk.class.php');
include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'services'.DIRECTORY_SEPARATOR.'niftycloud.class.php');

$ret = @include('settings.php');
if ( !$ret ) {
	trigger_error('describe_security_activities - ~/samples/settings.php not found.', E_USER_ERROR);
}

if ( !isset($params) ) {
	trigger_error('describe_security_activities - no variable $params found in settings.php', E_USER_ERROR);
}

$api = new NiftyCloud($params);

$params = array(
	'GroupName'=>'default(Linux)', 
);
$result = $api->describe_security_activities($params);
if ( $api->isError() ) {
	echo "Error\n";
} else {
	foreach($result->log as $log) {
		echo "{$log}\n";
	}
}