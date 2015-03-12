<?php
function runRemoteSSHCMD($host,$user,$cmd) {
	$server = $host;
	//ip address will work too i.e. 192.168.254.254 just make sure this is your public ip address not private as is the example

	//specify your username
	$username = $user;

	$password = "raspberry";
	//select port to use for SSH
	$port = "22";



	//form full command with ssh and command, you will need to use links above for auto authentication help
	$cmd_string = "ssh -p ".$port." ".$username."@".$server." ".$cmd;

	logEntry("remote command: ".$cmd_string);
	//this will run the above command on server A (localhost of the php file)
	exec($cmd_string, $output);

	if($output[0] == null || $output[0]=="") {
		$username = "fpp";
		//try as fpp user
		//form full command with ssh and command, you will need to use links above for auto authentication help
		$cmd_string = "ssh -p ".$port." ".$username."@".$server." ".$cmd;

		logEntry("user: 'pi' did not work trying with 'fpp': remote command: ".$cmd_string);
		//this will run the above command on server A (localhost of the php file)
		exec($cmd_string, $output);
	}

	//return the output to the browser
	//This will output the uptime for server B on page on server A
	//echo '<pre>';
	//print_r($output);
	return $output[0];
}
function getFPPSystems() {
	
	$results = "";

	//$url = $_SERVER['PHP_SELF']."/fppjson.php?command=getFPPSystems";
	
	$url = "http://localhost/fppjson.php?command=getFPPSystems";
	
	//echo "URL: ".$url."<br/>\n";
	$contents = file_get_contents($url);
	$contents = utf8_encode($contents);
	$results = json_decode($contents);
	
	//print_r($results);
	
	
	
	return $results;
	
}
//check all the event files for a string matching this and return true/false if exist
function checkEventFilesForKey($keyCheckString) {
	global $eventDirectory;
	
	$keyExist = false;
	$eventFiles = array();
	
	$eventFiles = directoryToArray($eventDirectory, false);
	foreach ($eventFiles as $eventFile) {
	
   	 if( strpos(file_get_contents($eventFile),$keyCheckString) !== false) {
        // do stuff
        $keyExist= true;
        break;
       // return $keyExist;
   	 }
	}
	
	return $keyExist;
	
}

function logEntry($data) {

	global $logFile,$myPid,$callBackPid;
	
	if($callBackPid != "") {
		$data = $_SERVER['PHP_SELF']." : [".$callBackPid.":".$myPid."] ".$data;
	} else { 
	
		$data = $_SERVER['PHP_SELF']." : [".$myPid."] ".$data;
	}
	$logWrite= fopen($logFile, "a") or die("Unable to open file!");
	fwrite($logWrite, date('Y-m-d h:i:s A',time()).": ".$data."\n");
	fclose($logWrite);
}


function escapeshellarg_special($file) {
	return "'" . str_replace("'", "'\"'\"'", $file) . "'";
}


//function send the message

function sendCommand($projectorCommand) {

	global $pluginName,$myPid,$pluginDirectory,$DEVICE,$DEVICE_CONNECTION_TYPE,$IP,$PORT;
	
	//$DEVICE = ReadSettingFromFile("DEVICE",$pluginName);
	//$DEVICE_CONNECTION_TYPE = ReadSettingFromFile("DEVICE_CONNECTION_TYPE",$pluginName);
	//$IP = ReadSettingFromFile("IP",$pluginName);
	//$PORT = ReadSettingFromFile("PORT",$pluginName);
	
	//$ENABLED = ReadSettingFromFile("ENABLED",$pluginName);

//	logEntry("reading config file");
//	logEntry(" DEVICE: ".$DEVICE." DEVICE_CONNECTION_TYPE: ".$DEVICE_CONNECTION_TYPE." IP: ".$IP. " PORT: ".$PORT);

	//logEntry("INSIDE SEND");
	//# Send line to  Projector
	$cmd = $pluginDirectory."/".$pluginName."/proj.php ";

	$cmd .= "-d".$DEVICE_CONNECTION_TYPE;


	switch ($DEVICE_CONNECTION_TYPE) {

		case "SERIAL":

			$SERIALCMD = " -s".$DEVICE." -c".$projectorCommand;
			$cmd .= $SERIALCMD;
				
			break;
				
		case "IP":
			$IPCMD = " -h".$IP. " -c".$projectorCommand;
			$cmd .= $IPCMD;
			break;
				
	}

	$cmd .= " -z".$myPid;
	
	logEntry("COMMAND: ".$cmd);
	system($cmd,$output);

	//system($cmd."\"".$line."\" ".$DEVICE,$output);
}

function processSequenceName($sequenceName) {
	
	global $projectorONSequence, $projectorOFFSequence,$projectorVIDEOSequence;
	
	logEntry("Sequence name: ".$sequenceName);

	$sequenceName = strtoupper($sequenceName);

	switch ($sequenceName) {

		case "PROJ-ON.FSEQ":

			logEntry("Projector On");
			sendCommand("ON");
			break;
			exit(0);
			
			case "PROJ-OFF.FSEQ":
			
				logEntry("Projector OFF");
				sendCommand("OFF");
				break;
				exit(0);
				
				case "PROJ-VIDEO-INPUT.FSEQ":
				
					logEntry("Projector Video Input Select");
					sendCommand("VIDEO");
					break;
					exit(0);
				
		default:
			logEntry("We do not support sequence name: ".$sequenceName." at this time");
				
			exit(0);
				
	}
	


}
function processCallback($argv) {

	global $DEBUG,$pluginName;
	
	
	if($DEBUG)
		print_r($argv);
	//argv0 = program
	
	//argv2 should equal our registration // need to process all the rgistrations we may have, array??
	//argv3 should be --data
	//argv4 should be json data
	
	$registrationType = $argv[2];
	$data =  $argv[4];
	
	logEntry("PROCESSING CALLBACK");
	$clearMessage=FALSE;
	
	switch ($registrationType)
	{
		case "media":
			if($argv[3] == "--data")
			{
				$data=trim($data);
				logEntry("DATA: ".$data);
				$obj = json_decode($data);
	
				$type = $obj->{'type'};
	
				switch ($type) {
						
					case "sequence":
	
						//$sequenceName = ;
						processSequenceName($obj->{'Sequence'});
							
						break;
					case "media":
							
						logEntry("We do not understand type media at this time");
							
						exit(0);
	
						break;
	
					default:
						logEntry("We do not understand: type: ".$obj->{'type'}. " at this time");
						exit(0);
						break;
	
				}
	
	
			}
	
			break;
			exit(0);
				
		default:
			exit(0);
	
	}
	


}
?>