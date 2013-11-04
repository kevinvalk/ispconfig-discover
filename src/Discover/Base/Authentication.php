<?php
namespace Discover\Base;

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