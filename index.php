<?php
/*
 * Copyright 2009 Mark J. Headd
 * 
 * This file is part of Toronto Child Care Finder
 * 
 * Toronto Child Care Finder is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Toronto Child Care Finder is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Toronto Child Care Finder.  If not, see <http://www.gnu.org/licenses/>.
 *  
 */

// Function to load class files as needed.
function __autoload($class_name) {
    require_once ('classes/'.strtolower($class_name).'.class.php');
}

// Variable to hold details of a child carre center.
$centerDetails = "";

// Database access information.
$host = "";
$database = "";
$user = "";
$password = "";
$table = "";

// Query to run to find child care center in a zip code.
$query = "SELECT * FROM $table WHERE postal_code LIKE ";

// List of available options
$options = Array("#next", "#previous", "#reset");

// Function to navigate through result set of child care providers
// TODO: Backward navigation via #previous is still a bit dodgy. Needs more work.
function determineStep($step, $message, $numSteps) {
	GLOBAL $options;
	if(!in_array($message, $options)) {
		die("You must enter #next or #reset.");
	}
	else {
		switch($message) {
			case "#next":
				return $step;
			case "#previous":
				return $step - ($numSteps - 2);
			case "#reset":
				die("To search for a child care center, enter a postal code FSA.<reset>");
		}
	}
}

try {
	
	// Create a new IMified Bot Object and get the relevent properties.
	$ChildCareBotObject = new imified($_POST);
	$message = $ChildCareBotObject->getMsg();
	$postalCode = $ChildCareBotObject->getValue(0);
	$step = ($ChildCareBotObject->getStep()-1);
	$numSteps = count($ChildCareBotObject->getAllValues());
	$network = $ChildCareBotObject->getNetwork();
  	
	// If this is the first step, they must enter a postal code FSA.
	if ($step == 0 && (strlen($message) != 3)) {
		die("You must enter a valid postal code.<reset>");
	}
	
	// If this is not the dirst step, determine which record to display.
	if ($step > 0) {
		$step = determineStep($step, $message, $numSteps);
	}
	
	$connection = new dbconnection($host, $user, $password);
	$connection->selectDB($database);
	$query .= '\''.strtoupper($postalCode).'%\' LIMIT '.$step.',1';
	$result = $connection->runQuery($query);
	
	if ($connection->getNumRowsAffected() == 0) {
		if($step == 0) {
			die("There are no registered child care providers in that postal code. Try another.<reset>");	
		}
		else {
			die("There are no more proivders in that postal code. Send #reset to search again.");
		}
				
	}
		$details = mysql_fetch_assoc($result);
		if ($network == 'SMS' || $network == 'TWITTER') {			
			$centerDetails .= $details["location_short_name"].". ";
			$centerDetails .= $details["street_num"]." ";
			$centerDetails .= $details["street"].". ";
			$centerDetails .= $details["postal_code"].". ";
			$centerDetails .= $details["phone"]." - ";
			$centerDetails .= "Total Spaces: ".$details["tot_space"];
		}
		
		else {
			$centerDetails .= "Name: ".$details["location_name"]."<br>";
			$centerDetails .= "Address: ".$details["street_num"]." ".$details["street"]." ".$details["postal_code"]."<br>";
			$centerDetails .= "Phone: ".$details["phone"]."<br>";
			$centerDetails .= "0-18 months spaces: ".$details["ig_space"]."<br>";
			$centerDetails .= "18-30 months spaces: ".$details["tg_space"]."<br>";
			$centerDetails .= "30-60 months spaces: ".$details["pg_space"]."<br>";
			$centerDetails .= "5-12 years spcaes: ".$details["sg_space"]."<br>";
			$centerDetails .= "All age groups spaces: ".$details["tot_space"]."<br>";	
		}
		
		// Write out the response to the user.
		if ($step == 0) {
			echo "Send #next to view another or #reset to start over. <br>";
		}
		echo $centerDetails;
}

catch (Exception $ex) {
	die("Sorry. The Bot is having some issues: ".$ex->getMessage()."<reset>");	
}


?>