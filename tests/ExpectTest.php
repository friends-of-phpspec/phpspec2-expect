<?php

namespace FriendsOfPhpSpec\PhpSpec\Tests;

use PhpSpec\Exception\Exception as PhpSpecException;
use PhpSpec\Matcher\MatchersProvider;
use PHPUnit\Framework\TestCase;

class ExpectTest extends TestCase implements MatchersProvider
{
    private bool $addInvalidMatcher = false;

    protected function setUp(): void
    {
        $this->addInvalidMatcher = false;
    }

    /**
     * @dataProvider correctExpectations
     */
    public function testItDoesNotThrowWhenExpectationIsMet($expectation): void
    {
        $expectation();
        $this->addToAssertionCount(1); // No exception thrown
    }

    /**
     * @dataProvider incorrectExpectations
     */
    public function testItThrowsWhenExpectationIsNotMet($expectation): void
    {
        $this->expectException(PhpSpecException::class);
        $expectation();
    }

    public function testItThrowsWhenCustomMatcherDoesNotImplementCorrectInterface(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->addInvalidMatcher = true;
        expect(1)->toBe(1);
    }

    public function testItCanBeDeactivated(): void
    {
        // Active by default
        $this->assertTrue(e710d4e7_friends_of_phpspec_use_expect());

        putenv('PHPSPEC_DISABLE_EXPECT=1');
        $this->assertFalse(e710d4e7_friends_of_phpspec_use_expect());

        putenv('PHPSPEC_DISABLE_EXPECT=');
        $this->assertTrue(e710d4e7_friends_of_phpspec_use_expect());

        define('PHPSPEC_DISABLE_EXPECT', true);
        $this->assertFalse(e710d4e7_friends_of_phpspec_use_expect());
    }

    /**
     * Cases that should evaluate without an exception
     */
    public function correctExpectations(): array
    {
        return [
            [ function () { expect(5)->toBe(5); } ],
            [ function () { expect(5)->notToBe(1); } ],
            [ function () { expect(5)->toBeLike('5'); } ],
            [ function () { expect((new Foo()))->toHaveType(Foo::class); } ],
            [ function () { expect((new Foo()))->toHaveCount(1); } ],
            [ function () { expect((new Foo()))->toBeFoo(); } ],
            [ function () { expect((new Foo())->getArray())->toBeArray(); } ],
            [ function () { expect((new Foo())->getString())->toBeString(); } ],
            [ function () { expect(['foo'])->toContain('foo'); } ],
            [ function () { expect(['foo' => 'bar'])->toHaveKey('foo'); } ],
            [ function () { expect(['foo' => 'bar'])->toHaveKeyWithValue('foo','bar'); } ],
            [ function () { expect('foo bar')->toContain('bar'); } ],
            [ function () { expect('foo bar')->toStartWith('foo'); } ],
            [ function () { expect('foo bar')->toEndWith('bar'); } ],
            [ function () { expect('foo bar')->toMatch('/bar/'); } ],
            [ function () { expect((new Foo()))->toThrow('InvalidArgumentException')->duringThrowException(); } ],
            [ function () { expect((new Foo()))->toTrigger(E_USER_DEPRECATED)->duringTriggerError(); } ],
            [ function () { expect(1.444447777)->toBeApproximately(1.444447777, 1.0e-9); } ],
            [ function () { expect((new Foo())->getIterator())->toIterateAs(new \ArrayIterator(['Foo', 'Bar'])); } ],
            // Custom matchers
            [ function () { expect(['foo' => 'bar'])->toHaveKey('foo'); } ],
            [ function () { expect(1)->toHaveFoo(1); } ],
        ];
    }

    /**
     * Cases that should throw an exception when evaluated
     */
    public function incorrectExpectations(): array
    {
        return [
            [ function () { expect(6)->toBe(5); } ],
            [ function () { expect(6)->notToBe(6); } ],
            [ function () { expect(6)->toBeLike('5'); } ],
            [ function () { expect((new Foo()))->toHaveType('Bar'); } ],
            [ function () { expect((new Foo()))->toHaveCount(2); } ],
            [ function () { expect((new Foo()))->toBeBar(); } ],
            [ function () { expect((new Foo())->getString())->toBeArray(); } ],
            [ function () { expect((new Foo())->getArray())->toBeString(); } ],
            [ function () { expect(['foo'])->toContain('bar'); } ],
            [ function () { expect(['foo' => 'bar'])->toHaveKey('bar'); } ],
            [ function () { expect(['foo' => 'bar'])->toHaveKeyWithValue('foo','foo'); } ],
            [ function () { expect('foo bar')->toContain('baz'); } ],
            [ function () { expect('foo bar')->toStartWith('baz'); } ],
            [ function () { expect('foo bar')->toEndWith('baz'); } ],
            [ function () { expect('foo bar')->toMatch('/baz/'); } ],
            [ function () { expect((new Foo()))->toThrow('AnotherException')->duringThrowException(); } ],
            [ function () { expect(1.444447777)->toBeApproximately(1.444447778, 1.0e-9); } ],
            [ function () { expect((new Foo())->getIterator())->toIterateAs(new \ArrayIterator(['Bar', 'Foo'])); } ],
        ];
    }

    /**
     * Provide custom matchers
     *
     * @return array
     */
    public function getMatchers(): array
    {
        return [
            'haveKey' => function ($subject, $key) { return array_key_exists($key, $subject); },
            'haveFoo' => new FooMatcher(),
            'haveBar' => $this->addInvalidMatcher ? new \stdClass() : new FooMatcher(),
        ];
    }
}
