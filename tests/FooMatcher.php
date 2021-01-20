<?php

use PhpSpec\Exception\Example\FailureException;
use PhpSpec\Matcher\BasicMatcher;

/**
 * Test fixture used in ExpectTest
 */
class FooMatcher extends BasicMatcher
{
    public function supports(string $name, $subject, array $arguments): bool
    {
        return 'haveFoo' === $name;
    }

    protected function matches($subject, array $arguments): bool
    {
        return $subject === $arguments[0];
    }

    protected function getFailureException(string $name, $subject, array $arguments): FailureException
    {
        return new FailureException();
    }

    protected function getNegativeFailureException(string $name, $subject, array $arguments): FailureException
    {
        return new FailureException();
    }
}
