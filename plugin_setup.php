<?php
//$DEBUG=true;

$skipJSsettings = 1;
//include_once '/opt/fpp/www/config.php';
include_once '/opt/fpp/www/common.php';

$pluginName = "Nagios";

include_once 'functions.inc.php';
include_once 'commonFunctions.inc.php';
//include 'projectorCommands.inc';
$myPid = getmypid();

//arg0 is  the program
//arg1 is the first argument in the registration this will be --list
//$DEBUG=true;
$logFile = $settings['logDirectory']."/".$pluginName.".log";
$sequenceExtension = ".fseq";

//logEntry("open log file: ".$logFile);



$DEBUG = false;



if(isset($_POST['submit']))
{
	

	
	
	$ENABLED=$_POST["ENABLED"];



	

} 


	
	//$ENABLED = ReadSettingFromFile("ENABLED",$pluginName);
	$ENABLED = $pluginSettings['ENABLED'];
	

	$fppInstallLocation = "/opt/fpp";
	


	$fppSystems = getFPPSystems();
	
	if ($fppSystems != "") {
	
		//print_r($fppSystems);
		
		$fppCount = count($fppSystems);
		//echo "Found: ".$fppCount." FPP instances <br/> \n";
		echo "<table border=\"1\" cellspacing=\"3\" cellpadding=\"3\"> \n";
		
		echo "<tr> \n";
		echo "<td> \n";
		echo "HOST \n";
		echo "</td> \n";
		
		echo "<td> \n";
		echo "IP \n";
		echo "</td> \n";
		echo "<td> \n";
		echo "FPP Mode \n";
		echo "</td> \n";
		echo "<td> \n";
		echo "Platform \n";
		echo "</td> \n";
		echo "<td> \n";
		echo "Running Version \n";
		echo "</td> \n";
		
		echo "<td> \n";
		echo "Local Git Version \n";
		echo "</td> \n";
		
		echo "<td> \n";
		echo "Local Running Branch \n";
		echo "</td> \n";
		
		echo "<td> \n";
		echo "Remote Git Version <br/> Available for platform \n";
		echo "</td> \n";
		
		echo "</tr> \n";
		
		for($i=0;$i<=$fppCount-1;$i++) {
		echo "<tr> \n";	
			$fppName = $fppSystems[$i]->HostName;
			$fppIP = $fppSystems[$i]->IP;
			$fppMode = $fppSystems[$i]->fppMode;
			$fppPlatform = $fppSystems[$i]->Platform;
			
			
			echo "<td> \n";
			echo $fppName."\n";
			echo "</td> \n";
			
			echo "<td> \n";
			echo $fppIP."\n";
			echo "</td> \n";
			
			echo "<td> \n";
			echo $fppMode."\n";
			echo "</td> \n";
			
			echo "<td> \n";
			echo $fppPlatform."\n";
			echo "</td> \n";
			
			echo "<td> \n";
			$command = "git --git-dir=".$fppInstallLocation."/.git/ describe --tags";
			$result = runRemoteSSHCMD($fppIP, "pi", $command);
			echo $result;
			//echo '</pre>';
			echo "</td> \n";
			
			echo "<td> \n";
			$command = "git --git-dir=".$fppInstallLocation."/.git/ rev-parse --short HEAD";
			$local_git = runRemoteSSHCMD($fppIP, "pi", $command);
			echo $local_git;
			//echo '</pre>';
			echo "</td> \n";
			
			echo "<td> \n";
			//command that will be run on server B
			$command = "git --git-dir=".$fppInstallLocation."/.git/ branch --list | grep '\\*' | awk '{print \$2}'";
			$git_branch = runRemoteSSHCMD($fppIP, "pi", $command);
			echo $git_branch;
			//echo '</pre>';
			echo "</td> \n";
			
			echo "<td> \n";
			$command = "git ls-remote --heads https://github.com/FalconChristmas/fpp | grep 'refs/heads/$git_branch\$' | awk '$1 > 0 { print substr($1,1,7)}'";
			$remote_git = runRemoteSSHCMD($fppIP, "pi", $command);
			//$remote_git = "1234";
			if($local_git != $remote_git) {
				echo "<a href=\"http://".$fppIP."/about.php\">UPDATE</a> <br/>\n";
			}
			echo $remote_git;
			//echo '</pre>';
			echo "</td> \n";
			echo "</tr> \n";
		}
		echo "</table> \n";
		
	} else {
		echo "No FPP systems other than localhost found<br/> \n";
		
	}
?>

<html>
<head>
</head>

<div id="Nagios" class="settings">
<fieldset>
<legend>Nagios control Support Instructions</legend>

<p>Known Issues:
<ul>
<li>NONE</li>
</ul>

<p>Configuration:
<ul>
<li>username: nagiosadmin</li>
<li>password: what you configured: falcon?</li>
<li>Run the following on THIS machine with user pi or fpp:</li>
<li> cd ~ (go to home directory /home/pi|fpp)</li>
<li> ssh-keygen -t rsa</li>
<li> ssh pi|or|fpp@(each remote PI or BBB) mkdir -p .ssh</li>
<li> cat .ssh/id_rsa.pub | ssh pi|or|fpp@(each remote PI or bbb) 'cat >> .ssh/authorized_keys'</li>
<li> test by ssh'ing into each machine ssh pi|fpp@(each pi or bbb)</li>
<li> You should not be prompted with a password!</li>
</ul>

<form method="post" action="http://<? echo $_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT']?>/plugin.php?plugin=<? echo $pluginName;?>&page=plugin_setup.php">

<p/>

<?
echo "ENABLE PLUGIN: ";

//if($ENABLED == "on" || $ENABLED == 1) {
//	echo "<input type=\"checkbox\" checked name=\"ENABLED\"> \n";
	PrintSettingCheckbox("Nagios", "ENABLED", $restart = 0, $reboot = 0, "1", "0", $pluginName = $pluginName, $callbackName = "");
//} else {
//	echo "<input type=\"checkbox\"  name=\"ENABLED\"> \n";
//}
echo "<p/>\n";


?>
<p/>
<input id="submit_button" name="submit" type="submit" class="buttons" value="Save Config">
</form>



<p>To report a bug, please file it against the Projector Control plugin project on Git: https://github.com/LightsOnHudson/FPP-Plugin-Nagios

</fieldset>
</div>
<br />
</html>
