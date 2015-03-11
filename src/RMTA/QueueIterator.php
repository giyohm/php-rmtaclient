<?php
/*
 * Copyright (c) 2014 Gilles Chehade <gilles@rentabiliweb.com>
 *
 * Permission to use, copy, modify, and distribute this software for any
 * purpose with or without fee is hereby granted, provided that the above
 * copyright notice and this permission notice appear in all copies.
 *
 * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
 * WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
 * ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
 * WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
 * ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
 * OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
 */

namespace RMTA;

class QueueIterator
{
	/**
	 * @ignore
	 */
	function __construct($spooler, $options)
	{
		$this->spooler = $spooler;

		$this->domains	  = null;
		$this->routing	  = null;
		$this->routers	  = null;
		$this->activities = null;

		if ($options != null) {
			if (array_key_exists("domains", $options))
				$this->domains = is_array($options['domains']) ? $options['domains'] : array($options['domains']);
			if (array_key_exists("routing", $options) && $options['routing'] != null)
				$this->routing = is_array($options['routing']) ? $options['routing'] : array($options['routing']);
			if (array_key_exists("routers", $options) && $options['routers'] != null)
				$this->routers = is_array($options['routers']) ? $options['routers'] : array($options['routers']);
			if (array_key_exists("activities", $options) && $options['activities'] != null)
				$this->activities = is_array($options['activities']) ? $options['activities'] : array($options['activities']);
		}
	}

	/**
	 * Retrieve the number of messages in this QueueIterator instance
	 *
	 * @return integer
	 */
	public function size()
	{
		$params = array();
		if ($this->domains)
			$params['domains'] = $this->domains;
		if ($this->routing)
			$params['routing'] = $this->routing;
		if ($this->routers)
			$params['routers'] = $this->routers;
		if ($this->activities)
			$params['activities'] = $this->activities;
		return $this->spooler->client->rest_call('spooler/'.$this->spooler->id.'/queue/size', $params, "POST");
	}

	/**
	 * Retrieve a list of $count Mail instances starting at offset $offset
	 *
	 * @param integer $offset (optional) offset of first Mail to retrieve
	 * @param integer $count (optional) number of Mail instances to retrieve
	 *
	 * @return Mail[]
	 */
	public function mails($offset = 0, $count = 5000)
	{
		$params = array();
		$params['offset'] = $offset;
		$params['count']  = $count;
		if ($this->domains)
			$params['domains'] = $this->domains;
		if ($this->routing)
			$params['routing'] = $this->routing;
		if ($this->routers)
			$params['routers'] = $this->routers;
		if ($this->activities)
			$params['activities'] = $this->activities;

		$a = array();
		foreach ($this->spooler->client->rest_call('spooler/'.$this->spooler->id.'/queue/mails',
			$params, "POST") as $m) {
			array_push($a, new Mail($this->spooler, $m['rcpt'], $m));
		}
		return $a;
	}

	/**
	 * Lookup email addresses or domain names by prefix
	 *
	 * @param string $type type of lookup to perform ("address", "domain")
	 * @param string $term prefix for the lookup
	 *
	 * @return string[]
	 */
	public function lookup($type, $term)
	{
		$url = 'spooler/'.$this->spooler->id.'/queue/lookup/'.$type;
		$params = array("term" => $term);
		return $this->spooler->client->rest_call($url, $params, "POST");
	}
}

?>
