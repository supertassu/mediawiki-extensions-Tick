<?php

namespace MediaWiki\Extension\Tick;

use ApiQueryBase;

class TickQueryApi extends ApiQueryBase
{
	public function execute()
	{
		$pages = $this->getPageSet()->getTitles();

		$ticks = array_merge(...array_map([TickDatabase::class, 'getTicks'], $pages));

		foreach ($ticks as $tick)
		{
			$this->getResult()->addValue([ 'query', $this->getModuleName() ], null, $tick);
		}
	}
}
