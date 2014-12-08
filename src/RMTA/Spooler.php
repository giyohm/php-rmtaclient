<?php
/*
 * Copyright (c) 2013 Gilles Chehade <gilles@rentabiliweb.com>
 *
 * WTFPL :)
 *
 */

namespace RMTA;

class Spooler
{
	function __construct($client, $spooler_id, $data)
	{
		$this->client = $client;
		$this->id         = $spooler_id;
		$this->domain     = null;
		$this->type       = null;
		$this->state      = null;
		$this->summary    = null;
		$this->params     = array();
		$this->content    = new Content();
		$this->_setup($data);
	}

	private function _setup($data)
	{
		$this->domain     = $data['domain'];
		$this->type       = $data['type'];
		$this->state      = $data['state'];
		$this->summary    = $data['summary'];

		$this->params['name'] = $data['name'];
		$this->params['start'] = $data['start'];
		$this->params['ttl'] = $data['ttl'];

		if (is_array($data['properties']))
			$this->content->content = $data['properties'];
	}

	function identifier()
	{
		return $this->id;
	}

	function name($value = null)
	{
		if ($value === null) {
			if (!array_key_exists('name', $this->params))
				return null;
			return $this->params['name'];
		}
		else {
			$this->params['name'] = $value;
		}
	}

	function start($value = null)
	{
		if ($value === null) {
			if (!array_key_exists('start', $this->params))
				return null;
			return $this->params['start'];
		}
		else {
			$this->params['start'] = $value;
		}
	}

	function ttl($value = null)
	{
		if ($value === null) {
			if (!array_key_exists('ttl', $this->params))
				return null;
			return $this->params['ttl'];
		}
		else {
			$this->params['ttl'] = $value;
		}
	}

	public function queue($options = null)
	{
		return new QueueIterator($this, $options);
	}

	public function batch()
	{
		return new SpoolBatch($this);
	}

	public function mail($recipient)
	{
		return new Mail($this, $recipient);
	}

	public function update()
	{
		$params = array(
			'name'		=> $this->params['name'],
			'start'		=> $this->params['start'],
			'ttl'		=> $this->params['ttl'],
			'properties'    => $this->content->_serialize(),
		);
		return $this->client->rest_call('spooler/'.$this->id.'/update', $params, "POST");
	}

	public function shoot()
	{
		return $this->client->rest_call('spooler/'.$this->id.'/shoot', null, "POST");
	}

	public function cancel()
	{
		return $this->client->rest_call('spooler/'.$this->id.'/cancel', null, "POST");
	}

	public function scoring()
	{
		return $this->client->rest_call('spooler/'.$this->id.'/scoring', null, "POST");
	}

	public function statistics($destination = null)
	{
		$params = array("destination" => $destination);
		return new Statistics($this->client->rest_call('spooler/'.$this->id.'/statistics', $params, "POST"));
	}
}

?>
