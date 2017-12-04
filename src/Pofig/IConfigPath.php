<?php
namespace Pofig;


interface IConfigPath
{
	/**
	 * @param string $configName
	 * @return string[]|null
	 */
	public function getFilePath(string $configName): ?array;
}