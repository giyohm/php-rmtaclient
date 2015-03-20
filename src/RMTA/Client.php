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

  /**
   * @filesource
   */


namespace RMTA;

/*
 * URL of production API servers, default value for RMTAClient connector
 */
define('RMTA_API_URL', 'https://api2.rmta-services.com/api');
define('RMTA_API_VERSION', '2.0');

class Client
{
	/**
	 * @ignore
	 */
	function rest_call($remote_method, $params = null, $verb = 'POST')
	{
		$cparams = array( 'http' => array( 'method' => $verb, 'ignore_errors' => true, 'header' => "Content-type: application/json\r\n" ) );

		$url = $this->url.'/'.$remote_method.'/';
		if ($this->token)
			$url .= $this->token;

		if ($params !== null) {
		    $params = json_encode($params);
		    if ($verb == 'POST')
			$cparams['http']['content'] = $params;
		    else
			$url .= '?'.$params;
		}

		$context = stream_context_create($cparams);
		$fp = fopen($url, 'rb', false, $context);
		if (!$fp)
			throw new ServerException("fopen failed");

		$ret = stream_get_contents($fp);
		fclose($fp);
		if ($ret === false)
			throw new ServerException("stream_get_contents failed");
		$json = json_decode($ret, true);
		if ($json === null)
			throw new ServerException("json_decode failed");
		if (is_array($json) && array_key_exists("error", $json))
			throw new RemoteCallError($json["error"], 0, NULL);

		return $json;
	}

	/**
	 * Client constructor.
	 *
	 * Authenticates a client using a credentials tuple consisting of a username and password
	 *
	 * @param string $username client username
	 * @param string $password client password
	 * @param string $url (optional) API server URL for debugging purposes
	 * @param string $version (optional) API version for debugging purposes
	 *
	 * @return void
	 *
	 * @source 2 1 test
	 */
	function __construct($username, $password, $url = RMTA_API_URL, $version = RMTA_API_VERSION) {
		$this->token = null;
		$this->url = $url . '/' . $version;
		$json = $this->rest_call('authenticate', array( 'username' => $username, 'password' => $password ), "POST");
		$this->token  = $json["token"];
	}

	/**
	 * Return a Domain connector for a specific domain
	 *
	 * @param string $domain domain for which the connector is requested
	 *
	 * @return Domain
	 */
	function domain($domain)
	{
		return new Domain($this, $domain);
	}


	/**
	 * Return a Spooler connector for a specific spooler
	 *
	 * @param integer $spooler_id spooler identifier for which the connector is requested
	 *
	 * @return Spooler
	 */
	function spooler($spooler_id)
	{
		$s = $this->rest_call('spooler/'.$spooler_id.'/load', null, "POST");
		return new Spooler($this, $s['id'], $s);
	}

	/**
	 * Return a list of Domain connectors for all domains belonging to the client
	 *
	 * @return Domain[]
	 */
	function domain_list()
	{
		$ret = array();
		foreach ($this->rest_call('domain-list', null, "POST") as $value)
		    array_push($ret, $this->domain($value));
		return $ret;
	}

	/**
	 * Obtain a SpoolerFilter instance allowing the retreive spoolers matching certain conditions
	 *
	 * @param array $options a list of options that a spooler should match
	 *
	 * @return SpoolerFilter[]
	 */
	function spoolers($options = null)
	{
		return new SpoolerFilter($this, $options);
	}

	/**
	 * Return a list of Spooler connectors belonging to the client and respecting $options 
	 *
	 * @param array $options a list of options that a spooler should match to be returned by this call
	 * @param integer $offset (optional) offset of first Spooler to retrieve
	 * @param integer $count (optional) number of Spooler instances to retrieve
	 *
	 * @return Spooler[]
	 */
	function spooler_list($options = null, $offset = 0, $limit = 20)
	{
		$spoolers = $this->spoolers($options);
		$res = $spoolers->get($offset, $limit);
		return $res;
	}

	/**
	 * @ignore
	 */
	public function timeline($timeframe = "monthly")
	{
		if ($timeframe != "monthly")
			throw new ClientException("invalid timeframe");
		return $this->rest_call('statistics/entity/timeline/' . $timeframe,
		    null, "POST");
	}


	/* TEMPORARY AND / OR LEGACY */
	/**
	 * @ignore
	 */
	public function scoreText($text)
	{
	        $params = array(
			'text' => $text
		);
		return $this->rest_call('scoring/text', $params, "POST");
	}
	
	/**
	 * @ignore
	 */
	public function cleanupText($text)
	{
	        $params = array(
			'text' => $text
		);
		return $this->rest_call('cleanup/text', $params, "POST");
	}
	
	/**
	 * @ignore
	 */
	public function scoreHtml($html)
	{
	        $params = array(
			'html' => $html
		);
		return $this->rest_call('scoring/html', $params, "POST");
	}
	
	/**
	 * @ignore
	 */
	public function cleanupHtml($html)
	{
	        $params = array(
			'html' => $html
		);
		return $this->rest_call('cleanup/html', $params, "POST");
	}
	
	/**
	 * @ignore
	 */
	public function Html2Text($html)
	{
	        $params = array(
			'html' => $html
		);
		return $this->rest_call('cleanup/html2text', $params, "POST");
	}

	/**
	 * @ignore
	 */
	public function html_to_text($html)
	{
		$params = array(
			'html' => $html
		);
		return $this->rest_call('standalone/html-to-text', $params, "POST");
	}
}

?>
