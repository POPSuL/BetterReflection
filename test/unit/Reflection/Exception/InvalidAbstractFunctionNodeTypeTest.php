<?php

namespace Roave\BetterReflectionTest\Reflection\Exception;

use Roave\BetterReflection\Reflection\Exception\InvalidAbstractFunctionNodeType;
use Roave\BetterReflection\Reflection\ReflectionFunctionAbstract;
use PhpParser\Node;
use PHPUnit_Framework_TestCase;

/**
 * @covers \Roave\BetterReflection\Reflection\Exception\InvalidAbstractFunctionNodeType
 */
class InvalidAbstractFunctionNodeTypeTest extends PHPUnit_Framework_TestCase
{
    public function testFromNode() : void
    {
        $node = new Node\Scalar\LNumber(5);
        $exception = InvalidAbstractFunctionNodeType::fromNode($node);

        self::assertInstanceOf(InvalidAbstractFunctionNodeType::class, $exception);
        self::assertSame(sprintf(
            'Node for "%s" must be "%s" or "%s", was a "%s"',
            ReflectionFunctionAbstract::class,
            Node\Stmt\ClassMethod::class,
            Node\FunctionLike::class,
            Node\Scalar\LNumber::class
        ), $exception->getMessage());
    }
}
