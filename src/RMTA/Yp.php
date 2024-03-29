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

class Yp
{
	function __construct($client, $domain)
	{
		$this->client = $client;
		$this->domain = $domain;
	}

	function mail($address)
	{
		return new YpMail($this, $address);
	}

	function batch()
	{
		return new YpBatch($this);
	}

	function score($request_id)
	{
		$params = array("request-id" => $request_id);
		return $this->client->rest_call('yp/'.$this->domain.'/get-scoring-result', $params, "POST");
	}
}

?>
