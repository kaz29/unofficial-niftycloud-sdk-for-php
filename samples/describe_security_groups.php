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

$params = array('GroupName'=>'default(Linux)');
// $params = array('GroupName'=>array('default(Linux)'));
// $params = array('Filter'=>array('description'=>'foo', 'group-name'=>array('test')));
// $params = array('Filter'=>array('group-name'=>array('default','test')));
$result = $api->describe_security_groups($params);
if ( $api->isError() ) {
	echo "error\n";
} else {
	foreach($result->securityGroupInfo->item as $item) {
		echo "{$item->groupName}: {$item->groupStatus}\n";
		$index = 0;
		foreach($item->ipPermissions->item as $ip_permission) {
			$index++;
$buf =<<<EOT
\tip_permission[{$index}]
\t\tipProtocol:{$ip_permission->ipProtocol}
\t\tfromPort:{$ip_permission->fromPort}
\t\ttoPort:{$ip_permission->toPort}
\t\tinOut:{$ip_permission->inOut}
\t\tipRanges\n
EOT;
			foreach($ip_permission->ipRanges->item as $range) {
				$buf .= "\t\t\t{$range->cidrIp}\n";
			}
			echo $buf;
		}
		
	}
}