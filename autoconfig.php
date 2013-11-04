<?php
abstract class ServerType
{
	const IMAP = 'imap';
	const POP = 'pop3';
	const SMTP = 'smtp';
}

abstract class SocketType
{
	const SSL = 'ssl';
	const STARTTLS = 'starttls';
	const OFF = 'plain';
}

abstract class Authentication
{
	const PLAIN = 'plain';
	const SECURE = 'secure';
	const NTLM = 'ntlm';
	const GSSAPI = 'gssapi';
	const CLIENT_UP = 'client-IP-address';
	const CERTIFICATE = 'TLS-client-cert';
	const NONE = 'none';
}

class Server
{
	public $type;
	public $hostname;
	public $port;
	public $ssl;
	public $username;
	public $authentication;

	static public function factory($type, $ssl, $authentication, $hostname, $username, $port = null)
	{
		$s = new self();
		$s->type = $type;
		$s->hostname = $hostname;
		$s->ssl = $ssl;
		$s->username = $username;
		$s->authentication = $authentication;
		$s->port = ($port === null ? self::getPort($type, $ssl) : $port);
		return $s;
	}

	static public function getPort($type, $ssl)
	{
		$ssl = (stripos($ssl, SocketType::SSL) !== false || $ssl === true);
		switch ($type)
		{
			case ServerType::POP: return $ssl ? 995 : 110;
			case ServerType::IMAP: return $ssl ? 993 : 143;
			case ServerType::SMTP: return $ssl ? 465 : 587;
			default: return 0;
		}
	}
}

class Mail
{
	public $name;
	public $domain;
	public $servers;
}

class AutoConfig
{
	private $dom;

	public function __construct()
	{
		$this->dom = new DOMDocument('1.0', 'UTF-8');
		$this->dom->formatOutput = true;
	}

	public function translate(Mail $mail)
	{
		$root = $this->dom->createElement('clientConfig');
		$this->dom->appendChild($root);
		$this->addAttribute($root, 'version', '1.1');

		$provider = $this->getNode('emailProvider', 'id', $mail->domain);
		$root->appendChild($provider);

		// Set up default email provider
		$this->addElement($provider, 'domain', strtolower($mail->domain));
		$this->addElement($provider, 'displayName', ucfirst($mail->name));
		$this->addElement($provider, 'displayShortName', ucfirst($mail->name));

		// Build servers
		foreach ($mail->servers as &$server)
		{
			// Make the server
			$s = $this->getNode($server->type == ServerType::SMTP ? 'outgoingServer' : 'incomingServer', 'type', strtolower($server->type));

			$this->addElement($s, 'hostname', $server->hostname);
			$this->addElement($s, 'port', $server->port);
			$this->addElement($s, 'socketType', strtoupper($server->ssl));
			$this->addElement($s, 'authentication', $server->authentication);
			$this->addElement($s, 'username', $server->username);

			// Ad this server to the provider
			$provider->appendChild($s);
		}
	}


	public function getNode($name, $attribute = null, $value = null)
	{
		$n = $this->dom->createElement($name);
		if ($attribute !== null && $value !== null)
			$this->addAttribute($n, $attribute, $value);
		return $n;
	}

	public function addElement(&$node, $name, $value = null)
	{
		$e = $this->dom->createElement($name, $value);
		$node->appendChild($e);
		return $this;
	}

	public function addAttribute(&$node, $name, $value)
	{
		$a = $this->dom->createAttribute($name);
		$a->value = $value;
		$node->appendChild($a);
		return $this;
	}

	public function __toString()
	{
		return $this->dom->saveXML();
	}

	public function render()
	{
		return (string) $this;
	}

}

// Build server info
$s1 = Server::factory(ServerType::IMAP, SocketType::SSL, Authentication::PLAIN, 'imap.omniasoft.nl', '%EMAILADDRESS%');
$s2 = Server::factory(ServerType::SMTP, SocketType::SSL, Authentication::PLAIN, 'smtp.omniasoft.nl', '%EMAILADDRESS%');
$s3 = Server::factory(ServerType::POP, SocketType::SSL, Authentication::PLAIN, 'pop.omniasoft.nl', '%EMAILADDRESS%');

$mail = new Mail;
$mail->name = 'Omniasoft';
$mail->domain = 'omniasoft.nl';
$mail->servers[] = $s1;
$mail->servers[] = $s3;
$mail->servers[] = $s2;

// Create root document
$autoconfig = new AutoConfig();
$autoconfig->translate($mail);

// Output
header("Content-Type: text/xml; charset=utf-8");
die($autoconfig);