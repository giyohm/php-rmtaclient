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

class Mail
{
	/**
	 * @ignore
	 */
	function __construct($spooler, $recipient, $info = null)
	{
		$this->spooler   = $spooler;
		$this->recipient = $recipient;
		$this->content   = new Content();
		$this->info      = $info;
	}

	/**
	 * Obtain the recipient associated to this Mail object
	 *
	 * @return string
	 */
	public function recipient()
	{
		return $this->recipient;
	}
	
	/**
	 * Request this Mail object to be added to the spooler
	 *
	 * @return array
	 */
	public function spool()
	{
		$params = array("recipients" => array($this->recipient => $this->content->_serialize()));
		return $this->spooler->client->rest_call('spooler/'.$this->spooler->id.'/spool', $params, "POST");
	}

	/**
	 * Request this Mail object to be scored
	 *
	 * @return Score
	 */
	public function score()
	{
		$params = array("recipient" => $this->recipient);
		$serialized = $this->content->_serialize();
		if ($serialized)
			$params['content'] = $serialized;
		return new Score($this->spooler->client->rest_call('spooler/'.$this->spooler->id.'/score', $params, "POST"));
	}

	/**
	 * Request this Mail object to be previewed
	 *
	 * @return Preview
	 */
	public function preview()
	{
		$params = array("recipient" => $this->recipient);
		$serialized = $this->content->_serialize();
		if ($serialized)
			$params['content'] = $serialized;
		return new Preview($this->spooler->client->rest_call('spooler/'.$this->spooler->id.'/preview', $params, "POST"));
	}
}

?>
