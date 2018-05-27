<?php

/**
 * The admin routes.
 * Users need to authenticate via HTTP Basic Authentication middleware.
 * The username and password are defined in the config.php file.
 */

$app->group('/admin', function () {

	/**
	 * This route shows the admin page.
	 */
	$this->get('[/]', function ($request, $response, $args) {
		$flash = $this->flash->getMessages();

		try {

			$clientid = file_exists( ROOT_PATH . '/client_id.json' );
			$credentials = file_exists( ROOT_PATH . '/credentials.json' );

			$folder_id = false;
			if ( FOLDER_ID AND strlen( FOLDER_ID ) > 0 ) {
				// Folder name
				$folder_id = FOLDER_ID;
			}

		} catch (Exception $e) {
			$flash['error'][] = $e->getMessage(); // For current request
		}

		return $this->view->render($response, 'admin/index.html', [
			'flash' => $flash,
			'clientid' => $clientid,
			'credentials' => $credentials,
			'folder_id' => $folder_id,
			'tree' => file_exists ( ROOT_PATH . '/tree.json'),
			'user' => $request->getHeader('PHP_AUTH_USER')[0], // We need user and password to load tree menu via Javascript
			'password' => $request->getHeader('PHP_AUTH_PW')[0]
		]);

	})->setName('admin');

	/**
	 * This route returns the folder <select> menu which is inserted into the admin page using Javascript fetch.
	 */
	$this->get('/folderselect', function ($request, $response, $args) {
		try {

			// Get all folders
			$drive = new \Drive();
			$t = new \Tree();
			$folders = $t->getChildFolders( $drive->service(), 'root', true );

		} catch (Exception $e) {
			$error = $e->getMessage();
		}
		return $this->view->render($response, 'admin/folderselect.html', [
			'error' => $error,
			'folder_id' => FOLDER_ID,
			'folders' => $folders
		]);
	});

	/**
	 * This route redirects to Google to authenticate the Google Drive user,
	 * and Google redirects the authenticated user here.
	 * When the user is authenticated, the credentials are stored in credentials.json by this route.
	*/
	$this->get('/oauth2callback', function ($request, $response, $args) {

		try {

			$filename = ROOT_PATH . '/credentials.json';
			@$fp = fopen( $filename, 'r' );
			if ( $fp and filesize( $filename ) > 0 ) {
				throw new \Exception('The credentials are already set.');
			}

			$drive = new \Drive();
			$drive->client->setRedirectUri( APP_URL . '/admin/oauth2callback' );

			if ( $request->getParam('code') == null ) {

				// Redirects to Google to authenticate the Google Drive user
				$auth_url = $drive->client->createAuthUrl();
				//header( 'Location: ' . filter_var( $auth_url, FILTER_SANITIZE_URL) );
				return $response->withStatus(302)->withHeader(
					'Location',
					$auth_url
				);

			} else {

				try {

					// Store credentials in credentials.json
					$credentials = $drive->client->authenticate( $request->getParam('code') );
					$fp = fopen( $filename, 'wb' );
					fwrite( $fp, json_encode( $credentials ) );
					fclose( $fp );

				} catch (Exception $e) {
					$this->flash->addMessage( 'error', $e->getMessage() );
				}

				// Redirect back to homepage
				return $response->withStatus(302)->withHeader(
					'Location',
					APP_URL
				);

			}

		// Catch credentioals already exist
		} catch (Exception $e) {
			$this->flash->addMessage( 'error', $e->getMessage() );

			// Redirect back to homepage
			return $response->withStatus(302)->withHeader(
				'Location',
				APP_URL
			);
		}
	});

	/**
	 * This route sets a new FOLDER_ID in config.php, and deletes the old tree.json file.
	 */
	$this->put('/folder', function ($request, $response, $args) {
		try {

			// Set FOLDER_ID in config.php
			$content = file_get_contents( ROOT_PATH.'/config.php' );
			$content = preg_replace(
				'/(define\(\'FOLDER_ID\'\,.+\').{0,}(\'\);)/',
				'${1}'.$request->getParam('folder_id').'${2}',
				$content
			);

			file_put_contents( ROOT_PATH.'/config.php' , $content );

			// Delete tree.json file
			unlink( ROOT_PATH . '/tree.json' );

		} catch (Exception $e) {
			$this->flash->addMessage( 'error', $e->getMessage() );
		}
		return $response->withStatus(302)->withHeader(
			'Location',
			$this->get('router')->pathFor( 'admin' )
		);
	});

	/**
	 * This route deletes the tree.json file.
	 */
	$this->delete('/tree', function ($request, $response, $args) {
		try {
			// Delete tree.json file
			unlink( ROOT_PATH . '/tree.json' );
			$this->flash->addMessage( 'success', 'The tree.json file is deleted.' );
		} catch (Exception $e) {
			$this->flash->addMessage( 'error', $e->getMessage() );
		}
		return $response->withStatus(302)->withHeader(
			'Location',
			$this->get('router')->pathFor( 'admin' )
		);
	});

	/**
	 * This route deletes the credentials.json file AND the tree.json file.
	 */
	$this->delete('/credentials', function ($request, $response, $args) {
		try {
			// Delete credentials.json file AND tree.json
			unlink( ROOT_PATH . '/credentials.json' );
			unlink( ROOT_PATH . '/tree.json' );
			$this->flash->addMessage( 'success', 'The credentials.json file is deleted.' );
		} catch (Exception $e) {
			$this->flash->addMessage( 'error', $e->getMessage() );
		}
		return $response->withStatus(302)->withHeader(
			'Location',
			$this->get('router')->pathFor( 'admin' )
		);
	});

});

?>