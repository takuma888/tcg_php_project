<?php
/**
 * Created by PhpStorm.
 * User: tachigo
 * Date: 2018/6/2
 * Time: 下午3:52
 */

namespace TCG\Middleware;

use Middlewares\Utils\CallableHandler;
use Psr\Http\Server\MiddlewareInterface;
use RuntimeException;
use Middlewares\Utils\RequestHandler;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Closure;
use UnexpectedValueException;

class Dispatcher
{
    /**
     * @var MiddlewareInterface[]
     */
    private $middlewareStack = [];

    /**
     * Middleware stack lock
     *
     * @var bool
     */
    protected $middlewareLock = false;

    /**
     * @param $middleware
     * @return mixed
     */
    public function add($middleware)
    {
        if ($this->middlewareLock) {
            throw new RuntimeException('Middleware can’t be added once the stack is dequeuing');
        }
        $this->middlewareStack[] = $middleware;
        return $middleware;
    }

    /**
     * Dispatches the middleware stack and returns the resulting `ResponseInterface`.
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function dispatch(ServerRequestInterface $request): ResponseInterface
    {
        $this->middlewareLock = true;
        $resolved = $this->resolve(0);
        $response = $resolved->handle($request);
        $this->middlewareLock = false;
        return $response;
    }

    /**
     * @param int $index middleware stack index
     *
     * @return RequestHandlerInterface
     */
    private function resolve(int $index): RequestHandlerInterface
    {
        return new RequestHandler(function (ServerRequestInterface $request) use ($index) {
            $middleware = isset($this->middlewareStack[$index]) ? $this->middlewareStack[$index] : new CallableHandler(function () {
            });

            if ($middleware instanceof Closure) {
                $middleware = new CallableHandler($middleware);
            }

            if (!($middleware instanceof MiddlewareInterface)) {
                throw new UnexpectedValueException(
                    sprintf('The middleware must be an instance of %s', MiddlewareInterface::class)
                );
            }

            return $middleware->process($request, $this->resolve($index + 1));
        });
    }
}