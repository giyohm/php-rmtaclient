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
	/**
	 * @ignore
	 */
	function __construct($client, $domain)
	{
		$this->client = $client;
		$this->domain = $domain;
	}

	/**
	 * @return integer number of pending notifications in current connector
	 */
	public function count()
	{
		return $this->client->rest_call('notifications/' . $this->domain . '/count' , array(), "POST");
	}

	/**
	 *
	 * @param integer $count number of notifications to retrieve from current connector, defaults to 100
	 *
	 * @return array an array of at most $count notifications
	 */
	public function get($count = 100)
	{
		$params = array('count'  => $count);
		return $this->client->rest_call('domain/'.$this->domain.'/notifications/get', $params, "POST");
	}

	/**
	 *
	 * @param array $ids an array of notification identifiers to delete from current connector
	 *
	 * @return void
	 */
	public function delete($ids)
	{
		$params = array('ids'=>$ids);
		$this->client->rest_call('domain/'.$this->domain.'/notifications/delete', $params, "POST");
	}
}

?>
