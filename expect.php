<?php

use FriendsOfPhpSpec\PhpSpec\Expect\Subject;
use FriendsOfPhpSpec\PhpSpec\Expect\Wrapper;
use PhpSpec\CodeAnalysis\MagicAwareAccessInspector;
use PhpSpec\CodeAnalysis\VisibilityAccessInspector;
use PhpSpec\Console\Assembler\PresenterAssembler;
use PhpSpec\Exception\ExceptionFactory;
use PhpSpec\Factory\ReflectionFactory;
use PhpSpec\Formatter\Presenter\Presenter;
use PhpSpec\Loader\Node\ExampleNode;
use PhpSpec\Matcher;
use PhpSpec\Runner\MatcherManager;
use PhpSpec\ServiceContainer\IndexedServiceContainer;
use PhpSpec\Wrapper\Subject\Caller;
use PhpSpec\Wrapper\Subject\ExpectationFactory;
use PhpSpec\Wrapper\Subject\SubjectWithArrayAccess;
use PhpSpec\Wrapper\Subject\WrappedObject;
use PhpSpec\Wrapper\Unwrapper;
use Symfony\Component\EventDispatcher\EventDispatcher;

// Random string is chosen to avoid conflicts with other global functions
function e710d4e7_friends_of_phpspec_use_expect(): bool
{
    return !(getenv('PHPSPEC_DISABLE_EXPECT') || (defined('PHPSPEC_DISABLE_EXPECT') && PHPSPEC_DISABLE_EXPECT));
}

if (e710d4e7_friends_of_phpspec_use_expect() && !function_exists('expect')) {
    function expect($sus): Subject
    {
        $container = new IndexedServiceContainer();
        (new PresenterAssembler())->assemble($container);
        $container->configure();

        /** @var Presenter $presenter */
        $presenter = $container->get('formatter.presenter');

        $unwrapper = new Unwrapper();
        $dispatcher = new EventDispatcher();
        $accessInspector = new MagicAwareAccessInspector(new VisibilityAccessInspector());
        $reflectionFactory = new ReflectionFactory();
        $exampleNode = new ExampleNode('expect', new \ReflectionFunction(__FUNCTION__));

        $matchers  = new MatcherManager($presenter);
        $matchers->add(new Matcher\IdentityMatcher($presenter));
        $matchers->add(new Matcher\ComparisonMatcher($presenter));
        $matchers->add(new Matcher\ThrowMatcher($unwrapper, $presenter, $reflectionFactory));
        $matchers->add(new Matcher\TypeMatcher($presenter));
        $matchers->add(new Matcher\ObjectStateMatcher($presenter));
        $matchers->add(new Matcher\ScalarMatcher($presenter));
        $matchers->add(new Matcher\ArrayCountMatcher($presenter));
        $matchers->add(new Matcher\ArrayKeyMatcher($presenter));
        $matchers->add(new Matcher\ArrayKeyValueMatcher($presenter));
        $matchers->add(new Matcher\ArrayContainMatcher($presenter));
        $matchers->add(new Matcher\StringStartMatcher($presenter));
        $matchers->add(new Matcher\StringEndMatcher($presenter));
        $matchers->add(new Matcher\StringRegexMatcher($presenter));
        $matchers->add(new Matcher\StringContainMatcher($presenter));
        if (class_exists('PhpSpec\Matcher\TriggerMatcher')) {
            $matchers->add(new Matcher\TriggerMatcher($unwrapper));
        }
        if (class_exists('PhpSpec\Matcher\IterateAsMatcher')) {
            $matchers->add(new Matcher\IterateAsMatcher($presenter));
        }
        if (class_exists('PhpSpec\Matcher\ApproximatelyMatcher')) {
            $matchers->add(new Matcher\ApproximatelyMatcher($presenter));
        }

        $trace = debug_backtrace();
        if (isset($trace[1]['object'])) {
            $object = $trace[1]['object'];

            if ($object instanceof Matcher\MatchersProvider) {
                foreach ($object->getMatchers() as $name => $matcher) {
                    if ($matcher instanceof Matcher) {
                        $matchers->add($matcher);
                    } elseif (is_callable($matcher)) {
                        $matchers->add(new Matcher\CallbackMatcher($name, $matcher, $presenter));
                    } else {
                        throw new \RuntimeException(
                            'Custom matcher has to implement "PhpSpec\Matcher\MatcherInterface" or be a callable'
                        );
                    }
                }
            }
        }

        $exceptionFactory = new ExceptionFactory($presenter);
        $wrapper = new Wrapper($matchers, $presenter, $dispatcher, $exampleNode, $accessInspector);
        $wrappedObject = new WrappedObject($sus, $presenter);
        $caller = new Caller($wrappedObject, $exampleNode, $dispatcher, $exceptionFactory, $wrapper, $accessInspector);
        $arrayAccess = new SubjectWithArrayAccess($caller, $presenter, $dispatcher);
        $expectationFactory = new ExpectationFactory($exampleNode, $dispatcher, $matchers);

        return new Subject($sus, $wrapper, $wrappedObject, $caller, $arrayAccess, $expectationFactory);
    }
}
