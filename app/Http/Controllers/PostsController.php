<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Post;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Auth;


class PostsController extends Controller
{


    public function __construct() {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $posts =  Post::orderBy('created_at', 'desc')->where('status', '1')->get();



        return view('posts.index')->with('posts', $posts);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        if(auth()->user()->admin !== 1) {
            return redirect('/posts')->with('error', 'Niets gevonden!');
        }


        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'body' => 'required',
            'cover_image' => 'image|nullable|max:1999'

        ]);

        //handle file pload
            if($request->hasFile('cover_image')) {
                //Get filename with the extension
                $filenameWithExt = $request->file('cover_image')->getClientOriginalName();
                //Get just filename
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);;
                //Get just ext
                $extension = $request->file('cover_image')->getClientOriginalExtension();
                //Filename to store
                $fileNameToStore= $filename.'_'.time().'.'.$extension;
                // Upload image
                $path = $request->file('cover_image')->storeAs('public/cover_images', $fileNameToStore);
            } else {
                    $fileNameToStore = 'noimage.jpg';
            }
            //create new post
        $post = new Post;
        $post->title = $request->input('title');
        $post->body = $request->input('body');
        $post->user_id = auth()->user()->id; //zet post in de user_id die ingelogd is
        $post->cover_image = $fileNameToStore;
        $post->status = 0;
        $post->save();

        return redirect('/home')->with('success', 'Post Created, Now you are able to activate the post so others can see it!');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {


        //zoeken naar post id
        $post = Post::find($id);

        if(Auth::check() && $post->status == 1) {
                  //zoeken naar user datum van registratie
        $user = auth()->user()->created_at;

        //formateren van datum user naar ymd hsi
        $user1to = Carbon::createFromFormat('Y-m-d H:s:i', $user);

        //maken van carbon van created_at in post
        $post1 =  new Carbon($post->created_at);

        //Carbon van datum/tijd van nu omzetten in andere formaat
        $now = Carbon::now()->format('Y-m-d H:i:s');

        //Omzetten van post_datum in andere formaat
        $to = Carbon::createFromFormat('Y-m-d H:s:i', $post1);

        //Omzetten van user_datum in andere formaat
        $from = Carbon::createFromFormat('Y-m-d H:s:i', $now);

        //verschil in dagen tussen vandaag en sinds de post is gemaakt
        $diff_in_days = $to->diffInDays($from);

        //verschil in dagen tussen vandaag en sinds de user is geregistreerd
        $diff_in_days_user = $user1to->diffInDays($from);

        return view('posts.show')->with('post', $post)->with('days', $diff_in_days);

    } else {
            return redirect('posts')->with('error', 'Unauthorized!');

    }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        if(auth()->user()->admin !== 1) {
            return redirect('/posts')->with('error', 'Unauthorized Page');
        }

        $post =  Post::find($id);

        if(auth()->user()->id !== $post->user_id) {
            return redirect('/posts')->with('error', 'Unauthorized Page');
        }
        return view('posts.edit')->with('post', $post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'body' => 'required'
        ]);

         //handle file pload
         if($request->hasFile('cover_image')) {
            //Get filename with the extension
            $filenameWithExt = $request->file('cover_image')->getClientOriginalName();
            //Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);;
            //Get just ext
            $extension = $request->file('cover_image')->getClientOriginalExtension();
            //Filename to store
            $fileNameToStore= $filename.'_'.time().'.'.$extension;
            // Upload image
            $path = $request->file('cover_image')->storeAs('public/cover_images', $fileNameToStore);
        }

        $post = Post::find($id);
        $post->title = $request->input('title');
        $post->body = $request->input('body');
        $post->status = $request->input('status');
        if($request->hasFile('cover_image')) {
            $post->cover_image = $fileNameToStore;
        }

        $post->save();

        return redirect('/posts')->with('success', 'Post Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);

        if(auth()->user()->admin !== 1) {
            return redirect('/posts')->with('error', 'Unauthorized Page');
        }

        if($post->cover_image != 'noimage.jpg'){
            //Delete Image from folder if its deleted
            Storage::delete('public/cover_images/'.$post->cover_image);
        }

        $post->delete();
        return redirect('/posts')->with('success', 'Post Removed');

    }
}
