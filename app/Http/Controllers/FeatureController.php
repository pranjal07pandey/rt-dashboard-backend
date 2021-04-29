<?php

namespace App\Http\Controllers;

use Session;
use App\Post;
use App\Category;
use App\PostScreenshot;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Helpers\V2\FunctionUtils;

class FeatureController extends Controller
{
    public function index()
    {
	    Session::put('navigation','feature');
        Session::put('pageTitle','Feature Category');
        $catgories = Category::orderBy('created_at','asc')->get();
        return view('dashboard.V2.admin.feature.category.index', compact('catgories'));
    }

    public function saveCategoryInfo(Request $request)
    {
    	$this->validate($request,['name'=>'required','description'   => 'required']);
        if(Category::where('name',$request->name)->count()!=0){
            flash('The Name "'.$request->name.'" has already been taken.','warning');
            return redirect()->back();
        }else{
            $category           		 =   new Category();
            $category->name 		     =   $request->name;
            $category->slug  			 =   str_slug($request->name);
            $category->description       =   $request->description;
            $category->save();
            flash('Category added successfully.','success');
            return redirect()->back();
        }
    }

    public function updateCategoryInfo(Request $request){
       	$this->validate($request,['name'=>'required','description'   => 'required']);
        $category   			=   Category::where('id',$request->id)->firstOrFail();
        $category->name 		=   $request->name;
        $category->slug  	    =   str_slug($request->name);
        $category->description  =   $request->description;
        $category->update();
        flash('Category Updated successfully.','success');
        return redirect()->back();

    }

    public function categoryDelete($id)
    {
        $category = Category::where('id', $id)->first();
        $category->is_active = 0;
        $category->save();

        flash('Category deleted successfully.','success');
        return redirect()->back();
    }

    public function categoryRestore($id)
    {
        $category = Category::where('id', $id)->first();
        $category->is_active = 1;
        $category->save();

        flash('Category restored successfully.','success');
        return redirect()->back();
    }

    public function post($id)
    {
    	Session::put('navigation','feature');
        Session::put('pageTitle','Post');

        $category = Category::where('id',$id)->firstOrFail();
    	$posts = Post::where('category_id', $category->id)->get();
        return view('dashboard.V2.admin.feature.post.index', compact('category', 'posts'));
    }

    public function savePost(Request $request){

    	$this->validate($request,['name'=>'required','description'   => 'required', 'featured_image' => 'required']);
        if(Post::where('name',$request->name)->count()!=0){
            flash('The Name "'.$request->name.'" has already been taken.','warning');
            return redirect()->back();
        }else{
            $post           		 =   new Post();
            $post->category_id 		 =   $request->category_id;
            $post->name 		     =   $request->name;
            $post->slug  			 =   str_slug($request->name);
            $post->description       =   $request->description;

            $featured_image              =   Input::file('featured_image');
            if($request->hasFile('featured_image')) {
                if ($featured_image->isValid()) {
                    // $ext = $featured_image->getClientOriginalExtension();
                    // $filename = basename($request->file('featured_image')->getClientOriginalName(), '.' . $request->file('featured_image')->getClientOriginalExtension()) . time() . "." . $ext;
                    $dest = 'files/post/featured_image';
                    // $featured_image->move($dest, $filename);
                    // $post->featured_image = $dest . '/' . $filename;
                    $post->featured_image = FunctionUtils::imageUpload($dest,$featured_image);
                }
            }

            $post->save();
            flash('Post added successfully.','success');
            return redirect()->back();
        }
    }

    public function updatePostInfo(Request $request)
    {
    	$post   			=  Post::where('id',$request->id)->firstOrFail();
        $post->name 		=  $request->name;
        $post->slug         =  str_slug($request->name); 
        $post->description  =  $request->description;

        $featured_image     =  Input::file('featured_image');
        if($request->hasFile('featured_image')) {
            if ($featured_image->isValid()) {
                // $ext = $featured_image->getClientOriginalExtension();
                // $filename = basename($request->file('featured_image')->getClientOriginalName(), '.' . $request->file('featured_image')->getClientOriginalExtension()) . time() . "." . $ext;
                $dest = 'files/post/featured_image';
                // $featured_image->move($dest, $filename);
                // $post->featured_image = $dest . '/' . $filename;
                $post->featured_image = FunctionUtils::imageUpload($dest,$featured_image);
            }
        }

        $post->update();
        flash('Post updated successfully.','success');
        return redirect()->back();
    }

    public function postDelete($id)
    {
        $post = Post::where('id', $id)->first();
        $post->is_active = 0;
        $post->save();

        flash('Post deleted successfully.','success');
        return redirect()->back();
    }

    public function postRestore($id)
    {
        $post = Post::where('id', $id)->first();
        $post->is_active = 1;
        $post->save();

        flash('Post restored successfully.','success');
        return redirect()->back();
    }

    public function savePostScreenshot(Request $request){

    	$postScreenshot = new PostScreenshot();

    	$postScreenshot->post_id = $request->post_id;
    	$postScreenshot->name = $request->name;

    	$image     =  Input::file('screenshot');
        if($request->hasFile('screenshot')) {
            if ($image->isValid()) {
                // $ext = $image->getClientOriginalExtension();
                // $filename = basename($request->file('screenshot')->getClientOriginalName(), '.' . $request->file('screenshot')->getClientOriginalExtension()) . time() . "." . $ext;
                $dest = 'files/post/image';
                // $image->move($dest, $filename);
                // $postScreenshot->image = $dest . '/' . $filename;
                $postScreenshot->image = FunctionUtils::imageUpload($dest,$image);
            }
        }

        $postScreenshot->save();
        flash('Screenshot added successfully.','success');
        return redirect()->back();

    }

    public function postScreenshot($id)
    {
    	Session::put('navigation','feature');
        Session::put('pageTitle','Post');

        $post = Post::where('id',$id)->firstOrFail();
    	$screenshots = PostScreenshot::where('post_id', $post->id)->get();
        return view('dashboard.V2.admin.feature.post.screenshot', compact('post', 'screenshots'));
    }

    public function updateScreenshot(Request $request)
    {
    	$postScreenshot   			=  PostScreenshot::where('id',$request->id)->firstOrFail();
        $postScreenshot->name 		=  $request->name;

        $image     =  Input::file('screenshot');
        if($request->hasFile('screenshot')) {
            if ($image->isValid()) {
                // $ext = $image->getClientOriginalExtension();
                // $filename = basename($request->file('screenshot')->getClientOriginalName(), '.' . $request->file('screenshot')->getClientOriginalExtension()) . time() . "." . $ext;
                $dest = 'files/post/image';
                // $image->move($dest, $filename);
                // $postScreenshot->image = $dest . '/' . $filename;
                $postScreenshot->image = FunctionUtils::imageUpload($dest,$image);
            }
        }

        $postScreenshot->update();
        flash('Screenshot updated successfully.','success');
        return redirect()->back();
    }
}
