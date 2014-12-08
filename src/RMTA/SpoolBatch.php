<?php
/*
 * Copyright (c) 2013 Gilles Chehade <gilles@rentabiliweb.com>
 *
 * WTFPL :)
 *
 */

namespace RMTA;

class SpoolBatch
{
	function __construct($spooler)
	{
		$this->spooler    = $spooler;
		$this->recipients = array();
	}

	public function mail($recipient)
	{
		$rcpt = new Mail($this->spooler, $recipient);
		$this->recipients[$recipient] = $rcpt;
		return $rcpt;
	}

	public function spool()
	{
		if (count($this->recipients) == 0)
			throw new ClientException("can't spool an empty batch");

		$r = array();
		foreach ($this->recipients as $rcpt)
		    $r[$rcpt->recipient] = $rcpt->content->_serialize();
		$params = array("recipients" => $r);
		unset($this->recipients);
		$this->recipients = array();
		return $this->spooler->client->rest_call('spooler/'.$this->spooler->id.'/spool', $params, "POST");
	}
}

?>
