<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Post;
use App\Favourite;

class PagesController extends Controller //elke controller moet een controller extenden
{
    public function index() {
        $title = 'Welkom bij Leidsman Begeleiding!';
        return view('pages.index')->with('title', $title);
    }

    public function about() {
        $title = 'About us!';
        return view('pages.about')->with('title', $title);
    }

    public function services() {
        $post = DB::table('posts')
                ->inRandomOrder()
                ->get();
        $title = 'Wij bieden verschillende services aan, zoals!';


        return view('pages.services')->with('title', $title)->with('posts', $post);
    }









}


