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

class Domain
{
	function __construct($client, $name)
	{
		$this->client = $client;
		$this->domain = $name;
	}

	public function name()
	{
		return $this->domain;
	}

	public function spooler_list($options=null)
	{
		$domain	= $this->domain;
		$type   = null;
		$state	= null;
		if ($options != null) {
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

	public function spooler_create($type = "campaign")
	{
		$params = array(
			'name'  => "No name",
			'type'  => $type,
			'start' => time(),
			'ttl'   => 4 * 24 * 60 * 60,
			);
		$id = $this->client->rest_call('domain/'.$this->domain.'/create-spooler', $params, "POST");
		$data = $this->client->rest_call('spooler/'.$id."/load", null, "POST");
		return new Spooler($this->client, $data['id'], $data);
	}

	public function notifications()
	{
		return new Notifications($this->client, $this->domain);
	}

	public function templates()
	{
		return new Templates($this->client, $this->domain);
	}

	public function statistics($destination = null)
	{
		$params = array(
			"domain"      => $this->domain,
		        "destination" => $destination
		);
		return new Statistics($this->client->rest_call('domain/statistics', $params, "POST"));
	}

	public function timeline($timeframe = "weekly")
	{
		if ($timeframe != "daily" &&
		    $timeframe != "weekly" &&
		    $timeframe != "monthly" &&
		    $timeframe != "yearly")
			throw new ClientException("invalid timeframe");
		return $this->client->rest_call('statistics/domain/' . $this->domain . '/timeline/' . $timeframe,
		    null, "POST");
	}

	public function yp()
	{
		return new Yp($this->client, $this->domain);
	}
}

?>
