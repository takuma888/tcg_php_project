<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/6
 * Time: 下午12:35
 */


namespace TCG\Auth;


use TCG\Auth\Session\Segment;
use TCG\Auth\Session\SegmentInterface;
use TCG\Auth\Session\Session;

class Factory
{
    /**
     *
     * A session manager.
     *
     * @var Session
     *
     */
    protected $session;
    /**
     *
     * A session segment.
     *
     * @var SegmentInterface
     *
     */
    protected $segment;


    /**
     *
     * Constructor.
     *
     * @param array $cookie A copy of $_COOKIES.
     *
     * @param Session $session A session manager.
     *
     * @param SegmentInterface $segment A session segment.
     *
     */
    public function __construct(array $cookie, Session $session, SegmentInterface $segment) {
        $this->session = $session;
        $this->segment = $segment;
    }

    /**
     *
     * Returns a new authentication tracker.
     *
     * @return Auth
     *
     */
    public function newInstance()
    {
        return new Auth($this->segment);
    }

}