<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace HyperfTest\Cases;

use Hyperf\Engine\Contract\CoroutineInterface;
use Hyperf\Engine\Coroutine;

/**
 * @internal
 * @coversNothing
 */
class CoroutineTest extends AbstractTestCase
{
    public function testCoroutineCreate()
    {
        $this->runInCoroutine(function () {
            $coroutine = new Coroutine(function () {
                $this->assertTrue(true);
            });

            $coroutine->execute();

            $this->assertInstanceOf(CoroutineInterface::class, $coroutine);
            $this->assertIsInt($coroutine->getId());
        });

        $this->runInCoroutine(function () {
            $coroutine = Coroutine::create(function () {
                $this->assertTrue(true);
            });

            $this->assertInstanceOf(CoroutineInterface::class, $coroutine);
            $this->assertIsInt($coroutine->getId());
        });
    }

    public function testCoroutineContext()
    {
        $this->runInCoroutine(function () {
            $id = uniqid();
            $coroutine = Coroutine::create(function () use ($id) {
                $this->assertInstanceOf(\ArrayObject::class, Coroutine::getContextFor());
                $this->assertFalse(isset(Coroutine::getContextFor()['name']));
                $this->assertSame(null, Coroutine::getContextFor()['name'] ?? null);
                Coroutine::getContextFor()['name'] = $id;
                $this->assertSame($id, Coroutine::getContextFor()['name']);
                usleep(1000);
            });

            $this->assertSame($id, Coroutine::getContextFor($coroutine->getId())['name']);

            usleep(1000);
            $this->assertNull(Coroutine::getContextFor($coroutine->getId()));
        });
    }
}
