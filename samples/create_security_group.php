<?php
include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'niftycloud.sdk.class.php');
include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'services'.DIRECTORY_SEPARATOR.'niftycloud.class.php');

$ret = @include('settings.php');
if ( !$ret ) {
	trigger_error('describe_security_groups - ~/samples/settings.php not found.', E_USER_ERROR);
}

if ( !isset($params) ) {
	trigger_error('describe_security_groups - no variable $params found in settings.php', E_USER_ERROR);
}

$api = new NiftyCloud($params);

$params = array('GroupName'=>'apitest', 'GroupDescription'=>'foo');
$result = $api->create_security_group($params);
if ( $api->isError() ) {
	echo "error\n";
} else {
	echo "result:{$result->return}\n";
}