<?php
/*
 * Copyright (c) 2013 Gilles Chehade <gilles@rentabiliweb.com>
 *
 * WTFPL :)
 *
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

	public function name()
	{
		return $this->domain;
	}

	/**
	 * @ignore
	 */
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

	/**
	 * @param string $type the type of the spooler about to be created: service, transactional or campaign
	 *
	 * @return Spooler a Spooler connector to a newly created spooler of type $type
	 */
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

	/**
	 * @return RMTANotifications a RMTANotifications connector for the current domain
	 */
	public function notifications()
	{
		return new RMTANotifications($this->client, $this->domain);
	}

	/**
	 * @return RMTATemplates a RMTATemplates connector for the current domain
	 */
	public function templates()
	{
		return new RMTATemplates($this->client, $this->domain);
	}


	/**
	 * @param string $destination optional destination domain or provider
	 *
	 * @return mixed a hash table of statistics, possibly restricted to a single destination.
	 */
	public function statistics($destination = null)
	{
		$params = array(
			"domain"      => $this->domain,
		        "destination" => $destination
		);
		return new RMTAStatistics($this->client->rest_call('domain/statistics', $params, "POST"));
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
		return $this->client->rest_call('statistics/domain/' . $this->domain . '/timeline/' . $timeframe,
		    null, "POST");
	}
}

?>
