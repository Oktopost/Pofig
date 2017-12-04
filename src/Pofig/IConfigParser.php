<?php
namespace Pofig;


interface IConfigParser
{
	/**
	 * @param array $config
	 * @param string $targetType
	 * @return mixed|null
	 */
	public function parse(array $config, string $targetType);
}