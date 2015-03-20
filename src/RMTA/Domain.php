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
	/**
	 * @ignore
	 */
	function __construct($client, $name)
	{
		$this->client = $client;
		$this->domain = $name;
	}

	/**
	 * get the name associated to this Domain connector
	 *
	 * @return string
	 */
	public function name()
	{
		return $this->domain;
	}

	/**
	 * Obtain a SpoolerFilter instance allowing the retreive spoolers for this domain matching certain conditions
	 *
	 * @param array $options a list of options that a spooler should match
	 *
	 * @return SpoolerFilter[]
	 */
	public function spoolers($options = null)
	{
		$spoolers = $this->client->spoolers($options);
		$spoolers->domains = array($this->domain);
		return $spoolers;
	}

	/**
	 * get a list of Spooler connectors for this domain matching the requested $options
	 *
	 * @param array $options a list of options to match against spoolers
	 * @param integer $offset (optional) offset of first Spooler to retrieve
	 * @param integer $count (optional) number of Spooler instances to retrieve
	 *
	 * @return Spooler[]
	 */
	public function spooler_list($options = null, $offset = 0, $limit = 20)
	{
		$spoolers = $this->spoolers($options);
		$res = $spoolers->get($offset, $limit);
		return $res;
	}

	/**
	 * create a new spooler for this Domain and obtain a Spooler connector
	 *
	 * @param string $type type of the spooler to create (service, campaign, transactional, permanent)
	 *
	 * @return Spooler
	 */
	public function spooler_create($type = "campaign")
	{
		$params = array('type'  => $type);
		$id = $this->client->rest_call('domain/'.$this->domain.'/create-spooler', $params, "POST");
		$data = $this->client->rest_call('spooler/'.$id."/load", null, "POST");
		return new Spooler($this->client, $data['id'], $data);
	}

	/**
	 * Obtain a Notifications connector for this Domain
	 *
	 * @return Notifications
	 */
	public function notifications()
	{
		return new Notifications($this->client, $this->domain);
	}

	/**
	 * Obtain a Templates connector for this Domain
	 *
	 * @return Templates
	 */
	public function templates()
	{
		return new Templates($this->client, $this->domain);
	}

	/**
	 * @ignore
	 */
	public function statistics($destination = null)
	{
		if ($destination == null)
			$params = array();
		else
			$params = array("destination" => $destination);
		return new Statistics($this->client->rest_call('domain/'.$this->domain.'/statistics', $params, "POST"));
	}

	/**
	 * @ignore
	 */
	public function timeline($timeframe = "weekly")
	{
		if ($timeframe != "daily" &&
		    $timeframe != "weekly" &&
		    $timeframe != "monthly" &&
		    $timeframe != "yearly")
			throw new ClientException("invalid timeframe");
		return $this->client->rest_call('domain/' . $this->domain . '/timeline/' . $timeframe,
		    null, "POST");
	}

	/**
	 * @ignore
	 */
	public function yp()
	{
		return new Yp($this->client, $this->domain);
	}
}

?>
