<?php

namespace Roave\BetterReflectionTest\Reflector;

use Roave\BetterReflection\Identifier\IdentifierType;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflection\ReflectionFunction;
use Roave\BetterReflection\Reflector\Reflector;
use Roave\BetterReflection\SourceLocator\Ast\FindReflectionsInTree;
use Roave\BetterReflection\SourceLocator\Ast\Strategy\NodeToReflection;
use Roave\BetterReflection\SourceLocator\Located\LocatedSource;
use PhpParser\Parser;
use PhpParser\Lexer;

/**
 * @covers \Roave\BetterReflection\SourceLocator\Ast\FindReflectionsInTree
 */
class FindReflectionsInTreeTest extends \PHPUnit_Framework_TestCase
{
    private function getAstForString($php) : array
    {
        return (new Parser\Multiple([
            new Parser\Php7(new Lexer()),
            new Parser\Php5(new Lexer()),
        ]))->parse($php);
    }

    public function testInvokeDoesNotCallReflectNodesWhenNoNodesFoundInEmptyAst() : void
    {
        /** @var NodeToReflection|\PHPUnit_Framework_MockObject_MockObject $strategy */
        $strategy = $this->createMock(NodeToReflection::class);

        $strategy->expects($this->never())
            ->method('__invoke');

        /** @var Reflector|\PHPUnit_Framework_MockObject_MockObject $reflector */
        $reflector = $this->createMock(Reflector::class);
        $locatedSource = new LocatedSource('<?php', null);

        self::assertSame(
            [],
            (new FindReflectionsInTree($strategy))->__invoke(
                $reflector,
                $this->getAstForString($locatedSource->getSource()),
                new IdentifierType(IdentifierType::IDENTIFIER_CLASS),
                $locatedSource
            )
        );
    }

    public function testInvokeDoesNotCallReflectNodesWhenNoNodesFoundInPopulatedAst() : void
    {
        /** @var NodeToReflection|\PHPUnit_Framework_MockObject_MockObject $strategy */
        $strategy = $this->createMock(NodeToReflection::class);

        $strategy->expects($this->never())
            ->method('__invoke');

        /** @var Reflector|\PHPUnit_Framework_MockObject_MockObject $reflector */
        $reflector = $this->createMock(Reflector::class);
        $locatedSource = new LocatedSource('<?php echo "Hello world";', null);

        self::assertSame(
            [],
            (new FindReflectionsInTree($strategy))->__invoke(
                $reflector,
                $this->getAstForString($locatedSource->getSource()),
                new IdentifierType(IdentifierType::IDENTIFIER_CLASS),
                $locatedSource
            )
        );
    }

    public function testInvokeCallsReflectNodesForClassWithoutNamespace() : void
    {
        /** @var NodeToReflection|\PHPUnit_Framework_MockObject_MockObject $strategy */
        $strategy = $this->createMock(NodeToReflection::class);

        $mockReflection = $this->createMock(ReflectionClass::class);

        $strategy->expects($this->once())
            ->method('__invoke')
            ->will($this->returnValue($mockReflection));

        /** @var Reflector|\PHPUnit_Framework_MockObject_MockObject $reflector */
        $reflector = $this->createMock(Reflector::class);
        $locatedSource = new LocatedSource('<?php class Foo {}', null);

        self::assertSame(
            [
                $mockReflection,
            ],
            (new FindReflectionsInTree($strategy))->__invoke(
                $reflector,
                $this->getAstForString($locatedSource->getSource()),
                new IdentifierType(IdentifierType::IDENTIFIER_CLASS),
                $locatedSource
            )
        );
    }

    public function testInvokeCallsReflectNodesForNamespacedClass() : void
    {
        /** @var NodeToReflection|\PHPUnit_Framework_MockObject_MockObject $strategy */
        $strategy = $this->createMock(NodeToReflection::class);

        $mockReflection = $this->createMock(ReflectionClass::class);

        $strategy->expects($this->once())
            ->method('__invoke')
            ->will($this->returnValue($mockReflection));

        /** @var Reflector|\PHPUnit_Framework_MockObject_MockObject $reflector */
        $reflector = $this->createMock(Reflector::class);
        $locatedSource = new LocatedSource('<?php namespace Foo { class Bar {} }', null);

        self::assertSame(
            [
                $mockReflection,
            ],
            (new FindReflectionsInTree($strategy))->__invoke(
                $reflector,
                $this->getAstForString($locatedSource->getSource()),
                new IdentifierType(IdentifierType::IDENTIFIER_CLASS),
                $locatedSource
            )
        );
    }

    public function testInvokeCallsReflectNodesForFunction() : void
    {
        /** @var NodeToReflection|\PHPUnit_Framework_MockObject_MockObject $strategy */
        $strategy = $this->createMock(NodeToReflection::class);

        $mockReflection = $this->createMock(ReflectionFunction::class);

        $strategy->expects($this->once())
            ->method('__invoke')
            ->will($this->returnValue($mockReflection));

        /** @var Reflector|\PHPUnit_Framework_MockObject_MockObject $reflector */
        $reflector = $this->createMock(Reflector::class);
        $locatedSource = new LocatedSource('<?php function foo() {}', null);

        self::assertSame(
            [
                $mockReflection,
            ],
            (new FindReflectionsInTree($strategy))->__invoke(
                $reflector,
                $this->getAstForString($locatedSource->getSource()),
                new IdentifierType(IdentifierType::IDENTIFIER_FUNCTION),
                $locatedSource
            )
        );
    }
}
