<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Post;
use App\Tag;
use App\Category;
use Purifier;
use Session;
use Image;
use Storage;
use Auth;

class PostController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }
    
    public function index()
    {
         $posts = Post::orderBy('id', 'desc')->paginate(10);
        return view('posts.index')->withPosts($posts);
    }

    public function create()
    {
        //
        $categories = Category::all();
        $tags = Tag::all();
        return view('posts.create')->withCategories($categories)->withTags($tags);
    }
    

 
    public function store(Request $request)
    {
        
        // validate the data
        $this->validate($request, array(
                'title'         => 'required|max:255',
                'slug'          => 'required|alpha_dash|min:5|max:255|unique:posts,slug',
                'category_id'   => 'required|integer',
                'body'          => 'required',
                'featured_img' => 'sometimes|image',
            ));

        // store in the database
        $post = new Post;

        $post->title = $request->title;
        $post->slug = $request->slug;
        $post->category_id = $request->category_id;
        $post->body = Purifier::clean($request->body);
        $post->user_id = Auth::user()->id;

        if ($request->hasFile('featured_img')) {
          $image = $request->file('featured_img');
          $filename = time() . '.' . $image->getClientOriginalExtension();
          $location = public_path('images/' . $filename);
          Image::make($image)->resize(600, 300)->save($location);
          //this stores the filename to the database
          $post->image = $filename;
        }

        $post->save();
        //notice that we explicitly define tag function seperately because we want it to be optional
        $post->tags()->sync($request->tags, false);

        Session::flash('success', 'The blog post was successfully saved!');

        return redirect()->route('posts.show', $post);
    } 

  
    public function show($id)
    {
        $post = Post::find($id);
        return view('posts.show')->withPost($post);
    }

  
    public function edit($id)
    {
        $post = Post::find($id);
        $categories = Category::all();
        $cats = array();
        foreach ($categories as $category) {
            $cats[$category->id] = $category->name;
        }

        $tags = Tag::all();
        $tags2 = array();
        foreach ($tags as $tag) {
           $tags2[$tag->id] = $tag->name;
        }
        // return the view and pass in the var we previously created
        return view('posts.edit')->withPost($post)->withCategories($cats)->withTags($tags2);
    }


    public function update(Request $request, $id)
    {
        $post = Post::find($id);

         if ($request->input('slug') == $post->slug) {
            $this->validate($request, array(
                'title' => 'required|max:255',
                'category_id' => 'required|integer',
                'body'  => 'required',
                'featured_img' => 'image'
            ));
        } else {

        
        $this->validate($request, array(
                'title' => 'required|max:255',
                'slug'  => 'required|alpha_dash|min:5|max:255|unique:posts,slug',
                'category_id' => 'required|integer',
                'body'  => 'required',
                'featured_img' => 'image'
            ));
    }
        
        

        // Save the data to the database
        

        $post->title = $request->input('title');
        $post->slug = $request->input('slug');
        $post->category_id = $request->input('category_id');
        $post->body = Purifier::clean($request->input('body'));

        if ($request->hasFile('featured_img')) {
            //add new photo
          $image = $request->file('featured_img');
          $filename = time() . '.' . $image->getClientOriginalExtension();
          $location = public_path('images/' . $filename);
          Image::make($image)->resize(800, 400)->save($location);

          $oldfilename = $post->image;
          //update the database
          $post->image = $filename;
          //Delete the old photo
          Storage::delete($oldfilename);
      }

        $post->save();

        if (isset($request->tags)) {
            $post->tags()->sync($request->tags, true);
        } else {
            $post->tags()->sync(array());
        }


      
        Session::flash('success', 'This post was successfully saved.');

       
        return redirect()->route('posts.show', $post->id);
    }

   
   /**
     * delete the specified resource in the storage.
     *
     * @param  int  $id
     */
    public function destroy($id)
    {
        $post = Post::find($id);
        $post->tags()->detach();
        Storage::delete($post->image);

        $post->delete();

        Session::flash('success', 'The post was successfully deleted.');
        return redirect()->route('posts.index');
    }
}
