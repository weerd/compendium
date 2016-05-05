<?php

namespace Compendium\Services\Dropbox;

class DropboxTokenStore
{
    protected $key;

    protected $store;

    public function __construct($key)
    {
        $this->store = app('session');
        $this->key = $key;
    }

    public function clear()
    {
        $this->store->forget($this->key);
    }

    public function get()
    {

        return $this->store->get($this->key);
    }

    public function set($value)
    {
        $this->store->put($this->key, $value);
        $this->store->save();
    }
}
