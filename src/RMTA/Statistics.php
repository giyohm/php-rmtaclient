<?php
/*
 * Copyright (c) 2013 Gilles Chehade <gilles@rentabiliweb.com>
 *
 * WTFPL :)
 *
 */

namespace RMTA;

class Statistics
{
	function __construct($data)
	{
		$this->data = $data;
	}

	function json()
	{
		return json_encode($this->data);
	}

	function raw()
	{
		return $this->data;
	}
}

?>
