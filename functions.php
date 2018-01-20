<?php

	/* Airtable Time of Day Update
	* functions.php
	* By: Maureen
	* URL: http://maureendavey.io
	*
	* Purpose: Hold functions for the Airtable Time
	* of Day script.
	*/

	function callCurl($curl_url, $curl_headers, $curl_include_header, $curl_records, $curl_filter, $curl_request, $curl_data_update) {

		if($curl_records) {
			if(strpos($curl_url, '?') !== FALSE) {
				$append = '&';
			} else {
				$append = '?';
			}
			$curl_url .= $append . 'maxRecords=' . $curl_records . '&view=Grid%20view';
		}

		if($curl_filter) {
			if(strpos($curl_url, '?') !== FALSE) {
				$append = '&';
			} else {
				$append = '?';
			}
			$curl_filter = urlencode($curl_filter);
			$curl_url .= $append . 'filterByFormula=' . $curl_filter;
		}

		$ch = curl_init($curl_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, $curl_include_header);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_headers);
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

		if($curl_request === 'PATCH') {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH'); //Airtable recommends using a PATCH request for sending an update
		}

		if($curl_data_update) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($curl_data_update));
		}

		// Retrieve the response
		$result = curl_exec($ch);

		if(curl_error($ch)) {
			echo '<div class="p-3 mb-2 bg-danger text-white" style="word-wrap: break-word;">';
		    echo 'Error when calling ' . $curl_url . 'via cURL:';
		    echo '<br /><br />';
		    echo curl_error($ch);
		    echo '</div>';
		}

		// Close cURL
		curl_close($ch);

		return $result;
	}