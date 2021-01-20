<?php

include __DIR__ . '/Foo.php';
include __DIR__ . '/FooMatcher.php';

use PhpSpec\Exception\Exception as PhpSpecException;
use PhpSpec\Matcher\MatchersProvider;
use PHPUnit\Framework\TestCase;

class ExpectTest extends TestCase implements MatchersProvider
{
    private $addInvalidMatcher;

    function setUp(): void
    {
        $this->addInvalidMatcher = false;
    }

    /**
     * @test
     * @dataProvider correctExpectations
     */
    function it_does_not_throw_when_expectation_is_met($expectation)
    {
        $expectation();
        $this->addToAssertionCount(1); // No exception thrown
    }

    /**
     * @test
     * @dataProvider incorrectExpectations
     */
    function it_throws_when_expectation_is_not_met($expectation)
    {
        $this->expectException(PhpSpecException::class);
        $expectation();
    }

    /**
     * @test
     */
    function it_throws_when_custom_matcher_does_not_implement_correct_interface()
    {
        $this->expectException(RuntimeException::class);
        $this->addInvalidMatcher = true;
        expect(1)->toBe(1);
    }

    /**
     * @test
     */
    function it_can_be_deactivated()
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
    function correctExpectations()
    {
        return [
            [ function () { expect(5)->toBe(5); } ],
            [ function () { expect(5)->notToBe(1); } ],
            [ function () { expect(5)->toBeLike('5'); } ],
            [ function () { expect((new Foo()))->toHaveType('Foo'); } ],
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
    function incorrectExpectations()
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
            'haveBar' => $this->addInvalidMatcher ? new stdClass() : new FooMatcher(),
        ];
    }
}
