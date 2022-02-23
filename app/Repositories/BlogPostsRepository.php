<?php

namespace App\Repositories;

use App\Models\BlogPosts;
use App\Models\BlogComments;

class BlogPostsRepository
{
    /**
     * @type BlogPosts
     */
    protected $blogPosts;
    protected $blogComments;

    /**
     * @method __construct
     */
    public function __construct()
    {
        $this->blogPosts = new BlogPosts();
        $this->blogComments = new BlogComments();
    }

    /**
     * return all row 
     * @method all
     * @return Illuminate\Database\Eloquent\Collection  [\App\Models\BlogPosts]
     */
    public function all()
    {
        return $this->blogPosts->all();
    }

    /**
     * find to row 
     * @method 
     * @param  integer          $id [int]
     */
    public function getBlogPost($id)
    {
        // print_r($id);die();
        return $this->blogPosts->find($id);
    }

    /**
     * Stored row 
     * @method store
     * @param  Illuminate\Http\Request $request [App\Http\Requests]
     * @return bool
     */
    public function store($request)
    {
        // print_r($request);die();
        $request['created_by'] = auth()->user()->id;
        return $this->blogPosts->create($request);
    }

    /**
    * update blog post
    *
    * @method updateBlog
    *
    * @param $blogData, $blog
    *
    * @return bool
    */
    public function updateBlog(array $blogData, object $blog): ?bool
    {
        $blogData['updated_at'] = now(config('constants.TIME_ZONE'));
        $blogData['updated_by'] = auth()->user()->id ?? NULL;
        return $blog->update($blogData);
    }

    /**
     * Destroy row 
     * @method destroy
     * @param  integer  $id [int]
     * @return bool
     */
    public function destroy($id)
    {
        return $this->getBlogPost($id)->delete();
    }

    /**
     * find to row 
     * @method 
     * @param  integer          $id [int]
     */
    public function viewBlogPostwithInfo($id = null)
    {
        $model = $this->blogPosts::with('user:id,first_name,last_name')->with('comments:id,name,email,comments,blog_id');

        if(!empty($id)){
            $model = $model->where('id',$id);
        }

        return $model->select('id','title','description','created_by')->get();
    }

    public function getBlogProperties($request)
    {
        $model = $this->blogPosts::with('name,email,number')->with('comments:id,name,email,comments,blog_id');
        if(!empty($request['name'])){
            $model = $model->orWhere('name', 'LIKE', '%'.$request['name'].'%');
        }

        return $model->select('id','title','description','created_by')->get();
    }

     /**
     * Stored row 
     * @method store
     * @param  Illuminate\Http\Request $request [App\Http\Requests]
     * @return bool
     */
    public function storeBlogComments($request)
    {
        $request['created_by'] = auth()->user()->id;
        return $this->blogComments->create($request);
    }

    /**
     * find to row 
     * @method 
     * @param  integer          $id [int]
     */
    public function getBlogComment($id)
    {
        return $this->blogComments->find($id);
    }

    /**
    * update blog comments
    *
    * @method updateBlogComments
    *
    * @param $blogData, $blog
    *
    * @return bool
    */
    public function updateBlogComments(array $blogData, object $blog): ?bool
    {
        $blogData['updated_at'] = now(config('constants.TIME_ZONE'));
        $blogData['updated_by'] = auth()->user()->id ?? NULL;
        return $blog->update($blogData);
    }

    /**
     * return all row 
     * @method all
     * @return Illuminate\Database\Eloquent\Collection  [\App\Models\BlogPosts]
     */
    public function getAllBlogComments()
    {
        return $this->blogComments->all();
    }


    /**
     * Destroy row 
     * @method destroy
     * @param  integer  $id [int]
     * @return bool
     */
    public function blogCommentsdestroy($id)
    {
        return $this->getBlogComment($id)->delete();
    }
}
