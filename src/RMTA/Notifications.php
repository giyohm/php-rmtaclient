<?php
/*
 * Copyright (c) 2013 Gilles Chehade <gilles@rentabiliweb.com>
 *
 * WTFPL :)
 *
 */

namespace RMTA;

class Notifications
{
	function __construct($client, $domain)
	{
		$this->client = $client;
		$this->domain = $domain;
	}

	public function count()
	{
		return $this->client->rest_call('notifications/' . $this->domain . '/count' , array(), "POST");
	}

	public function get($count = 100)
	{
		$params = array('count'  => $count);
		return $this->client->rest_call('domain/'.$this->domain.'/notifications/get', $params, "POST");
	}

	public function delete($ids)
	{
		$params = array('ids'=>$ids);
		$this->client->rest_call('domain/'.$this->domain.'/notifications/delete', $params, "POST");
	}
}

?>
