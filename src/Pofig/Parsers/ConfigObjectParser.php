<?php
namespace Pofig\Parsers;


use Pofig\IConfigParser;
use Pofig\Objects\ReferenceConfigObject;


class ConfigObjectParser implements IConfigParser
{
	/**
	 * @param array $config
	 * @param string $targetType
	 * @return mixed|null
	 */
	public function parse(array $config, string $targetType)
	{
		return new ReferenceConfigObject($config);
	}
}