# Airtable Time of Day

This is a PHP script to update a specified column with the relative time of day (Morning, Afternoon, Evening) based on another datetime column in the same row. The script looks for rows that have not yet been updated with one of those values and pulls up to the number specified in the $max_records variable.

Variables/settings can be updated in config.php. The time values for what qualifies as 'Morning', 'Afternoon', and 'Evening' can be set there as well.

Based on the airtable-wrapper code by focuswish ( https://github.com/unshift/airtable-wrapper ) and Neilforce by kgranat ( https://github.com/kgranat/NeilForce ).