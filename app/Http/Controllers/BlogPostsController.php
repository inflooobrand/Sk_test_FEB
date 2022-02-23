<?php

namespace App\Http\Controllers;


// Repositories
use App\Repositories\BlogPostsRepository;

//Resource
use App\Http\Resources\BlogResource;
use App\Http\Resources\BlogCommentsResource;

use Illuminate\Http\Request;

//Response
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BlogPostsController extends Controller
{
    /**
     * @var $model
     */
    protected $blogRepo;

    public function __construct(BlogPostsRepository $blogRepo)
    {
        $this->blogRepo = $blogRepo;
    }

    /**
     * Create new user
     *
     * @method register
     *
     * @param RegistrationRequest Request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function blogCreate(Request $request): JsonResponse
    {
        // Create new Blog
        $user = $this->blogRepo->store($request->all());
        // Process record
        $response = new BlogResource($user);

        $data = ['user' => $response];
        return $this->response(__('messages.REGISTRATION_SUCCESS'), compact('data'));
    }

    /**
    * Get record from table by Id.
    *
    * @method show
    *
    * @param  ShowRequest $request
    *
    * @return \Illuminate\Http\JsonResponse
    */
    public function show(Request $request): JsonResponse
    {
        try {
            $blogId = $request['id'];
            // Get single record
            $model = $this->blogRepo->viewBlogPostwithInfo((int) $blogId);

            // Process record
            $response = new BlogResource($model);
        } catch (ModelNotFoundException $exception) {
            return $exception->render();
        }
        
        // Send Response
        $data = ['model' => $model];
        return $this->response(__('messages.RECORD_FETCHED'), compact('data'));
    }

    /**
    * Update record in table.
    *
    * @method update
    *
    * @param  UpdateRequest $request
    *
    * @return \Illuminate\Http\JsonResponse
    */
    public function update(Request $request): JsonResponse
    {
        try {
            // auth user id
            $blogId = $request['id'];

            $blogRecord =$this->blogRepo->getBlogPost((int) $blogId);
            if(empty($blogRecord))
            {
                throw new ModelNotFoundException();

            }
            // Update record
            $this->blogRepo->updateBlog($request->all(), $blogRecord);

            $model = $this->blogRepo->getBlogPost((int) $blogId);
            // Process record
            $response = new BlogResource($model);
        } catch (ModelNotFoundException $exception) {
            return $exception->render();
        }

        // Send Response
        $data = ['model' => $response];
        return $this->response(__('messages.RECORD_UPDATED'), compact('data'));
    }

    /**
    * List all records.
    *
    * @method list
    *
    * @param  Request $request
    *
    * @return \Illuminate\Http\JsonResponse
    */
    public function list(Request $request): JsonResponse
    {
        // Declare variables
        $msg = 'RECORD_EMPTY';

        // Get all records
        $model = $this->blogRepo->viewBlogPostwithInfo();

        // Process record
         $response = BlogResource::collection($model);

        // Send Response
        $data = ['model' => $response];
        return $this->response(__('messages.'.$msg), compact('data'));
    }

    /**
    * delete blog record.
    *
    * @method delete
    *
    * @param  Request $request
    *
    * @return \Illuminate\Http\JsonResponse
    */
    public function delete(Request $request): JsonResponse
    {
        // Declare variables
        $msg = 'RECORD_DELETED';

        $blogId = $request['id'];

        $blogRecord = $this->blogRepo->getBlogPost((int) $blogId);

        if(empty($blogRecord))
        {
            throw new ModelNotFoundException();
        }

        // Get all records
        $model = $this->blogRepo->destroy((int) $blogId);

        
        return $this->response(__('Blog deleted successfully'));
    }

    /**
     * Create new blog comments
     *
     * @method blogComments
     *
     * @param Request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function blogComments(Request $request): JsonResponse
    {
        
        // print_r($request->all());die();
        $user = $this->blogRepo->storeBlogComments($request->all());

        return $this->response(__('Blog comments saved successfully'));
    }

    /**
    * Get record from table by Id.
    *
    * @method show
    *
    * @param  ShowRequest $request
    *
    * @return \Illuminate\Http\JsonResponse
    */
    public function blogCommentShow(Request $request): JsonResponse
    {
        try {
            $blogId = $request['id'];
            // Get single record
            $model = $this->blogRepo->getBlogComment((int) $blogId);

            // Process record
            $response = new BlogCommentsResource($model);
        } catch (ModelNotFoundException $exception) {
            return $exception->render();
        }
        
        // Send Response
        $data = ['model' => $response];
        return $this->response(__('messages.RECORD_FETCHED'), compact('data'));
    }

    /**
    * Update record in table.
    *
    * @method update
    *
    * @param  UpdateRequest $request
    *
    * @return \Illuminate\Http\JsonResponse
    */
    public function blogCommentupdate(Request $request): JsonResponse
    {
        try {
            // auth user id
            $blogId = $request['id'];

            $blogRecord = $this->blogRepo->getBlogComment((int) $blogId);

            if(empty($blogRecord))
            {
                throw new ModelNotFoundException();
            }
            // Update record
            $this->blogRepo->updateBlogComments($request->all(), $blogRecord);

            $model = $this->blogRepo->getBlogComment((int) $blogId);
            // Process record
            $response = new BlogCommentsResource($model);
        } catch (ModelNotFoundException $exception) {
            return $exception->render();
        }

        // Send Response
        $data = ['model' => $response];
        return $this->response(__('messages.RECORD_UPDATED'), compact('data'));
    }

    /**
    * List all records.
    *
    * @method blogCommentList
    *
    * @param  Request $request
    *
    * @return \Illuminate\Http\JsonResponse
    */
    public function blogCommentList(Request $request): JsonResponse
    {
        // Declare variables
        $msg = 'RECORD_EMPTY';

        // Get all records
        $model = $this->blogRepo->getAllBlogComments();

        // Process record
         $response = BlogCommentsResource::collection($model);

        // Send Response
        $data = ['model' => $response];
        return $this->response(__('messages.'.$msg), compact('data'));
    }

    /**
    * delete blog record.
    *
    * @method blogCommentDelete
    *
    * @param  Request $request
    *
    * @return \Illuminate\Http\JsonResponse
    */
    public function blogCommentDelete(Request $request): JsonResponse
    {
        // Declare variables
        $msg = 'RECORD_DELETED';

        $blogId = $request['id'];

        $blogRecord = $this->blogRepo->getBlogComment((int) $blogId);

        if(empty($blogRecord))
        {
            throw new ModelNotFoundException();
        }

        // Get all records
        $model = $this->blogRepo->blogCommentsdestroy((int) $blogId);

        
        return $this->response(__('Blog deleted successfully'));
    }
}
