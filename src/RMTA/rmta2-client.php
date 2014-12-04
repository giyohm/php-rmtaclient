<?php
/*
 * Copyright (c) 2013 Gilles Chehade <gilles@rentabiliweb.com>
 *
 * WTFPL :)
 *
 */

/**
 * URL of production API servers, default value for RMTAClient connector
 */
define('RMTA_API_URL', 'https://api2.rmta-services.com/api');


/*<MAKE_PHP_SUPER_STRICT>*/
error_reporting(-1);
/**
 * @ignore
 */
function be_strict_for_fscking_sake($errno, $errstr, $errfile, $errline)
{ throw new RMTAClientException($errstr); }
set_error_handler("be_strict_for_fscking_sake");
/*</MAKE_PHP_SUPER_STRICT>*/


/**
 * @ignore
 */
class RMTAException extends Exception {}

class RMTAClientException extends RMTAException {}
class RMTAServerException extends RMTAException {}
class RMTARemoteCallError extends RMTAServerException
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

class RMTAMail
{
	function __construct($spooler, $recipient)
	{
		$this->spooler   = $spooler;
		$this->recipient = $recipient;
		$this->content   = new RMTAContent();
	}

	public function spool()
	{
		$params = array("recipients" => array($this->recipient => $this->content->_serialize()));
		$ret = $this->spooler->client->rest_call('spooler/'.$this->spooler->id.'/spool', $params, "POST");
		return $ret[0];
	}

	public function score()
	{
		$params = array("properties" => $this->content->_serialize());
		return $this->spooler->client->rest_call('spooler/'.$this->spooler->id.'/score', $params, "POST");
	}
}

class RMTAQueueIterator
{
	function __construct($spooler, $options)
	{
		$this->spooler = $spooler;

		$this->domain	= null;
		$this->routing	= null;
		$this->router	= null;
		$this->activity	= null;

		if ($options != null) {
			if (array_key_exists("domain", $options))
				$this->domain = is_array($options['domain']) ? $options['domain'] : array($options['domain']);
			if (array_key_exists("routing", $options) && $options['routing'] != null)
				$this->routing = is_array($options['routing']) ? $options['routing'] : array($options['routing']);
			if (array_key_exists("router", $options) && $options['router'] != null)
				$this->router = is_array($options['router']) ? $options['router'] : array($options['router']);
			if (array_key_exists("activity", $options) && $options['activity'] != null)
				$this->activity = is_array($options['activity']) ? $options['activity'] : array($options['activity']);
		}
	}

	public function size()
	{
		$params = array(
			"domain"  => $this->domain,
			"routing" => $this->routing,
			"router" => $this->router,
			"activity" => $this->activity,
			);
		return $this->spooler->client->rest_call('spooler/'.$this->spooler->id.'/queue/size', $params, "POST");
	}

	public function mails($offset = 0, $count = 5000)
	{
		$params = array(
			"offset"   => $offset,
			"count"    => $count,
			"domain"   => $this->domain,
			"routing"  => $this->routing,
			"router"   => $this->router,
			"activity" => $this->activity,
			);
		return $this->spooler->client->rest_call('spooler/'.$this->spooler->id.'/queue/mails', $params, "POST");
	}
}

class RMTASpoolBatch
{
	function __construct($spooler)
	{
		$this->spooler    = $spooler;
		$this->recipients = array();
	}

	public function mail($recipient)
	{
		$rcpt = new RMTAMail($this->spooler, $recipient);
		$this->recipients[$recipient] = $rcpt;
		return $rcpt;
	}

	public function spool()
	{
		if (count($this->recipients) == 0)
			throw new RMTAClientException("can't spool an empty batch");

		$r = array();
		foreach ($this->recipients as $rcpt)
		    $r[$rcpt->recipient] = $rcpt->content->_serialize();
		$params = array("recipients" => $r);
		unset($this->recipients);
		$this->recipients = array();
		return $this->spooler->client->rest_call('spooler/'.$this->spooler->id.'/spool', $params, "POST");
	}
}

class RMTAContent
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

class RMTASpooler
{
	/**
	 * @ignore
	 */
	function __construct($client, $spooler_id, $data)
	{
		$this->client = $client;
		$this->id         = $spooler_id;
		$this->domain     = null;
		$this->type       = null;
		$this->state      = null;
		$this->summary    = null;
		$this->params     = array();
		$this->content    = new RMTAContent();
		$this->_setup($data);
	}

	private function _setup($data)
	{
		$this->domain     = $data['domain'];
		$this->type       = $data['type'];
		$this->state      = $data['state'];
		$this->summary    = $data['summary'];

		$this->params['name'] = $data['name'];
		$this->params['start'] = $data['start'];
		$this->params['ttl'] = $data['ttl'];

		if (is_array($data['properties']))
			$this->content->content = $data['properties'];
	}

	function identifier()
	{
		return $this->id;
	}

	function name($value = null)
	{
		if ($value === null) {
			if (!array_key_exists('name', $this->params))
				return null;
			return $this->params['name'];
		}
		else {
			$this->params['name'] = $value;
		}
	}

	function start($value = null)
	{
		if ($value === null) {
			if (!array_key_exists('start', $this->params))
				return null;
			return $this->params['start'];
		}
		else {
			$this->params['start'] = $value;
		}
	}

	function ttl($value = null)
	{
		if ($value === null) {
			if (!array_key_exists('ttl', $this->params))
				return null;
			return $this->params['ttl'];
		}
		else {
			$this->params['ttl'] = $value;
		}
	}

	public function queue($options = null)
	{
		return new RMTAQueueIterator($this, $options);
	}

	public function batch()
	{
		return new RMTASpoolBatch($this);
	}

	public function mail($recipient)
	{
		return new RMTAMail($this, $recipient);
	}

	/**
	 * @return RMTASpooler a RMTASpooler connector to the updated spooler
	 */
	public function update()
	{
		$params = array(
			'name'		=> $this->params['name'],
			'start'		=> $this->params['start'],
			'ttl'		=> $this->params['ttl'],
			'properties'    => $this->content->_serialize(),
		);
		return $this->client->rest_call('spooler/'.$this->id.'/update', $params, "POST");
	}

	/**
	 *
	 * marks the current spooler ready for shoot
	 *
	 * @return void
	 */
	public function shoot()
	{
		return $this->client->rest_call('spooler/'.$this->id.'/shoot', null, "POST");
	}


	/**
	 *
	 * cancels the spooler identified by the current spooler connector
	 *
	 * @return void
	 */
	public function cancel()
	{
		return $this->client->rest_call('spooler/'.$this->id.'/cancel', null, "POST");
	}

	public function scoring()
	{
		return $this->client->rest_call('spooler/'.$this->id.'/scoring', null, "POST");
	}

	public function statistics($destination = null)
	{
		$params = array("destination" => $destination);
		return new RMTAStatistics($this->client->rest_call('spooler/'.$this->id.'/statistics', $params, "POST"));
	}
}

class RMTADomain
{
	/**
	 * @ignore
	 */
	function __construct($client, $name)
	{
		$this->client = $client;
		$this->domain = $name;
	}

	public function name()
	{
		return $this->domain;
	}

	/**
	 * @ignore
	 */
	public function spooler_list($options=null)
	{
		$domain	= $this->domain;
		$type   = null;
		$state	= null;
		if ($options != null) {
			if (array_key_exists("type", $options) && $options['type'] != null)
				$type = is_array($options['type']) ? $options['type'] : array($options['type']);
			if (array_key_exists("state", $options) && $options['state'] != null)
				$state = is_array($options['state']) ? $options['state'] : array($options['state']);
		}

		$params = array(
			"domain"=> $domain,
			"type"	=> $type,
			"state"	=> $state,
		);

		$res = array();
		foreach ($this->client->rest_call('spooler-list', $params, "POST") as $value)
		    array_push($res, new RMTASpooler($this->client, $value['id'], $value));
		return $res;
	}

	/**
	 * @param string $type the type of the spooler about to be created: service, transactional or campaign
	 *
	 * @return RMTASpooler a RMTASpooler connector to a newly created spooler of type $type
	 */
	public function spooler_create($type = "campaign")
	{
		$params = array(
			'name'  => "No name",
			'type'  => $type,
			'start' => time(),
			'ttl'   => 4 * 24 * 60 * 60,
			);
		$id = $this->client->rest_call('domain/'.$this->domain.'/create-spooler', $params, "POST");
		$data = $this->client->rest_call('spooler/'.$id."/load", null, "POST");
		return new RMTASpooler($this->client, $data['id'], $data);
	}

	/**
	 * @return RMTANotifications a RMTANotifications connector for the current domain
	 */
	public function notifications()
	{
		return new RMTANotifications($this->client, $this->domain);
	}

	/**
	 * @return RMTATemplates a RMTATemplates connector for the current domain
	 */
	public function templates()
	{
		return new RMTATemplates($this->client, $this->domain);
	}


	/**
	 * @param string $destination optional destination domain or provider
	 *
	 * @return mixed a hash table of statistics, possibly restricted to a single destination.
	 */
	public function statistics($destination = null)
	{
		$params = array(
			"domain"      => $this->domain,
		        "destination" => $destination
		);
		return new RMTAStatistics($this->client->rest_call('domain/statistics', $params, "POST"));
	}


	/**
	 * @ignore
	 */
	public function timeline($timeframe = "weekly")
	{
		if ($timeframe != "daily" &&
		    $timeframe != "weekly" &&
		    $timeframe != "monthly" &&
		    $timeframe != "yearly")
			throw new RMTAClientException("invalid timeframe");
		return $this->client->rest_call('statistics/domain/' . $this->domain . '/timeline/' . $timeframe,
		    null, "POST");
	}
}

class RMTANotifications
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
	 * @return integer number of pending notifications in current connector
	 */
	public function count()
	{
		return $this->client->rest_call('notifications/' . $this->domain . '/count' , array(), "POST");
	}

	/**
	 *
	 * @param integer $count number of notifications to retrieve from current connector, defaults to 100
	 *
	 * @return array an array of at most $count notifications
	 */
	public function get($count = 100)
	{
		$params = array('count'  => $count);
		return $this->client->rest_call('domain/'.$this->domain.'/notifications/get', $params, "POST");
	}

	/**
	 *
	 * @param array $ids an array of notification identifiers to delete from current connector
	 *
	 * @return void
	 */
	public function delete($ids)
	{
		$params = array('ids'=>$ids);
		$this->client->rest_call('domain/'.$this->domain.'/notifications/delete', $params, "POST");
	}
}

class RMTATemplates {
	/**
	 * @ignore
	 */
	function __construct($client, $domain)
	{
		$this->client = $client;
		$this->domain = $domain;
	}

	/**
	 * @ignore
	 */
	public function listing($type)
	{
		$params = array('type' => $type);
		return $this->client->rest_call('domain/'.$this->domain.'/templates/list', $params, "POST");
	}

	/**
	 * @ignore
	 */
	public function get($name)
	{
		$params = array('name' => $name);
		return $this->client->rest_call('domain/'.$this->domain.'/templates/get', $params, "POST");
	}

	/**
	 * @ignore
	 */
	public function add($name, $type, $content)
	{
		$params = array('type' => $type, 'name' => $name, 'content' => $content);
		return $this->client->rest_call('domain/'.$this->domain.'/templates/add', $params, "POST");
	}

	/**
	 * @ignore
	 */
	public function update($name, $type, $content)
	{
		$params = array('type' => $type, 'name' => $name, 'content' => $content);
		return $this->client->rest_call('domain/'.$this->domain.'/templates/update', $params, "POST");
	}

	/**
	 * @ignore
	 */
	public function remove($name)
	{
		$params = array('name' => $name);
		return $this->client->rest_call('domain/'.$this->domain.'/templates/remove', $params, "POST");
	}
}

class RMTAStatistics
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

class RMTAAPI
{
	function __construct($client)
	{
		$this->client = $client;
	}

	/**
	 * @param string $domain
	 *
	 * @return RMTADomain an RMTADomain connector to $domain
	 */
	function domain($domain)
	{
		return new RMTADomain($this->client, $domain);
	}

	/**
	 * @param integer $id
	 *
	 * @return RMTASpooler an RMTASpooler connector to spooler id $id
	 */
	function spooler($spooler_id)
	{
		$s = $this->client->rest_call('spooler/'.$spooler_id.'/load', null, "POST");
		return new RMTASpooler($this->client, $s['id'], $s);
	}

	/**
	 * @return array an array of RMTADomain connectors to each domain registered for the authenticated client
	 */
	function domain_list()
	{
		$ret = array();
		foreach ($this->client->rest_call('domain-list', null, "POST") as $value)
		    array_push($ret, $this->domain($value));
		return $ret;
	}

	function spooler_list($options = null)
	{
		$domain	= null;
		$type   = null;
		$state	= null;
		if ($options != null) {
			if (array_key_exists("domain", $options) && $options['domain'] != null)
				$domain = is_array($options['domain']) ? $options['domain'] : array($options['domain']);
			if (array_key_exists("type", $options) && $options['type'] != null)
				$type = is_array($options['type']) ? $options['type'] : array($options['type']);
			if (array_key_exists("state", $options) && $options['state'] != null)
				$state = is_array($options['state']) ? $options['state'] : array($options['state']);
		}

		$params = array(
			"domain"=> $domain,
			"type"	=> $type,
			"state"	=> $state,
		);

		$res = array();
		foreach ($this->client->rest_call('spooler-list', $params, "POST") as $value)
			array_push($res, new RMTASpooler($this->client, $value['id'], $value));
		return $res;		
	}
}

class RMTAClient
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
