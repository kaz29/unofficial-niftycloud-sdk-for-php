<?php
include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'niftycloud.sdk.class.php');
include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'services'.DIRECTORY_SEPARATOR.'niftycloud.class.php');

$ret = @include('settings.php');
if ( !$ret ) {
	trigger_error('run_instances - ~/samples/settings.php not found.', E_USER_ERROR);
}

if ( !isset($params) ) {
	trigger_error('run_instances - no variable $params found in settings.php', E_USER_ERROR);
}

$api = new NiftyCloud($params);
$result = $api->run_instances(array(
	'ImageId' => 1,
	'KeyName' => $params['key_name'],
	'InstanceType' => 'mini',
	'InstanceId' => $params['instance_id'],
	'AccountingType' => NiftyCloud::ACCOUNTING_TYPE_PAYPER,
	'Admin' => null,
	'Password' => $params['instance_id'],
		// Firewall機能が有効でない環境の場合は以下を指定するとエラーが発生します。
	'SecurityGroup' => 'default(Linux)',
));
if ( $api->isError() ) {
	echo "Error\n";
} else {
	echo "{$result->instancesSet->item->instanceId}: {$result->instancesSet->item->instanceState->name}\n";
}