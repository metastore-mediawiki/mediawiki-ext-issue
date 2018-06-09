<?php

/**
 * Class MW_EXT_Issue
 * ------------------------------------------------------------------------------------------------------------------ */

class MW_EXT_Issue {

	/**
	 * Clear DATA (escape html).
	 *
	 * @param $string
	 *
	 * @return string
	 * -------------------------------------------------------------------------------------------------------------- */

	private static function clearData( $string ) {
		$outString = htmlspecialchars( trim( $string ), ENT_QUOTES );

		return $outString;
	}

	/**
	 * Convert DATA (replace space & lower case).
	 *
	 * @param $string
	 *
	 * @return string
	 * -------------------------------------------------------------------------------------------------------------- */

	private static function convertData( $string ) {
		$outString = mb_strtolower( str_replace( ' ', '-', $string ), 'UTF-8' );

		return $outString;
	}

	/**
	 * Get MediaWiki issue.
	 *
	 * @param $string
	 *
	 * @return string
	 * -------------------------------------------------------------------------------------------------------------- */

	private static function getMsgText( $string ) {
		$outString = wfMessage( 'mw-ext-issue-' . $string )->inContentLanguage()->text();

		return $outString;
	}

	/**
	 * Get JSON data.
	 *
	 * @return mixed
	 * -------------------------------------------------------------------------------------------------------------- */

	private static function getData() {
		$getData = file_get_contents( __DIR__ . '/storage/issue.json' );
		$outData = json_decode( $getData, true );

		return $outData;
	}

	/**
	 * Get issue.
	 *
	 * @param $issue
	 *
	 * @return mixed
	 * -------------------------------------------------------------------------------------------------------------- */

	private static function getIssue( $issue ) {
		$getData = self::getData();

		if ( ! isset( $getData['issue'][ $issue ] ) ) {
			return false;
		}

		$getIssue = $getData['issue'][ $issue ];
		$outIssue = $getIssue;

		return $outIssue;
	}

	/**
	 * Get issue id.
	 *
	 * @param $issue
	 *
	 * @return mixed
	 * -------------------------------------------------------------------------------------------------------------- */

	private static function getIssueID( $issue ) {
		$issue = self::getIssue( $issue ) ? self::getIssue( $issue ) : '';

		if ( ! isset( $issue['id'] ) ) {
			return false;
		}

		$getID = $issue['id'];
		$outID = $getID;

		return $outID;
	}

	/**
	 * Get issue content.
	 *
	 * @param $issue
	 *
	 * @return mixed
	 * -------------------------------------------------------------------------------------------------------------- */

	private static function getIssueContent( $issue ) {
		$issue = self::getIssue( $issue ) ? self::getIssue( $issue ) : '';

		if ( ! isset( $issue['content'] ) ) {
			return false;
		}

		$getContent = $issue['content'];
		$outContent = $getContent;

		return $outContent;
	}

	/**
	 * Get issue category.
	 *
	 * @param $issue
	 *
	 * @return mixed
	 * -------------------------------------------------------------------------------------------------------------- */

	private static function getIssueCategory( $issue ) {
		$issue = self::getIssue( $issue ) ? self::getIssue( $issue ) : '';

		if ( ! isset( $issue['category'] ) ) {
			return false;
		}

		$getCategory = $issue['category'];
		$outCategory = $getCategory;

		return $outCategory;
	}

	/**
	 * Register tag function.
	 *
	 * @param Parser $parser
	 *
	 * @return bool
	 * @throws MWException
	 * -------------------------------------------------------------------------------------------------------------- */

	public static function onParserFirstCallInit( Parser $parser ) {
		$parser->setFunctionHook( 'issue', __CLASS__ . '::onRenderTag', Parser::SFH_OBJECT_ARGS );

		return true;
	}

	/**
	 * Render tag function.
	 *
	 * @param Parser $parser
	 * @param PPFrame $frame
	 * @param array $args
	 *
	 * @return bool|string
	 * -------------------------------------------------------------------------------------------------------------- */

	public static function onRenderTag( Parser $parser, PPFrame $frame, array $args ) {
		// Out HTML.
		$outHTML = '<div class="mw-ext-issue"><div class="mw-ext-issue-body">';
		$outHTML .= '<div class="mw-ext-issue-icon"><div><i class="fas fa-sync"></i></div></div>';
		$outHTML .= '<div class="mw-ext-issue-content">';
		$outHTML .= '<h4>' . self::getMsgText( 'title' ) . '</h4>';
		$outHTML .= '<ol>';

		for ( $arg = array_shift( $args ); isset( $arg ); $arg = array_shift( $args ) ) {
			$type = self::convertData( $frame->expand( $arg ) );

			if ( ! self::getIssue( $type ) ) {
				$outHTML .= '<li>' . self::getMsgText( 'error' ) . '</li>';
				$parser->addTrackingCategory( 'mw-ext-issue-error-category' );
			} else {
				$outHTML .= '<li>' . self::getMsgText( self::getIssueContent( $type ) ) . '</li>';
				$parser->addTrackingCategory( self::getIssueCategory( $type ) );
			}
		}

		$outHTML .= '</ol>';
		$outHTML .= '</div>';
		$outHTML .= '</div></div>';

		// Out parser.
		$outParser = $parser->insertStripItem( $outHTML, $parser->mStripState );

		return $outParser;
	}

	/**
	 * Load resource function.
	 *
	 * @param OutputPage $out
	 * @param Skin $skin
	 *
	 * @return bool
	 * -------------------------------------------------------------------------------------------------------------- */

	public static function onBeforePageDisplay( OutputPage $out, Skin $skin ) {
		$out->addModuleStyles( array( 'ext.mw.issue.styles' ) );

		return true;
	}
}
