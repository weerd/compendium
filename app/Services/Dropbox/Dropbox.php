<?php

namespace Compendium\Services\Dropbox;

use Dropbox\Client;
use Dropbox\AppInfo;
use Dropbox\WebAuth;
use Dropbox\Exception_InvalidAccessToken;
use Illuminate\Http\Request;

class Dropbox
{
    /**
     * Dropbox user account info.
     *
     * @var array
     */
    public $account;

    /**
     * The Dropbox client instance.
     *
     * @var \Dropbox\Client
     */
    public $client;

    /**
     * The main app path.
     *
     * @var string
     */
    public $path = '/Compendium';

    /**
     * The main app contents.
     *
     * @var array
     */
    public $contents;

    /**
     * The Dropbox authentication instance.
     *
     * @var \Dropbox\WebAuth
     */
    protected $authenticator;

    /**
     * The Dropbox AppInfo instance.
     *
     * @var \Dropbox\AppInfo
     */
    protected $info;

    /**
     * The Dropbox App name.
     *
     * @var string
     */
    protected $name;

    /**
     * Create new Dropbox instance.
     */
    public function __construct()
    {
        $this->name = env('DROPBOX_APP_NAME');

        $this->info = new AppInfo(env('DROPBOX_API_KEY'), env('DROPBOX_API_SECRET'));

        $csrfTokenStore = new DropboxTokenStore(env('DROPBOX_TOKEN_SESSION_KEY'));

        $this->authenticator = new WebAuth($this->info, env('DROPBOX_APP_NAME'), env('DROPBOX_APP_REDIRECT'), $csrfTokenStore);
    }

    public function getPathContents($path = null)
    {
        if (! $path) {
            $path = $this->path;
        }

        $data = $this->client->getMetadataWithChildren($path);

        if (isset($data['contents'])) {
            return collect($data['contents']);
        }

        return $data;

    }

    /**
     * Request a new authentication token from Dropbox.
     *
     * @return void
     */
    public function requestToken()
    {
        header('Location: ' . $this->authenticator->start());

        exit();
    }

    /**
     * Store the Dropbox Authentication token in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Compendium\Models\User  $user
     * @return bool
     */
    public function storeToken($request, $user)
    {
        list($token, $dropboxId, $state) = $this->authenticator->finish($request->all());
        // @TODO: need to deal with catching the Exception_BadRequest exception if code has expired.
        // @TODO: need to deal with catching the WebAuthException_BadState if CSRF session token is missing?

        $user->dropbox_token = $token;
        $user->save();

        return $this->initializeClient($token);
    }

    /**
     * Check if user token is valid.
     *
     * @param  \Compendium\Models\User  $user
     * @return mixed
     */
    public function validateToken($user)
    {
        if ($user->dropbox_token) {
            return $this->initializeClient($user->dropbox_token);
        }

        return false;
    }

    protected function findOrCreateAppDirectory()
    {
        $data = $this->getPathContents($this->path);

        if (! $data) {
            $data = $this->client->createFolder($this->path);
        }

        return $this->contents = $data;
    }

    /**
     * Set the Dropbox client and attempt to connect to Dropbox.
     *
     * @param  string  $token
     * @return void
     */
    protected function initializeClient($token)
    {
        if (! $this->client) {
            $this->client = new Client($token, $this->name, 'UTF-8');

            try {
                $this->account = $this->client->getAccountInfo();

                if ($this->account) {
                    $this->findOrCreateAppDirectory();
                }

                dd($this);
                return $this->account;
            } catch (Exception_InvalidAccessToken $e) {
                return false;
            }
        }

        return $this->client->account;
    }
}
