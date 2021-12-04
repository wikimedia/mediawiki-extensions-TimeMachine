<?php

namespace MediaWiki\Extension\TimeMachine;

use HTMLForm;
use MWException;
use SpecialPage;

class SpecialTimeMachine extends SpecialPage {

	public function __construct() {
		parent::__construct( 'TimeMachine' );
	}

	/**
	 * @param Parser $parser
	 * @throws MWException
	 */
	public function execute( $parser ) {
		$request = $this->getRequest();
		if ( $request->wasPosted() ) {
			$date = $request->getVal( 'date' );
			$response = $request->response();
			$response->setCookie( 'timemachine-date', $date );
		}

		$output = $this->getOutput();
		$output->enableOOUI();

		$this->buildSetTimeForm();
		$output->addHTML( '<br> ' );
		$this->buildRemoveTimeForm();
		$this->setHeaders();
	}

	/**
	 * Render HTMLForm in OOUI mode
	 *
	 * @return null
	 * @throws MWException
	 */
	protected function buildSetTimeForm() {
		$request = $this->getRequest();
		$date = $request->getCookie( 'timemachine-date', null, date( 'Y-m-d' ) );

		$formDescriptor = [
			'info' => [
				'type' => 'info',
				'default' => $this->msg( 'timemachine-p1' )->escaped(),
			],
			'date' => [
				'type' => 'date',
				'name' => 'date',
				'default' => $date,
			]
		];

		$htmlForm = HTMLForm::factory( 'ooui', $formDescriptor, $this->getContext() );
		$htmlForm
			->setMethod( 'post' )
			->setSubmitTextMsg( 'timemachine-button1' )
			->prepareForm()
			->displayForm( false );
	}

	/**
	 * Render HTMLForm in OOUI mode
	 *
	 * @return null
	 * @throws MWException
	 */
	protected function buildRemoveTimeForm() {
		$formDescriptor = [
			'info' => [
				'type' => 'info',
				'default' => $this->msg( 'timemachine-p2' )->escaped(),
			],
			'info2' => [
				'type' => 'info',
				'default' => $this->msg( 'timemachine-p3' )->escaped(),
			],
		];

		$htmlForm = HTMLForm::factory( 'ooui', $formDescriptor, $this->getContext() );
		$htmlForm
			->addHiddenField( 'date', '' )
			->setMethod( 'post' )
			->setSubmitDestructive()
			->setSubmitTextMsg( 'timemachine-button2' )
			->prepareForm()
			->displayForm( false );
	}
}
