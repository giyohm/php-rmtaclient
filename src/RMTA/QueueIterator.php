<?php
/*
 * Copyright (c) 2013 Gilles Chehade <gilles@rentabiliweb.com>
 *
 * WTFPL :)
 *
 */

namespace RMTA;

class QueueIterator
{
	function __construct($spooler, $options)
	{
		$this->spooler = $spooler;

		$this->domain	= null;
		$this->routing	= null;
		$this->router	= null;
		$this->activity	= null;

		if ($options != null) {
			if (array_key_exists("domain", $options))
				$this->domain = is_array($options['domain']) ? $options['domain'] : array($options['domain']);
			if (array_key_exists("routing", $options) && $options['routing'] != null)
				$this->routing = is_array($options['routing']) ? $options['routing'] : array($options['routing']);
			if (array_key_exists("router", $options) && $options['router'] != null)
				$this->router = is_array($options['router']) ? $options['router'] : array($options['router']);
			if (array_key_exists("activity", $options) && $options['activity'] != null)
				$this->activity = is_array($options['activity']) ? $options['activity'] : array($options['activity']);
		}
	}

	public function size()
	{
		$params = array(
			"domain"  => $this->domain,
			"routing" => $this->routing,
			"router" => $this->router,
			"activity" => $this->activity,
			);
		return $this->spooler->client->rest_call('spooler/'.$this->spooler->id.'/queue/size', $params, "POST");
	}

	public function mails($offset = 0, $count = 5000)
	{
		$params = array(
			"offset"   => $offset,
			"count"    => $count,
			"domain"   => $this->domain,
			"routing"  => $this->routing,
			"router"   => $this->router,
			"activity" => $this->activity,
			);
		return $this->spooler->client->rest_call('spooler/'.$this->spooler->id.'/queue/mails', $params, "POST");
	}
}

?>
