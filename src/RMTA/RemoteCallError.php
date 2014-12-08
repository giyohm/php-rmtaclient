<?php
/*
 * Copyright (c) 2013 Gilles Chehade <gilles@rentabiliweb.com>
 *
 * WTFPL :)
 *
 */

namespace RMTA;

/**
 * @ignore
 */
class RemoteCallError extends ServerException
{
	protected $_details;
	public function __construct($message = "", $code = 0, Exception $previous = NULL, $details = NULL)
	{
		parent::__construct($message, $code, $previous);
		$this->_details = $details;
	}
	public function getDetails()
	{
		return $this->_details;
	}
}
?>
