<?php

namespace MediaWiki\Extension\TimeMachine;

use MediaWiki\MediaWikiServices;

class Hooks {
	/**
	 * This method redirects to the first revision before the time set by the user in Special:TimeMachine
	 * It would be better if instead of redirecting it changed the request on the fly, but I haven't found
	 * a way yet.
	 *
	 * @param Title &$title
	 * @param \Article &$article
	 * @param OutputPage &$output
	 * @param User &$user
	 * @param \WebRequest $request
	 * @param \MediaWiki $mediaWiki
	 */
	public static function onBeforeInitialize( &$title, &$article, &$output, &$user, $request, $mediaWiki ) {
		if ( $request->getVal( 'action', 'view' ) != 'view' ) {
			return;
		}

		$date = $request->getCookie( 'timemachine-date' );
		if ( !$date || $request->getBool( 'oldid' ) ) {
			return;
		}

		$dbr = MediaWikiServices::getInstance()->getDBLoadBalancer()->getConnection( DB_REPLICA );

		$rev_timestamp = wfTimestamp( TS_UNIX, $date . ' 00:00:00' );
		$rev_page = $title->getArticleID();

		$rev_id = $dbr->selectField(
			'revision',
			'rev_id',
			[ 'rev_page' => $rev_page, 'rev_timestamp < ' . $dbr->timestamp( $rev_timestamp ) ],
			__METHOD__,
			[ 'ORDER BY' => 'rev_timestamp DESC', 'LIMIT' => 1 ]
		);

		// The page doesn't exist yet
		if ( !$rev_id ) {
			return;
		}

		// Redirect to the old revision of the page
		$url = $title->getLocalURL( [ 'oldid' => $rev_id ] );
		$output->redirect( $url );
	}
}
