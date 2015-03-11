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

class Preview
{
	/**
	 * @ignore
	 */
	function __construct($preview)
	{
		$this->preview = $preview;
	}

	/**
	 * Retrieve the SMTP level sender used for this message
	 *
	 * @return string
	 */
	function sender()
	{
		return $this->preview['sender'];
	}

	/**
	 * Retrieve the SMTP level recipient used for this message
	 *
	 * @return string
	 */
	function recipient()
	{
		return $this->preview['rcpt'];
	}

	/**
	 * Retrieve the subject for this message
	 *
	 * @return string
	 */
	function subject()
	{
		return $this->preview['subject'];
	}

	/**
	 * Retrieve part named $name with expansion variables rendered
	 *
	 * @return string
	 */
	function part($name)
	{
		if (! isset($this->preview['parts'][$name]))
		    return null;
		return $this->preview['parts'][$name];
	}

	/**
	 * Retrieve a rendered version of the entire email as seen on the wire
	 *
	 * @return string
	 */
	function email()
	{
		return $this->preview['email'];
	}
}

?>
