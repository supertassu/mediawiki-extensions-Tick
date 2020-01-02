<?php

namespace MediaWiki\Extension\Tick;

use ApiBase;

/**
 * @author Taavi Väänänen <mailbox@taavi.wtf>
 */
class TickApi extends ApiBase
{
	protected function getAllowedParams()
	{
		return [
			'title' => [
				ApiBase::PARAM_TYPE => 'string',
			],
			'pageid' => [
				ApiBase::PARAM_TYPE => 'integer',
			],
			'tickid' => [
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_ISMULTI => true,
				ApiBase::PARAM_REQUIRED => true,
			]
		];
	}

	public function execute()
	{
		$this->checkUserRightsAny('tick');

		$params = $this->extractRequestParams();
		$page = $this->getTitleOrPageId( $params );

		TickDatabase::tick($page->getTitle(), $params['tickid']);
	}

	public function isWriteMode()
	{
		return true;
	}

	public function needsToken()
	{
		return 'csrf';
	}
}
