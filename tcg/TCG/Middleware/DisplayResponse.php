<?php
/**
 * Created by PhpStorm.
 * User: tachigo
 * Date: 2018/6/2
 * Time: 下午4:17
 */

namespace TCG\Middleware;

use Psr\Http\Message\ResponseInterface;

class DisplayResponse
{
    public function send(ResponseInterface $response): ResponseInterface
    {
        // stop PHP sending a Content-Type automatically
        $response = $this->finalize($response);

        // Send response
        $this->respond($response);

        return $response;
    }

    /**
     * Send the response to the client
     *
     * @param ResponseInterface $response
     */
    public function respond(ResponseInterface $response)
    {
        // Send response
        if (!headers_sent()) {
            // Headers
            foreach ($response->getHeaders() as $name => $values) {
                foreach ($values as $value) {
                    header(sprintf('%s: %s', $name, $value), false);
                }
            }
            // Set the status _after_ the headers, because of PHP's "helpful" behavior with location headers.
            // See https://github.com/slimphp/Slim/issues/1730
            // Status
            header(sprintf(
                'HTTP/%s %s %s',
                $response->getProtocolVersion(),
                $response->getStatusCode(),
                $response->getReasonPhrase()
            ));
        }
        // Body
        if (!$this->isEmptyResponse($response)) {
            $body = $response->getBody();
            if ($body->isSeekable()) {
                $body->rewind();
            }
            $contentLength  = $response->getHeaderLine('Content-Length');
            if (!$contentLength) {
                $contentLength = $body->getSize();
            }
            $amountToRead = $contentLength;
            while ($amountToRead > 0 && !$body->eof()) {
                $data = $body->read($amountToRead);
                echo $data;
                $amountToRead -= strlen($data);
                if (connection_status() != CONNECTION_NORMAL) {
                    break;
                }
            }
        }
    }


    /**
     * Finalize response
     *
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    protected function finalize(ResponseInterface $response)
    {
        // stop PHP sending a Content-Type automatically
        ini_set('default_mimetype', '');
        if ($this->isEmptyResponse($response)) {
            return $response->withoutHeader('Content-Type')->withoutHeader('Content-Length');
        }
        // Add Content-Length header if `addContentLengthHeader` setting is set
        $addContentLengthHeader = false;
        if (isset($addContentLengthHeader) && $addContentLengthHeader == true) {
            if (ob_get_length() > 0) {
                throw new \RuntimeException("Unexpected data in output buffer. " .
                    "Maybe you have characters before an opening <?php tag?");
            }
            $size = $response->getBody()->getSize();
            if ($size !== null && !$response->hasHeader('Content-Length')) {
                $response = $response->withHeader('Content-Length', (string) $size);
            }
        }
        return $response;
    }


    /**
     * Helper method, which returns true if the provided response must not output a body and false
     * if the response could have a body.
     *
     * @see https://tools.ietf.org/html/rfc7231
     *
     * @param ResponseInterface $response
     * @return bool
     */
    protected function isEmptyResponse(ResponseInterface $response)
    {
        if (method_exists($response, 'isEmpty')) {
            return $response->isEmpty();
        }
        return in_array($response->getStatusCode(), [204, 205, 304]);
    }
}