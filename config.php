<?php
/* Airtable Time of Day Update Config/Settings */

//Error Reporting
$error_reporting = error_reporting(E_ALL);

//Airtable variables
$api_key = "YOUR_KEY_HERE";
$max_records = "10";
$base_name = "YOUR_BASE_NAME";
$time_column_name = "End Time"; //datetime column
$tod_column_name = "Time of Day"; //column to be updated

//User variables
$local_timezone = "EST5EDT";
$morning_end_string = "11:59:00";
$afternoon_end_string = "17:59:00";
$evening_end_string = "23:59:00";
$display_local_time = "m/d/Y H:i a";