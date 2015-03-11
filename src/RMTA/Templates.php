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

class Templates
{
	/**
	 * @ignore
	 */
	function __construct($client, $domain)
	{
		$this->client = $client;
		$this->domain = $domain;
	}

	public function listing($type)
	{
		$params = array('type' => $type);
		$a = array();
		foreach ($this->client->rest_call('domain/'.$this->domain.'/templates/list', $params, "POST") as $t)
		    array_push($a, new Template($this->client, $this->domain, $t));
		return $a;
		
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
