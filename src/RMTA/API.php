<?php
/*
 * Copyright (c) 2013 Gilles Chehade <gilles@rentabiliweb.com>
 *
 * WTFPL :)
 *
 */

namespace RMTA;

class API
{
	function __construct($client)
	{
		$this->client = $client;
	}

	/**
	 * @param string $domain
	 *
	 * @return RMTADomain an RMTADomain connector to $domain
	 */
	function domain($domain)
	{
		return new RMTADomain($this->client, $domain);
	}

	/**
	 * @param integer $id
	 *
	 * @return RMTASpooler an RMTASpooler connector to spooler id $id
	 */
	function spooler($spooler_id)
	{
		$s = $this->client->rest_call('spooler/'.$spooler_id.'/load', null, "POST");
		return new RMTASpooler($this->client, $s['id'], $s);
	}

	/**
	 * @return array an array of RMTADomain connectors to each domain registered for the authenticated client
	 */
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
			array_push($res, new RMTASpooler($this->client, $value['id'], $value));
		return $res;		
	}
}

?>
