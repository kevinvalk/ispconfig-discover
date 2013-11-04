<?php
require('vendor/autoload.php');

// Usages
use Discover\Base\Server,
Discover\Base\ServerType,
Discover\Base\SocketType,
Discover\Base\Authentication,
Discover\Base\MailInformation,
Discover\Autoconfig\Autoconfig,
Discover\Autodiscover\Autodiscover;

// Build our default server config
// TODO: Link this to ISPConfig
$imap = Server::factory(ServerType::IMAP, SocketType::SSL, Authentication::PLAIN, 'imap.omniasoft.nl', '%EMAILADDRESS%');
$smtp = Server::factory(ServerType::SMTP, SocketType::SSL, Authentication::PLAIN, 'smtp.omniasoft.nl', '%EMAILADDRESS%');
$pop = Server::factory(ServerType::POP, SocketType::SSL, Authentication::PLAIN, 'pop.omniasoft.nl', '%EMAILADDRESS%');

$mailInformation = new MailInformation;
$mailInformation->name = 'Omniasoft';
$mailInformation->domain = 'omniasoft.nl';
$mailInformation->servers = [$imap, $smtp, $pop];

// Check what to do
$request = $_SERVER['SCRIPT_URL'];
switch ($request)
{
	case Autoconfig::REQUEST:
		// There email adress
		$email = $_GET['emailaddress'];

		// Create root document
		$autoconfig = new Autoconfig();
		$autoconfig->translate($mailInformation);

		// Output
		header("Content-Type: text/xml; charset=utf-8");
		die($autoconfig);
	break;
	case Autodiscover::REQUEST:
		// There email adress
		//get raw POST data so we can extract the email address
		// TODO: Fancy XML parsing
		$data = file_get_contents("php://input");
		preg_match("/\<EMailAddress\>(.*?)\<\/EMailAddress\>/", $data, $matches);
		$email = $matches[1];

		// Create root document
		$autodiscover = new Autodiscover($email);
		$autodiscover->translate($mailInformation);

		// Output
		header("Content-Type: text/xml");
		die($autodiscover);
	break;
	default:
		die('Wrong...');
	break;
}
