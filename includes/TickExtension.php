<?php

namespace MediaWiki\Extension\Tick;

use DatabaseUpdater;
use Parser;
use Sanitizer;
use OOUI\ButtonWidget;

/**
 * @author Taavi Väänänen <mailbox@taavi.wtf>
 */
class TickExtension
{
	public static function onLoadExtensionSchemaUpdates( DatabaseUpdater $updater )
	{
		$updater->addExtensionTable( 'tick', dirname( __DIR__ ) . '/sql/add_ticks.sql' );
	}

	public static function onParserFirstCallInit( Parser $parser )
	{
		$parser->setFunctionHook( 'tick', [ self::class, 'renderTickTag' ] );
		$parser->setFunctionHook( 'submit_selected_ticks', [ self::class, 'renderSelectedTicksTag' ] );
	}

	public static function renderTickTag( Parser $parser, $id, $description = '' )
	{
		$parser->getOutput()->addModules([ 'ext.tick.tick' ]);

		$id = htmlspecialchars(Sanitizer::escapeIdForAttribute($id));

		$output = '<input type="checkbox" name="tick" value="' . $id . '"/>'
			. ' <button onclick="mw.tick.tick(\'' . $id . '\')">' . wfMessage( 'tick-button' )->parse() . '</button>'
			. ' ' . htmlspecialchars( $description ) . ' <span class="tick-status" data-tick-id="' . $id . '"></span>';
		return [ $output, 'noparse' => true, 'isHTML' => true ];
	}

	public static function renderSelectedTicksTag( Parser $parser )
	{
		$parser->getOutput()->addModules([ 'ext.tick.tick', 'ext.tick.tick-selected-ui', 'oojs-ui-core', 'oojs-ui-widgets' ]);
        $parser->enableOOUI();

        $button = new ButtonWidget([
        	'label' => wfMessage( 'tick-selected-button' )->text(),
			'href' => '#',
			'flags' => 'progressive',
			'infusable' => true,
		]);

        $button->addClasses([ 'mwe-tick-btn-all' ]);

		return [ $button->toString(), 'noparse' => true, 'isHTML' => true ];
	}
}
