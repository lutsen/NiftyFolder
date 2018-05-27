<?php

/**
 * The public routes.
 *
 * You can put the routes to your public pages usign your own Twig templates here.
 * Put your templates in the templates/piblic directory.
 */



/**
 * Main route, displays webdite content.
*/
$app->get('[/[{slug}]]', function ($request, $response, $args) {
	$flash = $this->flash->getMessages();

	try {

		$clientid = file_exists( ROOT_PATH . '/client_id.json' );
		$credentials = file_exists( ROOT_PATH . '/credentials.json' );

		$folder_id = false;
		if ( FOLDER_ID AND strlen( FOLDER_ID ) > 0 ) {
			// Folder name
			$folder_id = FOLDER_ID;
		}

		if ( !$clientid || !$credentials || !$folder_id ) {
			return $this->view->render($response, 'admin/public.html', [
				'flash' => $flash,
				'clientid' => $clientid,
				'credentials' => $credentials,
				'folder_id' => $folder_id
			]);
		}

		// Show content
		$drive = new \Drive();
		
		// Get all folders
		$t = new \Tree();
		$tree = $t->getCachedTree( $drive->service(), FOLDER_ID );

		$home_properties = array_values($tree)[0];
		if ( $home_properties['type'] !== 'document' ) {
			$home_properties = false;
		}

		if ( $args['slug'] ) {
			if ( $args['slug'] == $home_properties['slug'] ) {
				// Redirect to APP_URL
				return $response->withStatus( 302 )->withHeader( 'Location', APP_URL );
			} else {
				$slug = $args['slug'];
				$properties = $t->findPageByProperty( 'slug', $slug, $tree );
			}
		} else {
			$properties = $home_properties;
		}

		if ( $properties ) {

			// Get content based on type
			$c = new \Content();
			if ( $properties['type'] == 'document' ) {

				// Get HTML content
				$content = $c->getDocContent( $drive->service(), $properties['id'] );

			} else {

				// Get non-HTML content
				$content = $c->getOtherContent( $drive->service(), $properties['id'] );

				switch ( $properties['type'] ) {
					// Show in browser
					case 'gif':
					case 'jpg':
					case 'png':
						$disposition = 'inline';
						break;
					// Download
					default:
						$disposition = 'attachment';
						break;
				}

				// return non-HTML content
				echo $content;
				return $response->withHeader('Content-Description', 'File Transfer')
					->withHeader('Content-Type', $properties['mimeType'])
					->withHeader('Content-Disposition', $disposition.'; filename="'.$properties['name'].'"')
					->withHeader('Expires', '0')
					->withHeader('Cache-Control', 'must-revalidate')
					->withHeader('Pragma', 'public')
					->withHeader('Content-Length', mb_strlen($content, '8bit') );
			}

		} else {
			// User did not set homepage or page does not exist in tree
			$content = false;
			if ( $home_properties ) {
				// Page does not exist in tree
				throw new Exception( 'This page does not exist.' );
			}
		}

	} catch (Exception $e) {
		$flash['error'][] = $e->getMessage(); // For current request
	}

	// Return HTML content
	return $this->view->render( $response, 'public/index.html', [
		'flash' => $flash,
		'title' => $properties['name'],
		'menu' => $tree,
		'content' => $content
	] );

});

?>