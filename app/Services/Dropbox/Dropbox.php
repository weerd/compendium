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
     * The Dropbox authentication instance.
     *
     * @var \Dropbox\WebAuth
     */
    protected $authenticator;

    /**
     * The Dropbox client instance.
     *
     * @var \Dropbox\Client
     */
    protected $client;

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
     *
     * @param \Illuminate\Http\Request  $request
     */
    public function __construct(Request $request)
    {
        $this->name = env('DROPBOX_APP_NAME');

        $this->info = new AppInfo(env('DROPBOX_API_KEY'), env('DROPBOX_API_SECRET'));

        $csrfTokenStore = new DropboxTokenStore(env('DROPBOX_TOKEN_SESSION_KEY'));

        $this->authenticator = new WebAuth($this->info, env('DROPBOX_APP_NAME'), env('DROPBOX_APP_REDIRECT'), $csrfTokenStore);
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
     * Check if user token is valid.
     *
     * @param  \Compendium\Models\User  $user
     * @return mixed
     */
    public function validateToken($user)
    {
        if ($user->dropbox_token) {
            $this->client = new Client($user->dropbox_token, $this->name, 'UTF-8');

            try {
                return $this->client->getAccountInfo();
            } catch (Exception_InvalidAccessToken $e) {
                return false;
            }
        }

        return false;
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

        return $user->save();
    }
}
