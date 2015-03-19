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
			try {
				$params = array('name' => $value);
				$this->client->rest_call('spooler/'.$this->id.'/set-name', $params, "POST");
			} catch (Exception $e) {
				/* temporarily gracefully fail to cope with new client hitting current production */
			}
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
			try {
				$params = array('start' => $value);
				$this->client->rest_call('spooler/'.$this->id.'/set-start', $params, "POST");
			} catch (Exception $e) {
				/* temporarily gracefully fail to cope with new client hitting current production */
			}
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
			try {
				$params = array('ttl' => $value);
				$this->client->rest_call('spooler/'.$this->id.'/set-ttl', $params, "POST");
			} catch (Exception $e) {
				/* temporarily gracefully fail to cope with new client hitting current production */
			}
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

	public function timeline()
	{
		return $this->client->rest_call('spooler/' . $this->id . '/timeline', null, "POST");
	}
}

?>
