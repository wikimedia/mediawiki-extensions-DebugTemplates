<?php

/*
 * This is a custom API handler to expand templates with a given
 * list of parameters.
 *
 * @author Clark Verbrugge
 * @Licence CC BY-SA 3.0
 */
class ApiDebugTemplates extends ApiBase {
	public function execute() {
		global $wgParser;
		
        $params = $this->extractRequestParams();

		$title_obj = Title::newFromText( $params[ 'title' ] );
		
		if ( !$title_obj || $title_obj->isExternal() ) {
			$this->dieUsageMsg( array( 'invalidtitle', $params[ 'title' ] ) );
		}
		
		// The frame field is a JSON-encoded object
		$frame = FormatJson::parse( $params[ 'frame' ], FormatJson::FORCE_ASSOC );
		
		$result = $this->getResult();
		
		if ( $frame->isGood() ) {
			$options = ParserOptions::newFromContext( $this->getContext() );
			$parsed = $wgParser->preprocess( $params[ 'text' ],
				$title_obj,
				$options,
				null,
				$frame->getValue() );
			$this->getResult()->addValue( null, $this->getModuleName(),
				array ( 'result' => $parsed ) );
		} else {
			$this->getErrorFormatter()->addMessagesFromStatus( $this->getModuleName(), $frame );
		}
		return true;
	}
	
	/**
	 * Force the existence of our parameters.
	 *
	 * @return object Array of parameter to arrays
	 */
	public function getAllowedParams() {
		return array_merge( parent::getAllowedParams(), array(
				'text' => array (
					ApiBase::PARAM_TYPE => 'string',
					ApiBase::PARAM_REQUIRED => true
				),
				'frame' => array (
					ApiBase::PARAM_TYPE => 'string',
					ApiBase::PARAM_DFLT => '{}'
					//					ApiBase::PARAM_REQUIRED => true
				),
				'title' => array (
					ApiBase::PARAM_DFLT => 'API'
				),
			) );
	}
	
	/**
	 * Provide an example of usage
	 *
	 * @return object Array showing an example use and help text
	 */
	public function getExamplesMessages() {
		return array(
			'action=expandframe&text={{{a}}}&frame={"a":"b"}'
			=> 'apihelp-expandframe-example-1'
		);
	}
}