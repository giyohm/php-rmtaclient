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

	public function lookup($type, $term)
	{
		$url = 'spooler/'.$this->spooler->id.'/queue/lookup/'.$type;
		$params = array("term" => $term);
		return $this->spooler->client->rest_call($url, $params, "POST");
	}
}

?>
