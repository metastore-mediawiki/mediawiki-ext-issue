<?php

namespace MediaWiki\Extension\MW_EXT_Issue;

use Parser, PPFrame, OutputPage, Skin;
use MediaWiki\Extension\MW_EXT_Core\MW_EXT_Core;

/**
 * Class MW_EXT_Issue
 * ------------------------------------------------------------------------------------------------------------------ */
class MW_EXT_Issue {

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
	 * @throws \MWException
	 * -------------------------------------------------------------------------------------------------------------- */

	public static function onParserFirstCallInit( Parser $parser ) {
		$parser->setFunctionHook( 'issue', [ __CLASS__, 'onRenderTag' ], Parser::SFH_OBJECT_ARGS );

		return true;
	}

	/**
	 * Render tag function.
	 *
	 * @param Parser $parser
	 * @param PPFrame $frame
	 * @param array $args
	 *
	 * @return string
	 * -------------------------------------------------------------------------------------------------------------- */

	public static function onRenderTag( Parser $parser, PPFrame $frame, $args = [] ) {
		// Out HTML.
		$outHTML = '<div class="mw-ext-issue navigation-not-searchable"><div class="mw-ext-issue-body">';
		$outHTML .= '<div class="mw-ext-issue-icon"><div><i class="fas fa-wrench"></i></div></div>';
		$outHTML .= '<div class="mw-ext-issue-content">';
		$outHTML .= '<div class="mw-ext-issue-title">' . MW_EXT_Core::getMessageText( 'issue', 'title' ) . '</div>';
		$outHTML .= '<div class="mw-ext-issue-list">';
		$outHTML .= '<ul>';

		foreach ( $args as $arg ) {
			$type = MW_EXT_Core::outConvert( $frame->expand( $arg ) );

			if ( ! self::getIssue( $type ) ) {
				$outHTML .= '<li>' . MW_EXT_Core::getMessageText( 'issue', 'error' ) . '</li>';
				$parser->addTrackingCategory( 'mw-ext-issue-error-category' );
			} else {
				$outHTML .= '<li>' . MW_EXT_Core::getMessageText( 'issue', self::getIssueContent( $type ) ) . '</li>';
				$parser->addTrackingCategory( self::getIssueCategory( $type ) );
			}
		}

		$outHTML .= '</ul></div></div></div></div>';

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
		$out->addModuleStyles( [ 'ext.mw.issue.styles' ] );

		return true;
	}
}
