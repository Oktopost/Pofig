<?php
namespace Pofig\Exceptions;


/**
 * Thrown when a loader can not parse a config file.
 * Deliberately not a PofigException, so callers that fall back to defaults on PofigException do not hide a broken file.
 */
class ConfigFileException extends \UnexpectedValueException
{
	public function __construct(string $path)
	{
		parent::__construct("Config file $path could not be parsed");
	}
}
