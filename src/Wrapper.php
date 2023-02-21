<?php

namespace FriendsOfPhpSpec\PhpSpec\Expect;

use PhpSpec\CodeAnalysis\AccessInspector;
use PhpSpec\Exception\ExceptionFactory;
use PhpSpec\Formatter\Presenter\Presenter;
use PhpSpec\Loader\Node\ExampleNode;
use PhpSpec\Runner\MatcherManager;
use PhpSpec\Wrapper\Subject as BaseSubject;
use PhpSpec\Wrapper\Subject\Caller;
use PhpSpec\Wrapper\Subject\ExpectationFactory;
use PhpSpec\Wrapper\Subject\SubjectWithArrayAccess;
use PhpSpec\Wrapper\Subject\WrappedObject;
use PhpSpec\Wrapper\Wrapper as BaseWrapper;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Wrapper extends BaseWrapper
{
    private MatcherManager $matchers;
    private Presenter $presenter;
    private EventDispatcherInterface $dispatcher;
    private ExampleNode $example;
    private AccessInspector $accessInspector;

    public function __construct(
        MatcherManager $matchers,
        Presenter $presenter,
        EventDispatcherInterface $dispatcher,
        ExampleNode $example,
        AccessInspector $accessInspector
    ) {
        $this->matchers = $matchers;
        $this->presenter = $presenter;
        $this->dispatcher = $dispatcher;
        $this->example = $example;
        $this->accessInspector = $accessInspector;
    }

    public function wrap($value = null): BaseSubject
    {
        $exceptionFactory = new ExceptionFactory($this->presenter);
        $wrappedObject = new WrappedObject($value, $this->presenter);
        $caller = new Caller(
            $wrappedObject,
            $this->example,
            $this->dispatcher,
            $exceptionFactory,
            $this,
            $this->accessInspector
        );
        $arrayAccess = new SubjectWithArrayAccess($caller, $this->presenter, $this->dispatcher);
        $expectationFactory = new ExpectationFactory($this->example, $this->dispatcher, $this->matchers);

        return new Subject($value, $this, $wrappedObject, $caller, $arrayAccess, $expectationFactory);
    }
}
