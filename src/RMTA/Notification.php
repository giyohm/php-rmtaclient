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

class Notification
{
	/**
	 * @ignore
	 */
	function __construct($notifications, $data)
	{
		$this->notifications = $notifications;
		$this->id   = $data['id'];
		$this->data = $data;
	}

	/**
	 * Return the Notification identifier
	 *
	 * @return integer
	 */
	public function id()
	{
		return $this->id;
	}

	/**
	 * Delete the current Notification from the Notifications queue
	 */
	public function delete()
	{
		$params = array('ids'=>array($this->id));
		$this->notifications->client->rest_call('domain/'.$this->notifications->domain.'/notifications/delete', $params, "POST");
	}

	public function timestamp()
	{
		return $this->data['timestamp'];
	}

	public function spooler()
	{
		return $this->data['spooler-id'];
	}

	public function notice()
	{
		return $this->data['notice'];
	}

	public function payload()
	{
		return $this->data['payload'];
	}
}

?>
