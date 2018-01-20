<!DOCTYPE html>
<html>
	<head>
		<title>Airtable Time of Day Update</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css" integrity="sha384-Zug+QiDoJOrZ5t4lssLdxGhVrurbmBWopoEl+M6BdEfwnCJZtKxi1KgxUyJq13dy" crossorigin="anonymous">
	</head>
	<body>
		<div class="container">
			<div class="row">
		    	<div class="col-sm">
					<?php

					/* Airtable Time of Day Update
					* By: Maureen
					* URL: http://maureendavey.io
					*
					* Purpose: Read date information from an Airtable base
					* and update a "Time of Day" column with the appropriate
					* value (Morning, Afternoon, Evening) based on a specific
					* datetime column
					*
					* Set Morning, Afternoon, and Evening time cut-offs
					* in config.php
					*/

					require 'config.php';
					require 'functions.php'; 

					$error_reporting; //set in config.php

					// Set local timezone information
					date_default_timezone_set($local_timezone);

					// Create Date Objects
					$morning_end = new DateTime($morning_end_string, new DateTimeZone($local_timezone));
					$afternoon_end = new DateTime($afternoon_end_string, new DateTimeZone($local_timezone));
					$evening_end = new DateTime($evening_end_string, new DateTimeZone($local_timezone));

					$i=0; //for tracking how many entries were updated

					// Connect to Airtable via API URL set in config.php
					$headers = array("Authorization: Bearer $api_key");
					$filterByFormula = "AND(NOT({Time of Day} = 'Morning'), NOT({Time of Day} = 'Afternoon'), NOT({Time of Day} = 'Evening'), NOT({" . $time_column_name . "} = ''))"; // only pull entries that have not been updated yet and that already have the $time_column_name cell populated

					$entries = callCurl($basic_api_url, $headers, 0, $max_records, $filterByFormula, '', '');

					// Convert JSON to PHP
					$entries = json_decode($entries);

					echo "<h1>Airtable Time of Day Updater</h1>";

					echo "<p>Update up to $max_records records in the $tod_column_name column with the time of day (Morning, Afternoon, Evening) based on the datetime value of the $time_column_name column.</p>";

					if($entries) {
						// Update $tod_column_name to be Morning, Afternoon, or Evening
						echo "<ol>";
						foreach($entries->records as $record) {

							$entry_datetime_utc = new DateTime($record->fields->{$time_column_name}, new DateTimeZone("UTC")); //create a datetime object with UTC timezone based on Airtable data

							$entry_end_time = $entry_datetime_utc->setTimeZone(new DateTimeZone($local_timezone)); //convert to local timezone 

							if($entry_end_time->format("H:i") < $morning_end->format("H:i")) {
								$time_of_day = "Morning";
							} elseif($entry_end_time->format("H:i") < $afternoon_end->format("H:i")) {
								$time_of_day = "Afternoon";
							} elseif($entry_end_time->format("H:i") < $evening_end->format("H:i")) {
								$time_of_day = "Evening";
							} else {
								$time_of_day = "Wait...what time is it?";
							}

							//Update the $tod_column_name field for this entry
							$entry_id = $record->id;
							$data = array("fields" => array($tod_column_name => "$time_of_day"));
							$headers = array("Authorization: Bearer $api_key", "Content-Type: application/json");

							$result = callCurl("$basic_api_url/$entry_id", $headers, true, '', '', 'PATCH', $data);

							echo '<li class="p-3 mb-2 bg-success text-white">';
							echo '<strong>Entry Updated</strong><br />';
							echo 'Entry ID: ' . $record->id;
							echo '<br />';
							echo 'UTC ' . $time_column_name . ': ' . $record->fields->{$time_column_name};
							echo '<br />';
							echo 'Local ' . $time_column_name . ': ' . $entry_end_time->format($display_local_time);
							echo '<br />';
							echo 'Time of Day: ' . $time_of_day;
							echo '</li>';

							$i++;
						}

						echo "</ol>";

						if($i < 1) {
							echo "<p>No entries in need of updates were found.</p>";
						} else {
							echo "<p>A total of $i entries were updated.</p>";
						}

					} else {
						echo "<p>Either cURL returned an error, or the connection timed out.</p>";
					}

					?>
				</div>
			</div>
		</div>
	</body>
</html>