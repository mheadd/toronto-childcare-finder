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
 * 
 * This script can be used to convert the data file containing licensed Child Care
 * centers in Toronto from XML to CSV format for easier import into a database
 * like MySQL.
 * 
 * XML file is availalbe at http://www.toronto.ca/open/datasets/child-care/
 * 
 * To use this script, change the path to childcare.xml to your download location.
 * Invoke via the command line:
 * 
 * 		php xmltocsv.php > ~/childcare.csv
 *  
 */



try {

  	// Change the path to childcare.xml to match your download location.
	$doc = simplexml_load_file('path_to/childcare.xml');
  	$centers = $doc->CHILDCARE;

  foreach ($centers as $center) {

	$csv = '';
	$csv .= trim($center->LOC_ID).'|';
	$csv .= trim($center->LOC_NAME).'|';
	$csv .= trim($center->LOC_SHORT_NAME).'|';
	$csv .= trim($center->AUSPICE).'|';
	$csv .= trim($center->STR_NO).'|';
	$csv .= trim($center->STREET).'|';
	$csv .= trim($center->UNIT).'|';
	$csv .= trim($center->PCODE).'|';
	$csv .= trim($center->WARD).'|';
	$csv .= trim($center->PHONE).'|';
	$csv .= trim($center->BLDG_TYPE).'|';
	$csv .= trim($center->BLDG_NAME).'|';
	$csv .= trim($center->IGSPACE).'|';
	$csv .= trim($center->TGSPACE).'|';
	$csv .= trim($center->PGSPACE).'|';
	$csv .= trim($center->SGSPACE).'|';
	$csv .= trim($center->TOTSPACE).'|';
	$csv .= trim($center->GEO_ID).'|';
	$csv .= "\n";
	
	echo $csv;

  }  

}

catch (Exception $ex) {
	die("ERROR: ".$ex->getMessage());
}

?>