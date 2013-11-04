<?php
namespace Discover\Autoconfig;

use Discover\Base\Server,
Discover\Base\ServerType,
Discover\Base\SocketType,
Discover\Base\Authentication,
Discover\Base\MailInformation;

class Autoconfig
{
	const REQUEST = '/mail/config-v1.1.xml';
	private $dom;

	public function __construct()
	{
		$this->dom = new \DOMDocument('1.0', 'UTF-8');
		$this->dom->formatOutput = true;
	}

	public function translate(MailInformation $mail)
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