<?php

namespace FriendsOfPhpSpec\PhpSpec\Expect;

use PhpSpec\Wrapper\Subject as BaseSubject;

class Subject extends BaseSubject
{
    public function __call(string $method, array $arguments = [])
    {
        if (preg_match('/^(to|notTo)(.+)$/', $method, $matches)) {
            $method = 'should' . $matches[2];

            if ('notTo' === $matches[1]) {
                $method = 'shouldNot' . $matches[2];
            }
        }

        return parent::__call($method, $arguments);
    }
}
