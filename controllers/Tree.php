<?php

/*
 * Work with folder tree.
 */

class Tree {

	/**
	 * Turn text string into a valid URL readable string.
	 * From http://stackoverflow.com/questions/2955251/php-function-to-make-slug-url-string
	 *
	 * @param string	$text	String to convert.
	 *
	 * @return string	Converted string.
	 */
	private function slugify( $text ) {
		// replace non letter or digits by -
		$text = preg_replace('~[^\pL\d]+~u', '-', $text);

		// transliterate
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

		// remove unwanted characters
		$text = preg_replace('~[^-\w]+~', '', $text);

		// trim
		$text = trim($text, '-');

		// remove duplicate -
		$text = preg_replace('~-+~', '-', $text);

		// lowercase
		$text = strtolower($text);

		if (empty($text))
		{
			return 'n-a';
		}

		return $text;
	}

	/**
	 * Clean name if it starts with a number between square brackets to force a file-order in Google Drive.
	 * Example: "[1] Home" becomes "Home".
	 *
	 * @param string	$name	Name to clean.
	 *
	 * @return string	Cleaned namme.
	 */
	private function cleanName( $name ) {
		if ( substr( $name, 0, 1 ) == '[' ) {
			$name = trim( substr( $name, strpos($name, ']')+1 ) );
		}
		return $name;
	}

	/**
	 * Returns a simplified, more readable mimetype for mimetypes supplied by Google.
	 * Returns "unsuppoterd" if mimetype is not in the list.
	 *
	 * @param string	$mimetype	Mimetype supplied by Google.
	 *
	 * @return string	Simplified mimetype.
	 */
	private function simpleMimeType( $mimetype ) {
		switch ( $mimetype ) {
			case 'application/vnd.google-apps.document':
				return 'document';
				break;
			case 'application/vnd.google-apps.folder':
				return 'folder';
				break;

			case 'image/jpeg':
				return 'jpg';
				break;
			case 'image/png':
				return 'png';
				break;
			case 'image/gif':
				return 'gif';
				break;

			case 'application/pdf':
				return 'pdf';
				break;
			case 'application/rtf':
				return 'rtf';
				break;
			case 'application/msword':
				return 'word';
				break;
			case 'application/x-iwork-keynote-sffkey':
				return 'keynote';
				break;
			case 'application/x-iwork-pages-sffpages':
				return 'pages';
				break;
			case 'application/x-iwork-numbers-sffnumbers':
				return 'numbers';
				break;

			case 'application/x-compressed':
			case 'application/x-zip-compressed':
			case 'application/zip':
			case 'multipart/x-zip':
				return 'zip';
				break;

			case 'video/mp4':
				return 'mp4';
				break;
			case 'video/quicktime':
				return 'quicktime';
				break;

			default:
				return 'unsupported';
				break;
		}
	}

	/**
	 * Read the meta-data of all the folders in the connected Google Drive.
	 *
	 * @param object 	$service		A Google_Service_Drive object.
	 * @param string 	$parent_id		ID of the parent folder. 'root' is the root folder of the connected Google Drive account.
	 * @param boolean 	$folders_only	If true only folders are returned, on false (default) all files are returned.
	 *
	 * @return array	An array with the folder name as the key and the folder id as the value.
	 */
	public function getChildFolders( $service, $parent_id, $folders_only = false ) {
		$result = [];

		$q = "'" . $parent_id . "' in parents";
		if ( $folders_only ) {
			$q .= " and mimeType='application/vnd.google-apps.folder'";
		}

		// Use listFiles instead of listChildren to also get filenames
		$response = $service->files->listFiles( [
			'q' => $q,
			'spaces' => 'drive',
			'fields' => 'files(id, name, mimeType)',
			'orderBy' => 'name'
		] );

		foreach ($response->files as $file) {
			$type = $this->simpleMimeType( $file->mimeType );

			$class = '';
			if ( !in_array( $type, array( 'jpg', 'png', 'gif', 'folder', 'document' ) ) ) {
				$class = 'download';
			}

			$result[$file->id] = [
				'name' => $this->cleanName( $file->name ),
				'id' => $file->id,
				'mimeType' => $file->mimeType,
				'type' => $type,
				'slug' => $this->slugify( $this->cleanName( $file->name ) ),
				'class' => $class
			];

			if ( $file->mimeType == 'application/vnd.google-apps.folder' ) {
				$result[$file->id]['children'] = $this->getChildFolders( $service, $file->id, $folders_only ); // Recursive
			}
		}

		return $result;
	}

	/**
	 * Read tree from cached json file or from Google Drive.
	 * If the json file does not exist it is created after the tree is loaded from Google Drive.
	 * The json file is located at ROOT_PATH.'/tree.json'.
	 *
	 * @param object 	$service		A Google_Service_Drive object.
	 * @param string 	$parent_id		ID of the parent folder. 'root' is the root folder of the connected Google Drive account.
	 *
	 * @return array	An array with the folder name as the key and the folder id as the value.
	 */
	function getCachedTree( $service, $parent_id ) {

		$filename = ROOT_PATH . '/tree.json';
		if ( file_exists( $filename ) ) {

			// Load from json file
			$fp = fopen( $filename, 'r' );
			$tree = json_decode( fread( $fp, filesize( $filename ) ), true );
			fclose($fp);

		} else {

			// Load from Google Drive
			$tree = $this->getChildFolders( $service, $parent_id );
			$fp = fopen( $filename, 'wb' );
			fwrite( $fp, json_encode( $tree ) );
			fclose( $fp );

		}

		return $tree;
	}

	/**
	 * Find specific node in a tree by the value of a certain property.
	 *
	 * @param string 	$property	The name of the property.
	 * @param string 	$value		The value of the property.
	 * @param array 	$tree		The tree array.
	 *
	 * @return array	The properties of the found node.
	 */
	function findPageByProperty ( $property, $value, $tree ) {

		foreach ( $tree as $properties ) {
			if ( $properties[$property] == $value ) {
				return $properties;
			} elseif ( array_key_exists( 'children', $properties ) ) {
				$properties = $this->findPageByProperty ( $property, $value, $properties['children'] ); // Recursive
				if ( $properties ) {
					return $properties;
				}
			}
		}
		return false;
	}

}

?>