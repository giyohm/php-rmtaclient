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

class SpoolerFilter
{
	/**
	 * @ignore
	 */
	function __construct($client, $options = null)
	{
		$this->client  = $client;
		$this->iter_offset = -1;

		$this->domains = null;
		$this->types   = null;
		$this->states  = null;
		$this->start_after = null;
		$this->start_before = null;
		$this->end     = null;
		$this->name    = null;

		if ($options != null) {
			if (array_key_exists("domain", $options) && $options['domain'] != null)
				$this->domains = is_array($options['domain']) ? $options['domain'] : array($options['domain']);
			if (array_key_exists("type", $options) && $options['type'] != null)
				$this->types = is_array($options['type']) ? $options['type'] : array($options['type']);
			if (array_key_exists("state", $options) && $options['state'] != null)
				$this->states = is_array($options['state']) ? $options['state'] : array($options['state']);
			if (array_key_exists("start-after", $options) && $options["start-after"] != null)
				$this->start_after = $options["start-after"];
			if (array_key_exists("start-before", $options) && $options["start-before"] != null)
				$this->start_before = $options["start-before"];
			if (array_key_exists("end", $options) && $options["end"] != null)
				$this->end = $options["end"];
			if (array_key_exists("name", $options) && $options["name"] != null)
				$this->name = $options["name"];
		}
	}

	/**
	 * Get the number of spoolers matching this filter
	 *
	 * @return integer
	 */
	public function count()
	{
		$params = array();
		if ($this->domains)
			$params['domains'] = $this->domains;
		if ($this->states)
			$params['states'] = $this->states;
		if ($this->types)
			$params['types'] = $this->types;
		if ($this->name)
			$params['name'] = $this->name;
		if ($this->start_after)
			$params['start-after'] = $this->start_after;
		if ($this->start_before)
			$params['start-before'] = $this->start_before;
		if ($this->end)
			$params['end'] = $this->end;

		return $this->client->rest_call('spooler-count', $params, "POST");
	}

	/**
	 * Retrieve a list of $count Spoolers instances starting at offset $offset
	 *
	 * @param integer $offset (optional) offset of the first Spooler to retrieve
	 * @param integer $count (optional) number of Spooler instances to retrieve
	 *
	 * @return Spooler[]
	 */
	public function get($offset = -1, $count = 10, $reverse = null)
	{
		$params = array();
		$params['offset'] = $offset;
		$params['limit']  = $count;
		if ($reverse != null)
			$params['reverse'] = "yes";
		if ($this->domains)
			$params['domains'] = $this->domains;
		if ($this->states)
			$params['states'] = $this->states;
		if ($this->types)
			$params['types'] = $this->types;
		if ($this->name)
			$params['name'] = $this->name;
		if ($this->start_after)
			$params['start-after'] = $this->start_after;
		if ($this->start_before)
			$params['start-before'] = $this->start_before;
		if ($this->end)
			$params['end'] = $this->end;

		$res = array();
		foreach ($this->client->rest_call('spooler-list', $params, "POST") as $value)
			array_push($res, new Spooler($this->client, $value['id'], $value));
		return $res;
	}

	/**
	 * Reset the iterator
	 *
	 * @return null
	 */
	public function iter_reset()
	{
		$this->iter_offset = -1;
	}

	/**
	 * Retrieve the next list of at most $count Spoolers instances from this iterator
	 *
	 * @param integer $count (optional) number of Spooler instances to retrieve
	 *
	 * @return Spooler[]
	 */
	public function iter_next($count = 10)
	{
		$a = $this->get($this->iter_offset, $count);
		foreach($a as $spooler)
			if ($this->iter_offset == -1)
				$this->iter_offset = $spooler->identifier();
			else
				$this->iter_offset = max($this->iter_offset, $spooler->identifier());
		return $a;
	}

	public function iter_next_rev($count = 10)
	{
		$a = $this->get($this->iter_offset, $count, true);
		foreach($a as $spooler) {
			if ($this->iter_offset == -1)
				$this->iter_offset = $spooler->identifier();
			else
				$this->iter_offset = min($this->iter_offset, $spooler->identifier());
		}
		return $a;
	}
}

?>
