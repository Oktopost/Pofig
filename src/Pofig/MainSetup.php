<?php
namespace Pofig;


use Pofig\Base\ISetup;
use Pofig\Base\IMainSetup;
use Pofig\Base\IGroupSetup;


class MainSetup implements IMainSetup
{
	/** @var GroupSetup[] */
	private $groups = [];
	
	/** @var GroupSetup */
	private $mainGroup;
	
	/** @var IConfigParser[] */
	private $parsers = [];
	
	
	public function __construct()
	{
		$this->mainGroup = new GroupSetup();
	}
	
	
	public function group(string $name): IGroupSetup
	{
		if (!isset($this->groups[$name]))
		{
			$group = new GroupSetup();
			$this->groups[$name] = $group;
			return $group;
		}
		
		return $this->groups[$name];
	}
	
	public function addPathResolver(IConfigPath ...$path): ISetup
	{
		$this->mainGroup->addPathResolver(...$path);
		return $this;
	}
	
	public function addSimplePath(array $types, array $separators = ['.', '/', '\\']): ISetup
	{
		$this->mainGroup->addSimplePath($types, $separators);
		return $this;
	}
	
	public function addLoader(...$loader): ISetup
	{
		$this->mainGroup->addLoader(...$loader);
		return $this;
	}
	
	/**
	 * @param string|array $type
	 * @param IConfigLoader $loader
	 * @return ISetup
	 */
	public function addLoaderForType($type, IConfigLoader $loader): ISetup
	{
		$this->mainGroup->addLoaderForType($type, $loader);
		return $this;
	}
	
	/**
	 * @param string $type
	 * @param IConfigParser $parser
	 * @return IMainSetup|static
	 */
	public function addParser(string $type, IConfigParser $parser): IMainSetup
	{
		$this->parsers[$type] = $parser;
		return $this;
	}
	
	
	public function getMainGroup(): GroupSetup
	{
		return $this->mainGroup;
	}
	
	/**
	 * @return GroupSetup[]
	 */
	public function getGroups(): array
	{
		return $this->groups;
	}
	
	public function getParserFor(string $type): ?IConfigParser
	{
		
	}
}