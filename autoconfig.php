<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');

// Create root document
$autoconfig = new DOMDocument('1.0', 'UTF-8');
$autoconfig->formatOutput = true;
$root = $autoconfig->createElement('clientConfig');
$autoconfig->appendChild($root);





$autoconfig = $autoconfig->saveXML();
//die('<pre>'.htmlentities($autoconfig).'</pre>');


$autoconfig = '<?xml version="1.0" encoding="UTF-8"?>

<clientConfig version="1.1">
    <emailProvider id="domain.tld">
        <domain>Omniasoft.nl</domain>
        <displayName>Mail</displayName>
        <displayShortName>Mail</displayShortName>
        <incomingServer type="imap">
            <hostname>mail.omniasoft.nl</hostname>
            <port>993</port>
            <socketType>SSL</socketType>
            <authentication>password-cleartext</authentication>
            <username>%EMAILADDRESS%</username>
        </incomingServer>
        <outgoingServer type="smtp">
            <hostname>mail.omniasoft.nl</hostname>
            <port>465</port>
            <socketType>SSL</socketType>
            <authentication>password-cleartext</authentication>
            <username>%EMAILADDRESS%</username>
        </outgoingServer>
    </emailProvider>
</clientConfig>';


header("Content-Type: text/xml; charset=utf-8");
die($autoconfig);

/*

<?xml version="1.0"?>
<clientConfig version="1.1">
    <emailProvider id="example.com">
      <domain>example.com</domain>
      <domain>example.net</domain>

      <displayName>Google Mail</displayName>
      <displayShortName>GMail</displayShortName>

      <incomingServer type="pop3">
         <hostname>pop.example.com</hostname>
         <port>995</port>
         <socketType>SSL</socketType>
           <!-- "plain": no encryption
                "SSL": SSL 3 or TLS 1 on SSL-specific port
                "STARTTLS": on normal plain port and mandatory upgrade to TLS via STARTTLS
                -->
         <username>%EMAILLOCALPART%</username>
            <!-- "password-cleartext",
                 "plain" (deprecated):
                          Send password in the clear
                          (dangerous, if SSL isn't used either).
                          AUTH PLAIN, LOGIN or protocol-native login.
                 "password-encrypted",
                 "secure" (deprecated):
                           A secure encrypted password mechanism.
                           Can be CRAM-MD5 or DIGEST-MD5. Not NTLM.
                 "NTLM":
                           Use NTLM (or NTLMv2 or successors),
                           the Windows login mechanism.
                 "GSSAPI":
                           Use Kerberos / GSSAPI,
                           a single-signon mechanism used for big sites.
                 "client-IP-address":
                           The server recognizes this user based on the IP address.
                           No authentication needed, the server will require no username nor password.
                 "TLS-client-cert":
                           On the SSL/TLS layer, the server requests a client certificate and the client sends one (possibly after letting the user select/confirm one), if available. (Not yet supported by Thunderbird)
                 "none":
                           No authentication
                  Compatibility note: Thunderbird 3.0 accepts only "plain" and "secure". It will ignore the whole XML file, if other values are given. -->
         <authentication>password-cleartext</authentication>
         <pop3>
            <!-- remove the following and leave to client/user? -->
            <leaveMessagesOnServer>true</leaveMessagesOnServer>
            <downloadOnBiff>true</downloadOnBiff>
            <daysToLeaveMessagesOnServer>14</daysToLeaveMessagesOnServer>
            <!-- only for servers which don't allow checks more often -->
            <checkInterval minutes="15"/><!-- not yet supported -->
         </pop3>
         <password>optional: the user's password</password>
      </incomingServer>

      <outgoingServer type="smtp">
         <hostname>smtp.googlemail.com</hostname>
         <port>587</port>
         <socketType>STARTTLS</socketType> <!-- see above -->
         <username>%EMAILLOCALPART%</username> <!-- if smtp-auth -->
            <!-- smtp-auth (RFC 2554, 4954) or other auth mechanism.
                 For values, see incoming.
                 Additional options here:
                 "SMTP-after-POP":
                     authenticate to incoming mail server first
                     before contacting the smtp server.
                  Compatibility note: Thunderbird 3.0 accepts only "plain",
                  "secure", "none", and "smtp-after-pop".
                  It will ignore the whole XML file, if other values are given.
            -->
         <authentication>password-cleartext</authentication>
            <!-- If the server makes some additional requirements beyond
                 <authentication>.
                 "client-IP-address": The server is only reachable or works,
                     if the user is in a certain IP network, e.g.
                     the dialed into the ISP's network (DSL, cable, modem) or
                     connected to a company network.
                     Note: <authentication>client-IP-address</>
                     means that you may use the server without any auth.
                     <authentication>password-cleartext</> *and*
                     <restriction>client-IP-address</> means that you need to
                     be in the correct IP network *and* (should) authenticate.
                     Servers which do that are highly discouraged and
                     should be avoided, see {{bug|556267}}.
                Not yet implemented. Spec (element name?) up to change.
            -->
         <restriction>client-IP-address</restriction>
         <!-- remove the following and leave to client/user? -->
         <addThisServer>true</addThisServer>
         <useGlobalPreferredServer>true</useGlobalPreferredServer>
         <password>optional: the user's password</password>
      </outgoingServer>

      <identity>
         <!-- needed? -->
         <!-- We don't want Verizon setting "Organization: Verizon"
              for its customers -->
      </identity>

      <!-- see description. Not yet supported, see bug 564043. -->
      <inputField key="USERNAME" label="Screen name"></inputField>
      <inputField key="GRANDMA" label="Grandma">Elise Bauer</inputField>

      <!-- Add this only when users (who already have an account) have to
           do something before the account can work with IMAP/POP or SSL.
           Note: Per XML, & (ampersand) needs to be escaped to & a m p ;
           (without spaces).
           Not yet implemented, see bug 586364. -->
      <enable
           visiturl="https://mail.google.com/mail/?ui=2&shva=1#settings/fwdandpop">
           <instruction>Check 'Enable IMAP and POP' in Google settings page</instruction>
           <instruction lang="de">Schalten Sie 'IMAP und POP aktivieren' auf der Google Einstellungs-Seite an</instruction>
      </enable>

      <!-- A page where the ISP describes the configuration.
           This is purely informational and currently mainly for
           maintenance of the files and not used by the client at all.
           Note that we do not necessarily use exactly the config suggested
           by the ISP, e.g. when they don't recommend SSL, but it's available,
           we will configure SSL.
           The text content should contains a description in the native
           language of the ISP (customers), and a short English description,
           mostly for us.
      -->
      <instruction url="http://www.example.com/help/mail/thunderbird">
         <descr lang="en">Configure Thunderbird 2.0 for IMAP</descr>
         <descr lang="de">Thunderbird 2.0 mit IMAP konfigurieren</descr>
      </instruction>

    </emailProvider>

    <clientConfigUpdate url="https://www.example.com/config/mozilla.xml" />

</clientConfig>