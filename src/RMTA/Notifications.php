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

class Notifications
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
	 * Request the number of pending notifications to process
	 *
	 * @return integer
	 */
	public function count()
	{
		return $this->client->rest_call('domain/'.$this->domain.'/notifications/count' , array(), "POST");
	}

	/**
	 * Retrieve $count notifications from the Notifications queue
	 *
	 * @return Notification[]
	 */
	public function get($count = 100)
	{
		$params = array('count'  => $count);
		$result = $this->client->rest_call('domain/'.$this->domain.'/notifications/get', $params, "POST");
		$notifs = array();
		foreach($result as $r) {
			array_push($notifs, new Notification($this, $r));
		}
		return $notifs;
	}

	/**
	 * Delete all notifications passed as parameter
	 *
	 * @param Notification[] $notifications list of Notification to delete
	 */
	public function delete($notifications)
	{
		$ids = array();
		foreach($notifications as $n) {
			array_push($ids, $n->identifier());
		}
		$params = array('ids'=>$ids);
		return $this->client->rest_call('domain/'.$this->domain.'/notifications/delete', $params, "POST");
	}


	/**
	 * Obtain the list of events for which we wanted to be notified
	 *
	 * @return string[]
	 */
	public function registered()
	{
		return $this->client->rest_call('domain/'.$this->domain.'/notifications/registered', array(), "POST");
	}

	/**
	 * Register an event for notifications
	 *
	 * @param string $value name of the event for which notifications should be generated
	 *
	 * @return boolean
	 */
	public function register($value)
	{
		$params = array("notice" => $value);
		return $this->client->rest_call('domain/'.$this->domain.'/notifications/register', $params, "POST");
	}

	/**
	 * Unregister an event for notifications
	 *
	 * @param string $value name of the event for which notifications should no longer be generated
	 *
	 * @return boolean
	 */
	public function unregister($value)
	{
		$params = array("notice" => $value);
		return $this->client->rest_call('domain/'.$this->domain.'/notifications/unregister', $params, "POST");
	}
}

?>
