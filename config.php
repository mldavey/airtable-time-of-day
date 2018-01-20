<?php
/* Airtable Time of Day Update Config/Settings */

//Error Reporting
$error_reporting = error_reporting(E_ALL);

//Airtable variables
$api_key = "YOUR_API_KEY_HERE";
$basic_api_url = "https://api.airtable.com/url/for/your/base"; //in the format https://api.airtable.com/v#/xxxxxxxxxxxx/BaseName
$max_records = "10";
$time_column_name = "End Time"; //datetime column
$tod_column_name = "Time of Day"; //column to be updated

//User variables
$local_timezone = "EST5EDT";
$morning_end_string = "11:59:00";
$afternoon_end_string = "17:59:00";
$evening_end_string = "23:59:00";
$display_local_time = "m/d/Y H:i a";