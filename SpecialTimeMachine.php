<?php

class SpecialTimeMachine extends SpecialPage {

	public function __construct() {
		parent::__construct( 'TimeMachine' );
	}

	public function execute( $parser ) {
		$request = $this->getRequest();
		$date = $request->getCookie( 'timemachine-date', null, date( 'Y-m-d' ) );
		if ( $request->wasPosted() ) {
			$date = $request->getVal( 'date' );
			$response = $request->response();
			$response->setCookie( 'timemachine-date', $date );
		}
		$output = $this->getOutput();
		$output->enableOOUI();
		$output->addHTML('
			<p>' . wfMessage( 'timemachine-p1' )->escaped() . '</p>
			<form method="post">
			<input type="date" name="date" value="' . $date . '" />
			<button type="submit" class="mw-ui-button mw-ui-progressive">' . wfMessage( 'timemachine-button1' )->escaped() . '</button>
			</form>
			<p>' . wfMessage( 'timemachine-p2' )->escaped() . '</p>
			<p>' . wfMessage( 'timemachine-p3' )->escaped() . '</p>
			<form method="post">
			<input type="hidden" name="date" value="" />
			<button type="submit" class="mw-ui-button">' . wfMessage( 'timemachine-button2' )->escaped() . '</button>
			</form>
		');
		$this->setHeaders();
	}

	/**
	 * This method redirects to the first revision before the time set by the user in Special:TimeMachine
	 * It would be better if instead of redirecting it changed the request on the fly, but I haven't found
	 * a way yet.
	 */
	public static function onBeforeInitialize( &$title, &$article, &$output, &$user, $request, $mediaWiki ) {
		if ( $request->getVal( 'action', 'view' ) != 'view' ) {
			return true;
		}
		$date = $request->getCookie( 'timemachine-date' );
		$rev_id = $request->getVal( 'oldid' );
		if ( $date and ! $rev_id ) {
			$dbr = wfGetDB( DB_REPLICA );

			$rev_timestamp = wfTimestamp( TS_UNIX, $date . ' 00:00:00' );
			$rev_timestamp = $dbr->timestamp( $rev_timestamp );

			$rev_page = $title->getArticleID();

			$result = $dbr->select( 'revision', array('rev_id'), "rev_page = $rev_page AND rev_timestamp < $rev_timestamp", __METHOD__, array( 'ORDER BY' => 'rev_timestamp DESC', 'LIMIT' => 1 ) );
			if ( $row = $result->fetchRow() ) {
				//Redirect to the old revision of the page
				$rev_id = $row['rev_id'];
				$rev = Revision::newFromId( $rev_id );
				$url = $rev->getTitle()->getLocalURL( array( 'oldid' => $rev_id ) );
				$output->redirect( $url );
			} else {
				//The page doesn't exist yet
			}
		}
	}
}