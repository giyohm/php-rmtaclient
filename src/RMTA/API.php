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

class API
{
	function __construct($client)
	{
		$this->client = $client;
	}

	function domain($domain)
	{
		return new Domain($this->client, $domain);
	}

	function spooler($spooler_id)
	{
		$s = $this->client->rest_call('spooler/'.$spooler_id.'/load', null, "POST");
		return new Spooler($this->client, $s['id'], $s);
	}

	function domain_list()
	{
		$ret = array();
		foreach ($this->client->rest_call('domain-list', null, "POST") as $value)
		    array_push($ret, $this->domain($value));
		return $ret;
	}

	function spooler_list($options = null)
	{
		$domain	= null;
		$type   = null;
		$state	= null;
		if ($options != null) {
			if (array_key_exists("domain", $options) && $options['domain'] != null)
				$domain = is_array($options['domain']) ? $options['domain'] : array($options['domain']);
			if (array_key_exists("type", $options) && $options['type'] != null)
				$type = is_array($options['type']) ? $options['type'] : array($options['type']);
			if (array_key_exists("state", $options) && $options['state'] != null)
				$state = is_array($options['state']) ? $options['state'] : array($options['state']);
		}

		$params = array(
			"domain"=> $domain,
			"type"	=> $type,
			"state"	=> $state,
		);

		$res = array();
		foreach ($this->client->rest_call('spooler-list', $params, "POST") as $value)
			array_push($res, new Spooler($this->client, $value['id'], $value));
		return $res;		
	}
}

?>
