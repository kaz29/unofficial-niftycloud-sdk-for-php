<?php
include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'niftycloud.sdk.class.php');
include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'services'.DIRECTORY_SEPARATOR.'niftycloud.class.php');

$ret = @include('settings.php');
if ( !$ret ) {
	trigger_error('revoke_security_group_ingress - ~/samples/settings.php not found.', E_USER_ERROR);
}

if ( !isset($params) ) {
	trigger_error('revoke_security_group_ingress - no variable $params found in settings.php', E_USER_ERROR);
}

$api = new NiftyCloud($params);

$params = array(
	'GroupName'=>'apitest', 
	'IpPermissions' => array(
		array(
			'IpProtocol' => 'TCP',
			'FromPort' => '80',
			'InOut' => 'IN',
			'CidrIp' => '0.0.0.0',
		),
/*		array(
			'IpProtocol' => 'HTTP',
			'InOut' => 'IN',
			'CidrIp' => array('0.0.0.0'),
		),
*/
	)
);
$result = $api->revoke_security_group_ingress($params);
if ( $api->isError() ) {
	echo "error\n";
} else {
	echo "result:{$result->return}\n";
}