<?php
namespace Pofig;


interface IConfigLoader
{
	public function load(string $path): ?array;
}