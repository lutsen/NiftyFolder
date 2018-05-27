<?php

/*
 * Connect to Google Drive.
 */

class Drive {

	public $client;

	/**
	 * Set up a Google_Client object.
	 */
	function __construct() {
		$this->client = new Google_Client();
		$this->client->setAuthConfig( ROOT_PATH . '/client_secret.json' );

		// Next two lines needed to obtain refresh_token.
		// The offline access type allows NiftyFolder to access Google Drive when the owner is not present.
		$this->client->setAccessType('offline');
		$this->client->setApprovalPrompt('force');

		// The access scope to the Google Drive.
		// https://www.googleapis.com/auth/drive.readonly allows read-only access to file metadata and file content
		$this->client->addScope('https://www.googleapis.com/auth/drive.readonly');
	}

	public function service() {
		// Credentials check
		$filename = ROOT_PATH . '/credentials.json';
		if ( file_exists( $filename ) ) {

			// Read credentials into a string
			$fp = fopen( $filename, 'r' );
			$credentials = fread( $fp, filesize( $filename ) );
			fclose($fp);

			$this->client->setAccessToken(
				json_decode($credentials, true)
			);

			// Create new Google_Service_Drive $service object
			return new Google_Service_Drive( $this->client );

		} else {
			throw new \Exception($filename . ' does not exist.');
		}
	}

}

?>