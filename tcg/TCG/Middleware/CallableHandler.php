<?php
/**
 * Created by PhpStorm.
 * User: tachigo
 * Date: 2018/6/2
 * Time: 下午4:44
 */

namespace TCG\Middleware;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use UnexpectedValueException;

class CallableHandler implements MiddlewareInterface, RequestHandlerInterface
{
    private $callable;

    private $response;

    public function __construct(callable $callable, ResponseInterface $response)
    {
        $this->callable = $callable;
        $this->response = $response;
    }

    /**
     * {@inheritdoc}
     *
     * Process a server request and return a response.
     * @see RequestHandlerInterface
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws Exception
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->execute($this->callable, [$request]);
    }

    /**
     * {@inheritdoc}
     *
     * Process a server request and return a response.
     * @see MiddlewareInterface
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->execute($this->callable, [$request, $handler]);
    }

    /**
     * Magic method to invoke the callable directly
     * @return ResponseInterface
     * @throws Exception
     */
    public function __invoke(): ResponseInterface
    {
        return $this->execute($this->callable, func_get_args());
    }

    /**
     * Execute the callable.
     * @param callable $callable
     * @param array $arguments
     * @return ResponseInterface
     * @throws Exception
     */
    private function execute(callable $callable, array $arguments = []): ResponseInterface
    {
        ob_start();
        $level = ob_get_level();

        try {
            $return = call_user_func_array($callable, $arguments);

            if ($return instanceof ResponseInterface) {
                $response = $return;
                $return = '';
            } elseif (is_null($return)
                || is_scalar($return)
                || (is_object($return) && method_exists($return, '__toString'))
            ) {
                $response = $this->response;
            } else {
                throw new UnexpectedValueException(
                    'The value returned must be scalar or an object with __toString method'
                );
            }

            while (ob_get_level() >= $level) {
                $return = ob_get_clean().$return;
            }

            $body = $response->getBody();

            if ($return !== '' && $body->isWritable()) {
                $body->write($return);
            }

            return $response;
        } catch (Exception $exception) {
            while (ob_get_level() >= $level) {
                ob_end_clean();
            }

            throw $exception;
        }
    }
}