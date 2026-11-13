<?php
namespace Pofig\Setup;


use PHPUnit\Framework\TestCase;
use Pofig\IConfigPath;
use Pofig\IConfigLoader;


class GroupSetupTest extends TestCase
{
	private function resolver(array $paths): IConfigPath
	{
		$resolver = $this->createStub(IConfigPath::class);
		$resolver->method('getFilePath')->willReturn($paths);
		return $resolver;
	}
	
	private function loader(): IConfigLoader
	{
		return $this->createStub(IConfigLoader::class);
	}
	
	
	public function test_sanity_SelfReturned()
	{
		$subject = new GroupSetup();
		
		self::assertSame($subject, $subject->addPathResolver());
		self::assertSame($subject, $subject->addSimplePath(['ini']));
		self::assertSame($subject, $subject->addLoader());
		self::assertSame($subject, $subject->addLoaderForType('ini', $this->loader()));
		self::assertSame($subject, $subject->addIncludePath());
	}
	
	
	public function test_getPathFor_NoResolvers_EmptyArrayReturned()
	{
		self::assertSame([], (new GroupSetup())->getPathFor('a'));
	}
	
	public function test_addPathResolver_ResolversAddedInOrder()
	{
		$subject = new GroupSetup();
		$subject->addPathResolver($this->resolver(['a']), $this->resolver(['b', 'c']));
		$subject->addPathResolver($this->resolver(['d']));
		
		self::assertSame(['a', 'b', 'c', 'd'], $subject->getPathFor('x'));
	}
	
	public function test_addSimplePath_SimplePathAdded()
	{
		$subject = new GroupSetup();
		$subject->addSimplePath(['ini', 'json'], ['-']);
		
		self::assertSame(
			['a' . DIRECTORY_SEPARATOR . 'b.ini', 'a' . DIRECTORY_SEPARATOR . 'b.json'], 
			$subject->getPathFor('a-b'));
	}
	
	
	public function test_getLoadersFor_NoLoaders_EmptyArrayReturned()
	{
		self::assertSame([], (new GroupSetup())->getLoadersFor('ini'));
	}
	
	public function test_addLoader_SingleLoader_AddedAsGenericLoader()
	{
		$loader = $this->loader();
		$subject = new GroupSetup();
		$subject->addLoader($loader);
		
		self::assertSame([$loader], $subject->getLoadersFor('ini'));
		self::assertSame([$loader], $subject->getLoadersFor('json'));
	}
	
	public function test_addLoader_MultipleSingleLoaders_AllAddedInOrder()
	{
		$a = $this->loader();
		$b = $this->loader();
		$subject = new GroupSetup();
		$subject->addLoader($a, $b);
		
		self::assertSame([$a, $b], $subject->getLoadersFor('ini'));
	}
	
	public function test_addLoader_List_AddedAsGenericLoaders()
	{
		$a = $this->loader();
		$b = $this->loader();
		$c = $this->loader();
		$subject = new GroupSetup();
		$subject->addLoader([$a, $b]);
		$subject->addLoader([$c]);
		
		self::assertSame([$a, $b, $c], $subject->getLoadersFor('ini'));
	}
	
	public function test_addLoader_MapOfSingleLoader_AddedForType()
	{
		$loader = $this->loader();
		$subject = new GroupSetup();
		$subject->addLoader(['ini' => $loader]);
		
		self::assertSame([$loader], $subject->getLoadersFor('ini'));
		self::assertSame([], $subject->getLoadersFor('json'));
	}
	
	public function test_addLoader_MapOfLoaderLists_AddedForType()
	{
		$a = $this->loader();
		$b = $this->loader();
		$subject = new GroupSetup();
		$subject->addLoader(['ini' => [$a, $b]]);
		
		self::assertSame([$a, $b], $subject->getLoadersFor('ini'));
	}
	
	public function test_addLoader_MapThenAddLoaderForType_BothKeptInOrder()
	{
		$a = $this->loader();
		$b = $this->loader();
		$subject = new GroupSetup();
		$subject->addLoader(['ini' => $a]);
		$subject->addLoaderForType('ini', $b);
		
		self::assertSame([$a, $b], $subject->getLoadersFor('ini'));
	}
	
	public function test_addLoaderForType_ThenMap_BothKeptInOrder()
	{
		$a = $this->loader();
		$b = $this->loader();
		$subject = new GroupSetup();
		$subject->addLoaderForType('ini', $a);
		$subject->addLoader(['ini' => $b]);
		
		self::assertSame([$a, $b], $subject->getLoadersFor('ini'));
	}
	
	public function test_addLoaderForType_ThenMapOfList_AllKeptInOrder()
	{
		$a = $this->loader();
		$b = $this->loader();
		$c = $this->loader();
		$subject = new GroupSetup();
		$subject->addLoaderForType('ini', $a);
		$subject->addLoader(['ini' => [$b, $c]]);
		
		self::assertSame([$a, $b, $c], $subject->getLoadersFor('ini'));
	}
	
	public function test_addLoaderForType_LoadersAddedInOrder()
	{
		$a = $this->loader();
		$b = $this->loader();
		$subject = new GroupSetup();
		$subject->addLoaderForType('ini', $a);
		$subject->addLoaderForType('ini', $b);
		
		self::assertSame([$a, $b], $subject->getLoadersFor('ini'));
		self::assertSame([], $subject->getLoadersFor('json'));
	}
	
	public function test_getLoadersFor_TypedAndGenericLoaders_TypedLoadersFirst()
	{
		$typed = $this->loader();
		$generic = $this->loader();
		$subject = new GroupSetup();
		$subject->addLoader($generic);
		$subject->addLoaderForType('ini', $typed);
		
		self::assertSame([$typed, $generic], $subject->getLoadersFor('ini'));
		self::assertSame([$generic], $subject->getLoadersFor('json'));
	}
	
	
	public function test_getIncludePath_NoPaths_EmptyArrayReturned()
	{
		self::assertSame([], (new GroupSetup())->getIncludePath());
	}
	
	public function test_addIncludePath_PathsAddedInOrder()
	{
		$subject = new GroupSetup();
		$subject->addIncludePath('a', 'b');
		$subject->addIncludePath('c');
		
		self::assertSame(['a', 'b', 'c'], $subject->getIncludePath());
	}
	
	public function test_addIncludePath_TrailingSeparator_Removed()
	{
		$subject = new GroupSetup();
		$subject->addIncludePath('a' . DIRECTORY_SEPARATOR);
		
		self::assertSame(['a'], $subject->getIncludePath());
	}
	
	public function test_addIncludePath_EmptyPath_StoredAsIs()
	{
		$subject = new GroupSetup();
		$subject->addIncludePath('');
		
		self::assertSame([''], $subject->getIncludePath());
	}
}
