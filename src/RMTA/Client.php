<?php
/*
 * Copyright (c) 2013 Gilles Chehade <gilles@rentabiliweb.com>
 *
 * WTFPL :)
 *
 */

namespace \RMTA;


/**
 * URL of production API servers, default value for RMTAClient connector
 */
define('RMTA_API_URL', 'https://api2.rmta-services.com/api');

class Client
{
	/**
	 * @ignore
	 */
	function rest_call($remote_method, $params = null, $verb = 'POST')
	{
		$cparams = array( 'http' => array( 'method' => $verb, 'ignore_errors' => false, 'header' => "Content-type: application/json\r\n" ) );

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
			throw new RMTAServerException("fopen failed");

		$ret = stream_get_contents($fp);
		fclose($fp);
		if ($ret === false)
			throw new RMTAServerException("stream_get_contents failed");

		$json = json_decode($ret, true);
		if ($json === null)
			throw new RMTAServerException("json_decode failed");

		if (is_array($json) && array_key_exists("error", $json))
			throw new RMTARemoteCallError($json["error"], 0, NULL, $json["details"]);

		return $json;
	}

	/**
	 *
	 * RMTAClient constructor
	 *
	 * A RMTAClient instance abstracts a disconnected authenticated session to the RMTA infrastructure.
	 * Errors caused by network disruption do not require reinstantiating an object.
	 * The object may be cached and reused.
	 *
	 * @param string $username
	 * @param string $password
	 * @param string $url optional url for API servers, defaults to production
	 *
	 * @return void
	 */
	function __construct($username, $password, $url = RMTA_API_URL) {
		$this->token = null;
		$this->url = $url;
		$json = $this->rest_call('authenticate', array( 'username' => $username, 'password' => $password ), "POST");
		$this->token  = $json["token"];
	}

	public function api()
	{
		return new RMTAAPI($this);
	}


	/**
	 * @param string $domain
	 *
	 * @return RMTADomain an RMTADomain connector to $domain
	 */
	function domain($domain)
	{
		return $this->api()->domain($domain);
	}

	/**
	 * @param integer $id
	 *
	 * @return RMTASpooler an RMTASpooler connector to spooler id $id
	 */
	function spooler($spooler_id)
	{
		return $this->api()->spooler($spooler_id);
	}

	/**
	 * @return array an array of RMTADomain connectors to each domain registered for the authenticated client
	 */
	function domain_list()
	{
		return $this->api()->domain_list();
	}

	function spooler_list($options = null)
	{
		return $this->api()->spooler_list($options);
	}

	/**
	 * @ignore
	 */
	public function timeline($timeframe = "monthly")
	{
		if ($timeframe != "monthly")
			throw new RMTAClientException("invalid timeframe");
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
	public function scoreSpam($campaign)
	{
		if (! $campaign instanceof RMTACampaign)
			throw new RMTAClientException("expect a RMTACampaign instance");

	        $params = array(
			'domain'      => $campaign->domain,
			'subject'     => $campaign->subject,
			'html'        => $campaign->message_html,
			'text'        => $campaign->message_txt,
			'headers'     => $campaign->headers,
			'expand'      => $campaign->expand
		);
		return $this->rest_call('scoring/spam', $params, "POST");
	}

	/* standalone calls */
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


	/**/
}
?>
