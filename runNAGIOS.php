#!/usr/bin/php
<?
error_reporting(0);

//TODO:


$pluginName ="Nagios";
$myPid = getmypid();

$DEBUG=false;

$skipJSsettings = 1;
include_once("/opt/fpp/www/config.php");
include_once("/opt/fpp/www/common.php");
include_once("functions.inc.php");
include_once("commonFunctions.inc.php");
require ("lock.helper.php");
define('LOCK_DIR', '/tmp/');
define('LOCK_SUFFIX', '.lock');
$messageQueue_Plugin = "MessageQueue";
$MESSAGE_QUEUE_PLUGIN_ENABLED=false;


$logFile = $settings['logDirectory']."/".$pluginName.".log";

$messageQueuePluginPath = $pluginDirectory."/".$messageQueue_Plugin."/";

$messageQueueFile = urldecode(ReadSettingFromFile("MESSAGE_FILE",$messageQueue_Plugin));



if(($pid = lockHelper::lock()) === FALSE) {
	exit(0);

}

$fppSystems = getFPPSystems();

if ($fppSystems != "") {
	
	print_r($fppSystems);
}

lockHelper::unlock();
exit(0);
?>