<?php
namespace Pofig\Base;


use Pofig\IConfigParser;


interface IMainSetup extends ISetup 
{
	public function group(string $name): IGroupSetup;
	
	/**
	 * Register a group that serves configs from an in-memory array. It is merged in the
	 * position of its registration, like a file group.
	 * @param string $name
	 * @param array $configs Config name => config array. Names may use '.', '/' or '\' as separators.
	 * @return IMainSetup|static
	 */
	public function arrayGroup(string $name, array $configs): IMainSetup;
	
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