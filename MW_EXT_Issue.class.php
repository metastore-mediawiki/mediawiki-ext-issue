<?php

namespace MediaWiki\Extension\MetaStore;

use Parser, PPFrame, OutputPage, Skin;

/**
 * Class MW_EXT_Issue
 */
class MW_EXT_Issue {

	/**
	 * Get issue.
	 *
	 * @param $issue
	 *
	 * @return array
	 */
	private static function getData( $issue ) {
		$get = MW_EXT_Kernel::getJSON( __DIR__ . '/storage/issue.json' );
		$out = $get['issue'][ $issue ] ?? [] ?: [];

		return $out;
	}

	/**
	 * Get issue id.
	 *
	 * @param $issue
	 *
	 * @return mixed|string
	 */
	private static function getID( $issue ) {
		$issue = self::getData( $issue ) ? self::getData( $issue ) : '';
		$out   = $issue['id'] ?? '' ?: '';

		return $out;
	}

	/**
	 * Get issue content.
	 *
	 * @param $issue
	 *
	 * @return mixed|string
	 */
	private static function getContent( $issue ) {
		$issue = self::getData( $issue ) ? self::getData( $issue ) : '';
		$out   = $issue['content'] ?? '' ?: '';

		return $out;
	}

	/**
	 * Get issue category.
	 *
	 * @param $issue
	 *
	 * @return mixed|string
	 */
	private static function getCategory( $issue ) {
		$issue = self::getData( $issue ) ? self::getData( $issue ) : '';
		$out   = $issue['category'] ?? '' ?: '';

		return $out;
	}

	/**
	 * Register tag function.
	 *
	 * @param Parser $parser
	 *
	 * @return bool
	 * @throws \MWException
	 */
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
	 */
	public static function onRenderTag( Parser $parser, PPFrame $frame, array $args ) {
		// Out HTML.
		$outHTML = '<div class="mw-ext-issue navigation-not-searchable mw-ext-box"><div class="mw-ext-issue-body">';
		$outHTML .= '<div class="mw-ext-issue-icon"><div><i class="fas fa-wrench"></i></div></div>';
		$outHTML .= '<div class="mw-ext-issue-content">';
		$outHTML .= '<div class="mw-ext-issue-title">' . MW_EXT_Kernel::getMessageText( 'issue', 'title' ) . '</div>';
		$outHTML .= '<div class="mw-ext-issue-list">';
		$outHTML .= '<ul>';

		foreach ( $args as $arg ) {
			$type = MW_EXT_Kernel::outNormalize( $frame->expand( $arg ) );

			if ( ! self::getData( $type ) ) {
				$outHTML .= '<li>' . MW_EXT_Kernel::getMessageText( 'issue', 'error' ) . '</li>';
				$parser->addTrackingCategory( 'mw-ext-issue-error-category' );
			} else {
				$outHTML .= '<li>' . MW_EXT_Kernel::getMessageText( 'issue', self::getContent( $type ) ) . '</li>';
				$parser->addTrackingCategory( self::getCategory( $type ) );
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
	 */
	public static function onBeforePageDisplay( OutputPage $out, Skin $skin ) {
		$out->addModuleStyles( [ 'ext.mw.issue.styles' ] );

		return true;
	}
}
