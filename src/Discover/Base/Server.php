<?php
namespace Discover\Base;

use Discover\Base\ServerType;

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