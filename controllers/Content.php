<?php

/*
 * Read and manipulate content.
 */

class Content {

	/**
	 * Get HTML string from a DOMElement XML object node.
	 * From https://stackoverflow.com/questions/2087103/how-to-get-innerhtml-of-domnode#2087136
	 *
	 * @param object	$element	The DOMElement node
	 *
	 * @return string	HTML string
	 */
	private function innerHTML(\DOMElement $element) {
		$doc = $element->ownerDocument;

		$html = '';

		foreach ($element->childNodes as $node) {
			$html .= $doc->saveHTML($node);
		}

		return $html;
	}

	/**
	 * Replace Youtube link formatted like [https://youtu.be/videoid] with Youtube embed code.
	 *
	 * @param string	$html	HTML code with links to replace
	 *
	 * @return string	String with Youtube links replaced with embed code.
	 */
	private function embedTags( $html ) {
		return preg_replace(
			'/\[.{0,}https\:\/\/youtu\.be\/(.{11}).{0,}\]/',
			'<div class="embed-youtube"><iframe src="https://www.youtube.com/embed/$1?rel=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe></div>',
			$html
		);
	}

	/**
	 * Get styling and HTML from Google Doc.
	 * Returns an array with the following keys:
	 * css: <style> in <head> tag of the Google Doc.
	 * contentstyle: The inline styling of <body> tag, with max-width stripped off.
	 * html: The content of <body> tag.
	 *
	 * @param string	$service
	 * @param string	$file_id
	 *
	 * @return array	Array with ['css'], ['contentstyle'] and ['html']
	 */
	public function getDocContent( $service, $file_id ) {
		$return = [];

		// Load content
		$response = $service->files->export( $file_id, 'text/html', array('alt' => 'media') );
		$doc = new \DOMDocument();
		$doc->loadHTML( $response->getBody()->getContents() );

		// Get <style> in <head> tag
		$head = $doc->getElementsByTagName('head');
		$style = $head[0]->getElementsByTagName('style');
		if ( $style[0] ) {
			$return['css'] = $this->innerHTML( $style[0] );
		}

		// Get content of <body> tag
		$body = $doc->getElementsByTagName('body');
		$bodystyle = $body[0]->getAttribute('style');
		$return['contentstyle'] = substr( $bodystyle, 0, strpos($bodystyle, 'max-width:') ); // Strip maxwidth
		$html = $this->innerHTML( $body[0] );
		// Add media embeds like Youtube
		$return['html'] = $this->embedTags( $html );

		return $return;
	}

	/**
	 * Get content of a non-google-doc, like an image or PDF or Word or other type of file.
	 *
	 * @param string	$service
	 * @param string	$file_id
	 *
	 * @return string	Content from the file.
	 */
	public function getOtherContent( $service, $file_id ) {

		// Load content
		$response = $service->files->get( $file_id, array('alt' => 'media') );

		return $response->getBody()->getContents();

	}

}

?>