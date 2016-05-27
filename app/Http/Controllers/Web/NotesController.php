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
        // $this->dropbox->getPathContents('/compendium/cURL/01_basics.md');

        $notes = $this->dropbox->getPathContents(); // directory

        // dd($this->dropbox->client->searchFileNames('/', 'Compendium')); // Finds /Code/compendium

        return view('notes.index', compact('notes'));
    }
}
