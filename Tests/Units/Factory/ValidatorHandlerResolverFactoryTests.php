<?php

/**
 * This file is part of the Cubiche/Cqrs component.
 *
 * Copyright (c) Cubiche
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cubiche\Infrastructure\Cqrs\Tests\Units\Factory;

use Cubiche\Core\Bus\Exception\NotFoundException;
use Cubiche\Core\Bus\Middlewares\Handler\Locator\InMemoryLocator;
use Cubiche\Core\Bus\Middlewares\Handler\Resolver\HandlerClass\HandlerClassResolver;
use Cubiche\Core\Cqrs\Tests\Fixtures\Command\LoginUserCommand;
use Cubiche\Core\Delegate\Delegate;
use Cubiche\Infrastructure\Cqrs\Factory\ValidatorHandlerResolverFactory;
use Cubiche\Infrastructure\Cqrs\Tests\Fixtures\Command\CreateUserCommand;
use Cubiche\Infrastructure\Cqrs\Tests\Fixtures\Query\FindOneUserByIdQuery;
use Cubiche\Infrastructure\Cqrs\Tests\Fixtures\UserValidatorHandler;
use Cubiche\Infrastructure\Cqrs\Tests\Units\TestCase;

/**
 * ValidatorHandlerResolverFactoryTests class.
 *
 * Generated by TestGenerator on 2017-05-03 at 11:41:18.
 */
class ValidatorHandlerResolverFactoryTests extends TestCase
{
    /**
     * {@inheritdoc}
     */
    protected function createFactory()
    {
        return new ValidatorHandlerResolverFactory(new InMemoryLocator([]));
    }

    /**
     * Test Create method.
     */
    public function testCreate()
    {
        $this
            ->given($factory = $this->createFactory())
            ->then()
                ->object($factory->create())
                    ->isInstanceOf(HandlerClassResolver::class)
        ;
    }

    /**
     * Test Resolve method.
     */
    public function testResolve()
    {
        $this
            ->given($resolver = $this->createFactory()->create())
            ->and($validator = new UserValidatorHandler())
            ->and($resolver->addHandler(CreateUserCommand::class, $validator))
            ->and($resolver->addHandler(FindOneUserByIdQuery::class, $validator))
            ->when($result = $resolver->resolve(new CreateUserCommand('ivan', 'pass', 'ivan@cubiche.com')))
            ->then()
                ->object($result)
                    ->isInstanceOf(Delegate::class)
                ->and()
                ->when($result = $resolver->resolve(new FindOneUserByIdQuery('3dbb0644-70c7-42b2-bb55-21bba4f6e221')))
                ->then()
                    ->object($result)
                        ->isInstanceOf(Delegate::class)
        ;

        $this
            ->given($resolver = $this->createFactory()->create())
            ->and($resolver->addHandler(CreateUserCommand::class, new UserValidatorHandler()))
            ->then()
                ->exception(function () use ($resolver) {
                    $resolver->resolve(new LoginUserCommand('ivan@cubiche.com', 'pass'));
                })
                ->isInstanceOf(NotFoundException::class)
        ;
    }
}
