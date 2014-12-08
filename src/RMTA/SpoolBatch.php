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
