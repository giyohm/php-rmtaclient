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

class Content
{
	function __construct($spooler = null)
	{
		$this->spooler = $spooler;
		$this->content = array();
	}

	function _serialize()
	{
		if (count($this->content) == 0)
		   return null;
		return $this->content;
	}

	function sender($value = null)
	{
		if ($value === null) {
			if (!array_key_exists('sender', $this->content))
				return null;
			return $this->content['sender'];
		}
		else {
			$this->content['sender'] = $value;
		}
	}

	function recipient($value = null)
	{
		if ($value === null) {
			if (!array_key_exists('recipient', $this->content))
				return null;
			return $this->content['recipient'];
		}
		else {
			$this->content['recipient'] = $value;
		}
	}
	
	function subject($value = null)
	{
		if ($value === null) {
			if (!array_key_exists('subject', $this->content))
				return null;
			return $this->content['subject'];
		}
		else {
			$this->content['subject'] = $value;
		}
	}

	function mirror($value = null)
	{
		if ($value === null) {
			if (!array_key_exists('mirror', $this->content))
				return null;
			return $this->content['mirror'];
		}
		else {
			$this->content['mirror'] = $value;
		}
	}

	function unsubscribe($type = null, $value = null)
	{
		if ($type === null || $value === null) {
			if (!array_key_exists('unsubscribe', $this->content))
				return null;
			return $this->content['unsubscribe'];
		}
		else {
			$this->content['unsubscribe'] = array($type, $value);
		}
	}

	function header($key, $value = null)
	{
		if ($value === null) {
			if (!array_key_exists('headers', $this->content))
				return null;
			if (!array_key_exists($key, $this->content['headers']))
				return null;
			return $this->content['headers'][$key][0];
		}
		else {
			if (!array_key_exists('headers', $this->content))
				$this->content['headers'] = array();
			$this->content['headers'][$key] = array($value);
		}
	}

	function expand($key, $value = null)
	{
		if ($value === null) {
			if (!array_key_exists('expands', $this->content))
				return null;
			if (!array_key_exists($key, $this->content['expands']))
				return null;
			return $this->content['expands'][$key];
		}
		else {
			if (!array_key_exists('expands', $this->content))
				$this->content['expands'] = array();
			$this->content['expands'][$key] = $value;
		}
	}

	function part($name, $type = null, $content = null)
	{
		/* TODO: change 'content' to 'parts' */
		if ($type === null || $content === null) {
			if (!array_key_exists('parts', $this->content))
				return null;
			if (!array_key_exists($name, $this->content['parts']))
				return null;
			if ($this->content['parts'][$name]['type'] != 'content')
				return null;
			return $this->content['parts'][$name];
		}
		else {
			if (!array_key_exists('parts', $this->content))
				$this->content['parts'] = array();
			$this->content['parts'][$name] = array('type' => 'content', 'content-type' => $type, 'content' => $content);
		}
	}

	function template($name, $template = null)
	{
		/* TODO: change 'content' to 'parts' */
		if ($template === null) {
			if (!array_key_exists('parts', $this->content))
				return null;
			if (!array_key_exists($name, $this->content['parts']))
				return null;
			if ($this->content['parts'][$name]['type'] != 'template')
				return null;
			return $this->content['parts'][$name];
		}
		else {
			if (!array_key_exists('parts', $this->content))
				$this->content['content'] = array();
			$this->content['parts'][$name] = array('type' => 'template', 'template' => $template);
		}
	}

       	public function update()
	{
		if ($this->spooler) {
			$params = array('content' => $this->_serialize());
			return $this->spooler->client->rest_call('spooler/'.$this->spooler->id.'/set-content', $params, "POST");
		}
	}
}

?>
