<?php
namespace Pofig\Base;


use Pofig\IConfigParser;


interface IMainSetup extends ISetup 
{
	public function group(string $name): IGroupSetup;
	
	/**
	 * @param string $type
	 * @param IConfigParser $parser
	 * @return IMainSetup|static
	 */
	public function addParser(string $type, IConfigParser $parser): IMainSetup;
	
	/**
	 * @param string[] ...$types
	 * @return IMainSetup|static
	 */
	public function addStandardLoaders(string ...$types): IMainSetup;
}