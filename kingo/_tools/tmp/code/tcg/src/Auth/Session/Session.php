<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/6
 * Time: 上午11:29
 */

namespace TCG\Auth\Session;


class Session
{
    /**
     * @var bool
     */
    protected $started = false;
    /**
     *
     * A copy of the $_COOKIE array.
     *
     * @var array
     *
     */
    protected $cookie;
    /**
     * @var callable
     */
    protected $open;
    /**
     * @var callable
     */
    protected $close;
    /**
     * @var callable
     */
    protected $read;
    /**
     * @var callable
     */
    protected $write;
    /**
     * @var callable
     */
    protected $destroy;
    /**
     * @var callable
     */
    protected $gc;

    /**
     *
     * Constructor.
     *
     * @param array $cookie A copy of the $_COOKIE array.
     *
     */
    public function __construct(array $cookie)
    {
        $this->cookie = $cookie;
    }

    /**
     * @param array $config
     * @return bool
     */
    public function start(array $config = [])
    {
        if (!$this->started) {
            $this->started = true;
            session_start();
        }
        return $this->started;
    }


    public function bind(callable $open, callable $close, callable $read, callable $write, callable $destroy, callable $gc)
    {
        $this->open = $open;
        $this->close = $close;
        $this->read = $read;
        $this->write = $write;
        $this->destroy = $destroy;
        $this->gc = $gc;
        ini_set('session.save_handler', 'user');
        session_set_save_handler($this->open, $this->close, $this->read, $this->write, $this->destroy, $this->gc);
        register_shutdown_function([$this, 'close']);
    }


    public function close()
    {
        $this->started = false;
        if (session_id() !== '') {
            $max_lifetime = intval(ini_get('session.gc_maxlifetime'));
            call_user_func_array($this->gc, [$max_lifetime]);
            session_write_close();
        }
    }

    public function destroy()
    {
        if (session_id() !== '') {
            session_unset();
            session_destroy();
        }
    }


    /**
     *
     * Resumes a previously-started session.
     *
     * @return bool
     *
     */
    public function resume()
    {
        if (session_id() !== '') {
            return true;
        }
        if (isset($this->cookie[session_name()])) {
            return $this->start();
        }
        return false;
    }
    /**
     *
     * Regenerates a session ID.
     *
     * @return mixed
     *
     */
    public function regenerateId()
    {
        return session_regenerate_id(true);
    }
}