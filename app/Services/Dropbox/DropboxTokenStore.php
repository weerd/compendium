<?php

namespace Compendium\Services\Dropbox;

class DropboxTokenStore
{
    /**
     * Dropbox API key.
     *
     * @var string
     */
    protected $key;

    /**
     * Session store instance.
     *
     * @var \Illuminate\Session\SessionManager
     */
    protected $store;

    /**
     * Create new DropboxTokenStore instance.
     *
     * @param string  $key
     */
    public function __construct($key)
    {
        $this->store = app('session');
        $this->key = $key;
    }

    /**
     * Clear the Dropbox sesstion token from the store.
     *
     * @return void
     */
    public function clear()
    {
        $this->store->forget($this->key);
    }

    /**
     * Get the Dropbox session token from the store.
     *
     * @return mixed
     */
    public function get()
    {
        return $this->store->get($this->key);
    }

    /**
     * Set the Dropbox session token in the store.
     *
     * @param  string $value
     * @return void
     */
    public function set($value)
    {
        $this->store->put($this->key, $value);

        $this->store->save();
    }
}
