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
	/**
	 * @ignore
	 */
	function __construct($client, $spooler_id, $data)
	{
		$this->client = $client;
		$this->id         = $spooler_id;
		$this->_token     = null;
		$this->domain     = null;
		$this->type       = null;
		$this->state      = null;
		$this->summary    = null;
		$this->params     = array();
		$this->content    = new Content($this);
		$this->_setup($data);
	}

	/**
	 * @ignore
	 */
	private function _setup($data)
	{
		$this->domain     = $data['domain'];
		$this->type       = $data['type'];
		$this->state      = $data['state'];
		$this->summary    = $data['summary'];
		$this->_token     = $data['token'];

		$this->params['name'] = $data['name'];
		$this->params['start'] = $data['start'];
		$this->params['ttl'] = $data['ttl'];

		if (is_array($data['content']))
			$this->content->content = $data['content'];
	}

	/**
	 * Return the identifier associated to this Spooler instance
	 *
	 * When created, each spooler is assigned a unique numerical identifier which identifies it in the system.
	 *
	 * @return integer spooler identifier
	 */
	function identifier()
	{
		return $this->id;
	}

	function token()
	{
		return $this->_token;
	}

	/**
	 * Get or Set the name for this Spooler instance.
	 *
	 * Each spooler may be given a name for users to identify it in a more friendly manner.
	 * When called without parameter, the method returns the current label.
	 * When called with a parameter, the method will request that this parameter be the new label.
	 *
	 * @param string $value name to assign to the spooler
	 *
	 * @return string|void spooler name if get, nothing if set
	 */
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

	/**
	 * Get or Set the start timestamp for this Spooler
	 *
	 * A spooler will only be able to send mail when its start date has been reached.
	 *
	 * When called without parameter, the method will return the current start timestamp.
	 * When called with a parameter, the method will request that this timestamp be the new start date.
	 *
	 * The $value must be a Unix timestamp set in the future.
	 *
	 * @param integer $value start date after which a spooler is allowed to shoot.
	 *
	 * @return integer current start date
	 */
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

	/**
	 * Get or Set the time-to-live for a Spooler or for Mail objects spooled into it.
	 *
	 * Each spooler has a time-to-live (ttl) associated to it which controls how long it should attempt
	 * to deliver Mail objects spooled to it.
	 *
	 * In addition, spoolers of type "campaign" will end when ttl is reached.
	 *
	 * When called without parameter, the method will return the current ttl.
	 * When called with a parameter, the method will request that this new ttl is set.
	 *
	 * The $value must be a number of seconds.
	 *
	 * @param integer $value number of seconds after which a spooler should expire Mail objects.
	 *
	 * @return integer current ttl
	 */
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

	/**
	 * Obtain a QueueFilter instance allowing the inspection of the queue associated to a Spooler.
	 *
	 * Each spooler has a queue of Mail objects that are meant to be sent to their destination.
	 * This method returns an iterator for (a subset of) the queue.
	 *
	 * @param array $options (optional) options that individual Mail objects should match to be returned
	 *
	 * @return QueueFilter
	 */
	public function queue($options = null)
	{
		return new QueueFilter($this, $options);
	}

	/**
	 * Obtain a SpoolBatch instance suitable for spooling a collection of Mail objects.
	 *
	 * To be part of a shoot, a Mail object needs to be added to the Spooler queue.
	 * When submitting large numbers of Mail objects, it is preferable to batch them into a single request
	 * to avoid API call overhead and let the server split the batch into individual Mail objects.
	 *
	 * This method returns a SpoolBatch object which abstracts the batching process and allows for
	 * submitting several Mail objects as part of a single request.
	 *
	 * @return SpoolBatch
	 */
	public function batch()
	{
		return new SpoolBatch($this);
	}

	/**
	 * Obtain a SpoolMail instance suitable for spooling a single recipient.
	 *
	 * To be part of a shoot, a Mail object needs to be added to the Spooler queue.
	 * A Mail object describes a particular message for a specific recipient.
	 *
	 * @param string $recipient email address of the message recipient
	 *
	 * @return SpoolMail
	 */
	public function mail($recipient)
	{
		return new Mail($this, $recipient);
	}

	/**
	 * Mark as spooler as ready
	 *
	 * To avoid accidental shootings, a spooler may only start sending messages when it is in a ready state.
	 *
	 * This method commits the Spooler configuration, forbidding further changes, and marks the spooler as
	 * ready to be sent when its start date is reached.
	 *
	 * After a spooler has been marked ready, it can no longer be altered except for its name.
	 * Spooling is allowed as long as the Spooler has not reached its ttl.
	 * Content altering is disallowed.
	 *
	 * @return boolean
	 */
	public function ready()
	{
		return $this->client->rest_call('spooler/'.$this->id.'/ready', null, "POST");
	}

	/**
	 * Cancels a spooler
	 *
	 * After a spooler has been marked as ready, it may be desirable to cancel it.
	 * This method will cancel a spooler that has been marked ready.
	 *
	 * If the method is called before a spooler has started sending, it will immediately be discarded.
	 * If the method is called while a spooler is shooting, the shooting will be interrupted.
	 * 
	 * @return boolean
	 */
	public function cancel()
	{
		return $this->client->rest_call('spooler/'.$this->id.'/cancel', null, "POST");
	}

	/**
	 * @ignore
	 */
	public function scoring()
	{
		return $this->client->rest_call('spooler/'.$this->id.'/scoring', null, "POST");
	}

	/**
	 * @ignore
	 */
	public function statistics($destination = null)
	{
		if ($destination == null)
			$params = array();
		else
			$params = array("destination" => $destination);
		return new Statistics($this->client->rest_call('spooler/'.$this->id.'/statistics', $params, "POST"));
	}

	/**
	 * @ignore
	 */
	public function timeline()
	{
		return $this->client->rest_call('spooler/' . $this->id . '/timeline', null, "POST");
	}
}

?>
