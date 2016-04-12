<?php

namespace b8\Http\Response;

use b8\Http\Response;

class RedirectResponse extends Response
{
	public function __construct(Response $createFrom = null)
	{
		parent::__construct($createFrom);

		$this->setContent(null);
		$this->setResponseCode(302);
	}

	public function hasLayout()
	{
		return false;
	}

	public function flush()
	{
		parent::flush();
		die;
	}
}