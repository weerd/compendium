<?php

namespace Compendium\Services\Dropbox;

use Compendium\Services\Dropbox\DropboxTokenStore;
use Dropbox\Client;
use Dropbox\AppInfo;
use Dropbox\ArrayEntryStore;
use Dropbox\Exception_InvalidAccessToken;
use Dropbox\WebAuth;
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

    protected $info;

    protected $name;

    protected $user;

    public function __construct(Request $request)
    {
        $this->name = env('DROPBOX_APP_NAME');

        $this->info = new AppInfo(env('DROPBOX_API_KEY'), env('DROPBOX_API_SECRET'));

        $this->user = $request->user();

        $csrfTokenStore = new DropboxTokenStore('dropbox-auth-csrf-token');

        $this->authenticator = new WebAuth($this->info, env('DROPBOX_APP_NAME'), env('DROPBOX_APP_REDIRECT'), $csrfTokenStore);
    }

    /**
     * [requestToken description]
     * @param  [type] $user [description]
     * @return [type]       [description]
     */
    public function requestToken($user)
    {
        header('Location: ' . $this->authenticator->start());
        exit();
    }

    /**
     * [validateToken description]
     * @param  [type] $user [description]
     * @return [type]       [description]
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

        $user->dropbox_token = $token;

        return $user->save();
    }
}
