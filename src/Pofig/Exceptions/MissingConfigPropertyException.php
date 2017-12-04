<?php
namespace Pofig\Exceptions;


class MissingConfigPropertyException extends PofigException 
{
	private $name;
	
	
	public function __construct(string $name)
	{
		parent::__construct("Property $name not found in the configuration data set", 1);
	}
	
	
	public function getMissingPropertyName(): string
	{
		return $this->name;
	}
}