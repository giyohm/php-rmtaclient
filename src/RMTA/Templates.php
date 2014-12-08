<?php
/*
 * Copyright (c) 2013 Gilles Chehade <gilles@rentabiliweb.com>
 *
 * WTFPL :)
 *
 */

namespace RMTA;

class Templates
{
	function __construct($client, $domain)
	{
		$this->client = $client;
		$this->domain = $domain;
	}

	public function listing($type)
	{
		$params = array('type' => $type);
		return $this->client->rest_call('domain/'.$this->domain.'/templates/list', $params, "POST");
	}

	public function get($name)
	{
		$params = array('name' => $name);
		return $this->client->rest_call('domain/'.$this->domain.'/templates/get', $params, "POST");
	}

	public function add($name, $type, $content)
	{
		$params = array('type' => $type, 'name' => $name, 'content' => $content);
		return $this->client->rest_call('domain/'.$this->domain.'/templates/add', $params, "POST");
	}

	public function update($name, $type, $content)
	{
		$params = array('type' => $type, 'name' => $name, 'content' => $content);
		return $this->client->rest_call('domain/'.$this->domain.'/templates/update', $params, "POST");
	}

	public function remove($name)
	{
		$params = array('name' => $name);
		return $this->client->rest_call('domain/'.$this->domain.'/templates/remove', $params, "POST");
	}
}

?>
