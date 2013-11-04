<?php
namespace Discover\Base;

abstract class SocketType
{
	const SSL = 'ssl';
	const STARTTLS = 'starttls';
	const OFF = 'plain';
}