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

					include 'config.php';

					$error_reporting; //set in config.php

					// Set local timezone information
					date_default_timezone_set($local_timezone);

					// Create Date Objects
					$morning_end = new DateTime($morning_end_string, new DateTimeZone($local_timezone));
					$afternoon_end = new DateTime($afternoon_end_string, new DateTimeZone($local_timezone));
					$evening_end = new DateTime($evening_end_string, new DateTimeZone($local_timezone));

					// Connect to the "Entries" table in this Airtable base
					$headers = array("Authorization: Bearer $api_key");
					$filterByFormula = "AND(NOT({Time of Day} = 'Morning'), NOT({Time of Day} = 'Afternoon'), NOT({Time of Day} = 'Evening'))"; // only pull entries that have not been updated yet
					$filterByFormula = urlencode($filterByFormula);

					$ch = curl_init("https://api.airtable.com/v0/xxxxxxxxxxxx/$base_name?maxRecords=$max_records&view=Grid%20view&filterByFormula=$filterByFormula");
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
					curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

					// Save data to $entries
					$entries = curl_exec($ch);

					// Close cURL
					curl_close($ch);

					// Convert JSON to PHP
					$entries = json_decode($entries);

					echo "<h1>Airtable Time of Day Updater</h1>";

					echo "<p>Update up to $max_records records in the $tod_column_name column with the time of day (Morning, Afternoon, Evening) based on the datetime value of the $time_column_name column.</p>";

					echo "<ol>";

					// Update $tod_column_name to be Morning, Afternoon, or Evening
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
							$time_of_day = "What time is it?";
						}

						//Update the $tod_column_name field for this entry
						$entry_id = $record->id;
						$data = array("fields" => array($tod_column_name => "$time_of_day"));

						$ch = curl_init("https://api.airtable.com/v0/xxxxxxxxxxxx/$base_name/$entry_id");
						$headers = array("Authorization: Bearer $api_key", "Content-Type: application/json");

						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
						curl_setopt($ch, CURLOPT_HEADER, true);
						curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
						curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
						curl_setopt($ch, CURLOPT_FAILONERROR, true);
						curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);


						// Send the updated data
						$result = curl_exec($ch);

						echo "<li><strong>Entry Updated</strong>";
						echo "<br />";
						echo "Entry ID: $record->id";
						echo "<br />";
						echo "UTC End Time: " . $record->fields->{$time_column_name};
						echo "<br />";
						echo "Local End Time: " . $entry_end_time->format($display_local_time);
						echo "<br />";
						echo "Time of Day: $time_of_day";

						if(curl_error($ch)) {
						    echo "<br />";
						    echo "Error: " . curl_error($ch);
						}

						// Close cURL
						curl_close($ch);

						echo "<br /><br /></li>";
					}

					echo "</ol>";

					?>
				</div>
			</div>
		</div>
	</body>
</html>