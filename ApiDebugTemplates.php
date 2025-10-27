<?php

/*
 * This is a custom API handler to expand templates with a given
 * list of parameters.
 *
 * @author Clark Verbrugge
 * @Licence CC BY-SA 3.0
 */

use MediaWiki\Api\ApiBase;
use MediaWiki\Json\FormatJson;
use MediaWiki\MediaWikiServices;
use MediaWiki\Parser\ParserOptions;
use MediaWiki\Title\Title;
use Wikimedia\ParamValidator\ParamValidator;

class ApiDebugTemplates extends ApiBase {
	public function execute() {
		$params = $this->extractRequestParams();

		$title_obj = Title::newFromText( $params[ 'title' ] );

		if ( !$title_obj || $title_obj->isExternal() ) {
			$this->dieWithError( [ 'apierror-invalidtitle', wfEscapeWikiText( $params['title'] ) ] );
		}

		// The frame field is a JSON-encoded object
		$frame = FormatJson::parse( $params[ 'frame' ], FormatJson::FORCE_ASSOC );

		$parser = MediaWikiServices::getInstance()->getParser();
		$result = $this->getResult();

		if ( $frame->isGood() ) {
			$options = ParserOptions::newFromContext( $this->getContext() );
			$parser->setOptions( $options );
			$parsed = $parser->preprocess( $params[ 'text' ],
				$title_obj,
				$options,
				null,
				$parser->getPreprocessor()->newCustomFrame( $frame->getValue() )
			);
			$this->getResult()->addValue( null, $this->getModuleName(),
				 [ 'result' => $parsed ] );
		} else {
			$this->getErrorFormatter()->addMessagesFromStatus( $this->getModuleName(), $frame );
		}
		return true;
	}

	/**
	 * Force the existence of our parameters.
	 *
	 * @return array Array of parameter to arrays
	 */
	public function getAllowedParams() {
		return array_merge( parent::getAllowedParams(), [
				'text' => [
					ParamValidator::PARAM_TYPE => 'string',
					ParamValidator::PARAM_REQUIRED => true
				],
				'frame' => [
					ParamValidator::PARAM_TYPE => 'string',
					ParamValidator::PARAM_DEFAULT => '{}'
					// ParamValidator::PARAM_REQUIRED => true
				],
				'title' => [
					ParamValidator::PARAM_DEFAULT => 'API'
				],
			] );
	}

	/**
	 * Provide an example of usage
	 *
	 * @return array Array showing an example use and help text
	 */
	public function getExamplesMessages() {
		return [
			'action=expandframe&text={{{a}}}&frame={"a":"b"}'
			=> 'apihelp-expandframe-example-1'
		];
	}
}
