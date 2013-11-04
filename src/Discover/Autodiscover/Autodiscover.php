<?php
namespace Discover\Autodiscover;

use Discover\Base\Server,
Discover\Base\ServerType,
Discover\Base\SocketType,
Discover\Base\Authentication,
Discover\Base\MailInformation;

class Autodiscover
{
	const NS_AUTODISCOVER = 'http://schemas.microsoft.com/exchange/autodiscover/responseschema/2006';
	const NS_AUTODISCOVER_OUTLOOK = 'http://schemas.microsoft.com/exchange/autodiscover/outlook/responseschema/2006a';
	const REQUEST = '/AutoDiscover/AutoDiscover.xml';
	private $dom;
	private $email;

	public function __construct($email)
	{
		$this->dom = new \DOMDocument('1.0', 'UTF-8');
		$this->dom->formatOutput = true;
		$this->email = $email;
	}

	public function translate(MailInformation $mail)
	{
		$root = $this->dom->createElementNS(self::NS_AUTODISCOVER, 'Autodiscover');
		$this->dom->appendChild($root);
		$response = $this->dom->createElementNS(self::NS_AUTODISCOVER_OUTLOOK, 'Response');
		$root->appendChild($response);

		$provider = $this->getNode('Account');
		$response->appendChild($provider);

		// Set up default email provider
		$this->addElement($provider, 'AccountType', 'email');
		$this->addElement($provider, 'Action', 'settings');

		// Build servers
		foreach ($mail->servers as &$server)
		{
			// Make the server
			$s = $this->getNode('Protocol');


			$this->addElement($s, 'Type', strtoupper($server->type));
			$this->addElement($s, 'Server', $server->hostname);
			$this->addElement($s, 'Port', $server->port);
			$this->addElement($s, 'LoginName', $this->email);
			$this->addElement($s, 'DomainRequired', 'off');
			$this->addElement($s, 'SPA', 'off');
			$this->addElement($s, 'SSL', (stripos($server->ssl, SocketType::SSL) !== false || $server->ssl === true) ? 'on' : 'off');
			if ($server->type == ServerType::SMTP)
				$this->addElement($s, 'UsePOPAuth', 'on');	
			$this->addElement($s, 'AuthRequired', 'on');

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