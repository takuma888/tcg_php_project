<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/1
 * Time: 下午2:40
 */

namespace TCG\Http\Message;

/**
 * Provides a PSR-7 implementation of a reusable raw request body
 */
class RequestBody extends Body
{
    /**
     * Create a new RequestBody.
     */
    public function __construct()
    {
        $stream = fopen('php://temp', 'w+');
        stream_copy_to_stream(fopen('php://input', 'r'), $stream);
        rewind($stream);
        parent::__construct($stream);
    }
}