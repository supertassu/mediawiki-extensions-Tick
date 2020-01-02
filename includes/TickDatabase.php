<?php

namespace MediaWiki\Extension\Tick;

use RequestContext;
use Title;

class TickDatabase
{
	public static function getTicks( Title $title )
	{
		if (!$title->exists()) {
			return [];
		}

		$ticks = [];

		$dbr = wfGetDB( DB_REPLICA );
		$res = $dbr->select(
			'tick',
			['tick_id', 'tick_page', 'tick_name', 'tick_last_ticked'],
			['tick_page' => $title->getArticleID()],
			__METHOD__,
			['order by' => 'tick_user_id']
		);

		$context = RequestContext::getMain();

		foreach ( $res as $tick ) {
			$formattedTimestamp = null;

			if ($context) {
				$formattedTimestamp = $context->getLanguage()->userTimeAndDate( $tick->tick_last_ticked, $context->getUser() );
			}

			$ticks[] = [
				'internal_id' => $tick->tick_id,
				'given_id' => $tick->tick_name,
				'last_ticked' => $tick->tick_last_ticked,
				'last_ticked_in_human_language' => $formattedTimestamp,
				'page_id' => $tick->tick_page,
			];
		}

		return $ticks;
	}

	public static function tick( Title $title, array $tickNames )
	{
		if (!$title->exists()) {
			return false;
		}

		$dbw = wfGetDB( DB_MASTER );
		$currentTime = $dbw->timestamp();

		$rows = [];

		foreach ( $tickNames as $tickName ) {
			$rows[] = [
				'tick_page' => $title->getArticleID(),
				'tick_name' => $tickName,
			];
		}

		return $dbw->upsert(
			'tick',
			$rows,
			['tick_name_page_unique'],
			['tick_last_ticked' => $currentTime],
			__METHOD__
		);
	}
}
