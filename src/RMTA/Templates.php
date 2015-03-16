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

	/**
	 * Obtain a listing of registered templates
	 *
	 * This method returns a list of Template objects matching $type
	 *
	 * @params string $type mime-type for which a listing is desired
	 *
	 * @return Template[]
	 */
	public function listing($type)
	{
		$params = array('type' => $type);
		$a = array();
		foreach ($this->client->rest_call('domain/'.$this->domain.'/templates/list', $params, "POST") as $t)
		    array_push($a, new Template($this->client, $this->domain, $t['id'], $t['name'], $t['type'], null));
		return $a;		
	}

	/**
	 * Retrieve a Template by name
	 *
	 * This method retrieves the Template associated to $name
	 *
	 * @params string $name the name of the Template to retrieve
	 *
	 * @return Template
	 */
	public function get($name)
	{
		$params = array('name' => $name);
		$data = $this->client->rest_call('domain/'.$this->domain.'/templates/get', $params, "POST");
		return new Template($this->client, $this->domain, $data['id'], $data['name'], $data['type'], $data['content']);
	}

	/**
	 * Register a new Template
	 *
	 * This method registers a new Template and returns the corresponding object
	 *
	 * @params string $name the name of the Template
	 * @params string $type the mime-type of the Template
	 * @params string $content the content of the Template
	 *
	 * @return Template
	 */
	public function add($name, $type, $content)
	{
		$params = array('type' => $type, 'name' => $name, 'content' => $content);
		$id = $this->client->rest_call('domain/'.$this->domain.'/templates/add', $params, "POST");
		return new Template($this->client, $this->domain, $id, $name, $type, $content);
	}

	/**
	 * Update an existing Template
	 *
	 * This method updates an existing template
	 *
	 * @params string $name the name of the Template
	 * @params string $type the mime-type of the Template
	 * @params string $content the content of the Template
	 *
	 * @return Template
	 */
	public function update($name, $type, $content)
	{
		$params = array('type' => $type, 'name' => $name, 'content' => $content);
		$id = $this->client->rest_call('domain/'.$this->domain.'/templates/update', $params, "POST");
		return new Template($this->client, $this->domain, $id, $name, $type, $content);
	}

	/**
	 * Remove an existing template
	 *
	 * This method removes an existing template
	 *
	 * @params string $name the name of the Template
	 *
	 */
	public function remove($name)
	{
		$params = array('name' => $name);
		$this->client->rest_call('domain/'.$this->domain.'/templates/remove', $params, "POST");
	}
}

?>
