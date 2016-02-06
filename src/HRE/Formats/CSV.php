<?php
namespace HRE\Formats;

use HRE\Data\DataManager;

/**
CSV creation class
*/
class CSV
{
	public function __construct()
	{

	}

	/**
	* Generate CSV file
	* @param $manager	Data manager to retrieve data from
	* @param $send_to_browser If CSV will be send to browser, default true
	*/
	public function generateCSV(DataManager $manager, $send_to_browser = true)
	{
		// get data from manager
		$result = $manager->data();

		if ($send_to_browser) {
			// A name with a time stamp, to avoid duplicate filenames
			$filename = $manager->exportName().'.csv';

			// Tells the browser to expect a CSV file and bring up the
			// save dialog in the browser
			header( 'Content-Type: text/csv' );
			header( 'Content-Disposition: attachment;filename='.$filename);

			// This opens up the output buffer as a "file"
			$fp = fopen('php://output', 'w');

			if ($result) {
				// Get the first record
				$hrow = $result[0];
				$delimiter = ';';
				// Extracts the keys of the first record and writes them
				// to the output buffer in CSV format
				fputcsv($fp, array_keys($hrow), $delimiter);

				// Then, write every record to the output buffer in CSV format
				foreach ($result as $data) {
					fputcsv($fp, $data, $delimiter);
				}
			}
			// Close the output buffer (Like you would a file)
			fclose($fp);

			// Send the size of the output buffer to the browser
			$contLength = ob_get_length();
// 			header( 'Content-Length: '.$contLength);
		} else {
			return $result;
		}
	}

}
