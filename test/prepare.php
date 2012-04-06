<?php
set_include_path(dirname(__FILE__).PATH_SEPARATOR.dirname(dirname(dirname(__FILE__))) . PATH_SEPARATOR . get_include_path());

require_once 'PHPUnit/Autoload.php';
require_once 'Stagehand/Autoload.php';

$loader = Stagehand_Autoload::legacyLoader();
$loader->addNamespace('Stagehand');
Stagehand_Autoload::register($loader);

Stagehand_LegacyError_PHPError::enableConversion();
