<?php
/*
 * Copyright (c) 2013 Gilles Chehade <gilles@rentabiliweb.com>
 *
 * WTFPL :)
 *
 */

namespace RMTA;

class Content
{
	function __construct()
	{
		$this->content = array();
	}

	function _serialize()
	{
		if (count($this->content) == 0)
		   return null;
		return $this->content;
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
			if (!array_key_exists('content', $this->content))
				return null;
			if (!array_key_exists($name, $this->content['content']))
				return null;
			if ($this->content['content'][$name]['type'] != 'content')
				return null;
			return $this->content['content'][$name];
		}
		else {
			if (!array_key_exists('content', $this->content))
				$this->content['content'] = array();
			$this->content['content'][$name] = array('type' => 'content', 'content-type' => $type, 'content' => $content);
		}
	}

	function template($name, $template = null)
	{
		/* TODO: change 'content' to 'parts' */
		if ($template === null) {
			if (!array_key_exists('content', $this->content))
				return null;
			if (!array_key_exists($name, $this->content['content']))
				return null;
			if ($this->content['content'][$name]['type'] != 'template')
				return null;
			return $this->content['content'][$name];
		}
		else {
			if (!array_key_exists('content', $this->content))
				$this->content['content'] = array();
			$this->content['content'][$name] = array('type' => 'template', 'template' => $template);
		}
	}
}

?>
