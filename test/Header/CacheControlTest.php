<?php

namespace MichehTest\Cache\Header;

/**
 * @author Michel Hunziker <php@michelhunziker.com>
 * @copyright Copyright (c) 2015, Michel Hunziker <php@michelhunziker.com>
 * @license http://www.opensource.org/licenses/BSD-3-Clause The BSD-3-Clause License
 */
class CacheControlTest extends CacheControlTestCase
{
    /**
     * @var CacheControlStub
     */
    protected $cacheControl;

    /**
     * @var string
     */
    protected $controlClass = 'MichehTest\Cache\Header\CacheControlStub';

    /**
     * @covers Micheh\Cache\Header\CacheControl::withFlag
     */
    public function testWithFlag()
    {
        $clone = $this->cacheControl->withFlag('foo', true);
        $this->assertInstanceOf($this->controlClass, $clone);

        $this->assertAttributeSame([], 'directives', $this->cacheControl);
        $this->assertAttributeSame(['foo' => true], 'directives', $clone);
    }

    /**
     * @covers Micheh\Cache\Header\CacheControl::withFlag
     */
    public function testWithFlagAndFalse()
    {
        $clone = $this->cacheControl->withFlag('foo', false);
        $this->assertAttributeSame([], 'directives', $clone);
    }

    /**
     * @covers Micheh\Cache\Header\CacheControl::withFlag
     */
    public function testWithFlagRemovesFlag()
    {
        $clone = $this->cacheControl->withFlag('foo', true)->withFlag('foo', false);
        $this->assertAttributeSame([], 'directives', $clone);
    }

    /**
     * @covers Micheh\Cache\Header\CacheControl::hasFlag
     */
    public function testHasFlag()
    {
        $clone = $this->cacheControl->withFlag('foo', true);
        $this->assertTrue($clone->hasFlag('foo'));
    }

    /**
     * @covers Micheh\Cache\Header\CacheControl::hasFlag
     */
    public function testHasFlagWithoutValue()
    {
        $this->assertFalse($this->cacheControl->hasFlag('foo'));
    }

    /**
     * @covers Micheh\Cache\Header\CacheControl::withDirective
     */
    public function testWithDirective()
    {
        $clone = $this->cacheControl->withDirective('foo', 'bar');
        $this->assertInstanceOf($this->controlClass, $clone);

        $this->assertAttributeSame([], 'directives', $this->cacheControl);
        $this->assertAttributeSame(['foo' => 'bar'], 'directives', $clone);
    }

    /**
     * @covers Micheh\Cache\Header\CacheControl::withDirective
     */
    public function testWithDirectiveWithNegativeInt()
    {
        $clone = $this->cacheControl->withDirective('foo', -200);
        $this->assertAttributeSame(['foo' => 0], 'directives', $clone);
    }

    /**
     * @covers Micheh\Cache\Header\CacheControl::withDirective
     */
    public function testWithDirectiveWithNull()
    {
        $clone = $this->cacheControl->withDirective('foo', 'bar')->withDirective('foo', null);
        $this->assertAttributeSame([], 'directives', $clone);
    }

    /**
     * @covers Micheh\Cache\Header\CacheControl::getDirective
     */
    public function testGetDirective()
    {
        $clone = $this->cacheControl->withDirective('foo', 'bar');
        $this->assertSame('bar', $clone->getDirective('foo'));
    }

    /**
     * @covers Micheh\Cache\Header\CacheControl::getDirective
     */
    public function testGetDirectiveWithoutValue()
    {
        $this->assertNull($this->cacheControl->getDirective('foo'));
    }

    /**
     * @covers Micheh\Cache\Header\CacheControl::fromString
     * @covers Micheh\Cache\Header\CacheControl::getMethod
     */
    public function testFromStringWithFlag()
    {
        $control = CacheControlStub::fromString('no-transform');
        $this->assertAttributeSame(['no-transform' => true], 'directives', $control);
    }

    /**
     * @covers Micheh\Cache\Header\CacheControl::fromString
     * @covers Micheh\Cache\Header\CacheControl::getMethod
     */
    public function testFromStringWithToken()
    {
        $control = CacheControlStub::fromString('max-age=60');
        $this->assertAttributeSame(['max-age' => 60], 'directives', $control);
    }

    /**
     * @covers Micheh\Cache\Header\CacheControl::fromString
     * @covers Micheh\Cache\Header\CacheControl::getMethod
     */
    public function testFromStringWithMultiple()
    {
        $control = CacheControlStub::fromString('no-transform, max-age=100');
        $this->assertAttributeSame(['no-transform' => true, 'max-age' => 100], 'directives', $control);
    }

    /**
     * @covers Micheh\Cache\Header\CacheControl::fromString
     * @covers Micheh\Cache\Header\CacheControl::getMethod
     */
    public function testFromStringWithOverrideMethod()
    {
        $this->assertSame('123', CacheControlStub::fromString('custom=123'));
    }

    /**
     * @covers Micheh\Cache\Header\CacheControl::fromString
     * @covers Micheh\Cache\Header\CacheControl::getMethod
     */
    public function testFromStringWithUnknownDirective()
    {
        $control = CacheControlStub::fromString('foo="bar"');
        $this->assertAttributeSame(['foo' => 'bar'], 'directives', $control);
    }

    /**
     * @covers Micheh\Cache\Header\CacheControl::fromString
     * @covers Micheh\Cache\Header\CacheControl::getMethod
     */
    public function testFromStringWithUnknownDirectiveFlag()
    {
        $control = CacheControlStub::fromString('foo');
        $this->assertAttributeSame([], 'directives', $control);
    }

    /**
     * @covers Micheh\Cache\Header\CacheControl::withMaxAge
     */
    public function testWithMaxAge()
    {
        $control = $this->getControlWithDirective('max-age', 5);
        $this->assertReturn($control->withMaxAge('5'));
    }

    /**
     * @covers Micheh\Cache\Header\CacheControl::getMaxAge
     */
    public function testGetMaxAge()
    {
        $control = $this->getControlWithGetDirective('max-age');
        $this->assertReturn($control->getMaxAge());
    }

    /**
     * @covers Micheh\Cache\Header\CacheControl::withNoCache
     */
    public function testWithNoCache()
    {
        $control = $this->getControlWithFlag('no-cache', true);
        $this->assertReturn($control->withNoCache(true));
    }

    /**
     * @covers Micheh\Cache\Header\CacheControl::hasNoCache
     */
    public function testHasNoCache()
    {
        $control = $this->getControlWithHasFlag('no-cache');
        $this->assertReturn($control->hasNoCache());
    }

    /**
     * @covers Micheh\Cache\Header\CacheControl::withNoStore
     */
    public function testWithNoStore()
    {
        $control = $this->getControlWithFlag('no-store', true);
        $this->assertReturn($control->withNoStore(true));
    }

    /**
     * @covers Micheh\Cache\Header\CacheControl::hasNoStore
     */
    public function testHasNoStore()
    {
        $control = $this->getControlWithHasFlag('no-store');
        $this->assertReturn($control->hasNoStore());
    }

    /**
     * @covers Micheh\Cache\Header\CacheControl::withNoTransform
     */
    public function testWithNoTransform()
    {
        $control = $this->getControlWithFlag('no-transform', true);
        $this->assertReturn($control->withNoTransform(true));
    }

    /**
     * @covers Micheh\Cache\Header\CacheControl::hasNoTransform
     */
    public function testHasNoTransform()
    {
        $control = $this->getControlWithHasFlag('no-transform');
        $this->assertReturn($control->hasNoTransform());
    }

    /**
     * @covers Micheh\Cache\Header\CacheControl::withExtension
     */
    public function testWithExtension()
    {
        $control = $this->getControlWithDirective('foo', 'bar');
        $this->assertReturn($control->withExtension('foo', '"bar"'));
    }

    /**
     * @covers Micheh\Cache\Header\CacheControl::withExtension
     */
    public function testWithExtensionInvalidType()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'Name and value of the extension have to be a string.'
        );
        $this->cacheControl->withExtension('foo', true);
    }

    /**
     * @covers Micheh\Cache\Header\CacheControl::getExtension
     */
    public function testGetExtension()
    {
        $control = $this->getControlWithGetDirective('foo');
        $this->assertReturn($control->getExtension('foo'));
    }

    /**
     * @covers Micheh\Cache\Header\CacheControl::__toString
     */
    public function testToStringWithFlag()
    {
        $clone = $this->cacheControl->withFlag('foo', true);
        $this->assertSame('foo', (string) $clone);
    }

    /**
     * @covers Micheh\Cache\Header\CacheControl::__toString
     */
    public function testToStringWithToken()
    {
        $clone = $this->cacheControl->withDirective('foo', 30);
        $this->assertSame('foo=30', (string) $clone);
    }

    /**
     * @covers Micheh\Cache\Header\CacheControl::__toString
     */
    public function testToStringWithExtension()
    {
        $clone = $this->cacheControl->withDirective('foo', 'bar');
        $this->assertSame('foo="bar"', (string) $clone);
    }

    /**
     * @covers Micheh\Cache\Header\CacheControl::__toString
     */
    public function testToStringWithMultiple()
    {
        $clone = $this->cacheControl->withFlag('public', true)->withDirective('foo', 20);
        $this->assertSame('public, foo=20', (string) $clone);
    }

    /**
     * @covers Micheh\Cache\Header\CacheControl::__toString
     */
    public function testToStringWithEmpty()
    {
        $this->assertSame('', (string) $this->cacheControl);
    }
}
