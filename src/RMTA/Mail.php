<?php
/*
 * Copyright (c) 2013 Gilles Chehade <gilles@rentabiliweb.com>
 *
 * WTFPL :)
 *
 */

namespace RMTA;

class Mail
{
	function __construct($spooler, $recipient)
	{
		$this->spooler   = $spooler;
		$this->recipient = $recipient;
		$this->content   = new Content();
	}

	public function spool()
	{
		$params = array("recipients" => array($this->recipient => $this->content->_serialize()));
		$ret = $this->spooler->client->rest_call('spooler/'.$this->spooler->id.'/spool', $params, "POST");
		return $ret[0];
	}

	public function score()
	{
		$params = array("properties" => $this->content->_serialize());
		return $this->spooler->client->rest_call('spooler/'.$this->spooler->id.'/score', $params, "POST");
	}
}

?>
