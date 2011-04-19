<?php
include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'niftycloud.sdk.class.php');
include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'services'.DIRECTORY_SEPARATOR.'niftycloud.class.php');

$ret = @include('settings.php');
if ( !$ret ) {
	trigger_error('AvailabilityZonesTest::setUp() - ~/samples/settings.php not found.', E_USER_ERROR);
}

if ( !isset($params) ) {
	trigger_error('AvailabilityZonesTest::setUp() - no valiable $params found in settings.php', E_USER_ERROR);
}

$api = new NiftyCloud($params);

$result = $api->describe_instances();
if ( $api->isError() ) {
	echo "error\n";
} else {
	foreach($result->reservationSet->item as $item) {
		echo "{$item->instancesSet->item->instanceId}: {$item->instancesSet->item->instanceState->name}({$item->instancesSet->item->instanceType})\n";
	}
}