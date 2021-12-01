<?php

namespace MediaWiki\Extension\TimeMachine;

use SpecialPage;

class SpecialTimeMachine extends SpecialPage {

	public function __construct() {
		parent::__construct( 'TimeMachine' );
	}

	/**
	 * @param Parser $parser
	 */
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
		$output->addHTML( '
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
		' );
		$this->setHeaders();
	}
}
