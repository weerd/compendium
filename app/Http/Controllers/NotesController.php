<?php

namespace Compendium\Http\Controllers;

use Illuminate\Http\Request;
use Compendium\Http\Requests;
use Compendium\Services\Dropbox\Dropbox;
use Compendium\Http\Controllers\Controller;

class NotesController extends Controller
{
    protected $dropbox;

    public function __construct(Dropbox $dropbox)
    {
        $this->dropbox = $dropbox;

        $this->middleware('auth');
        $this->middleware('auth.dropbox');
    }

    public function index()
    {
        return 'jello!';
    }
}
