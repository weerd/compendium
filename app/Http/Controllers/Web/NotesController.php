<?php

namespace Compendium\Http\Controllers\Web;

use Compendium\Services\Dropbox\Dropbox;
use Compendium\Http\Controllers\Controller;

class NotesController extends Controller
{
    protected $dropbox;

    /**
     * Create new NotesController instance.
     *
     * @param \Compendium\Services\Dropbox\Dropbox  $dropbox
     */
    public function __construct(Dropbox $dropbox)
    {
        $this->dropbox = $dropbox;

        $this->middleware('auth');
        $this->middleware('auth.dropbox');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // dd($this->dropbox->client->getAccountInfo());

        return 'list of all notes from Dropbox';
    }
}
