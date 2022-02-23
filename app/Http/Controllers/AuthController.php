<?php

namespace App\Http\Controllers;

// Request
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\UserProfileUpdateRequest;

use App\Repositories\UserRepository;

// Exception
use App\Exceptions\UserNotFoundException;
use App\Exceptions\UserEmailException;
use App\Exceptions\ModelNotFoundException;

// Response
use Illuminate\Http\JsonResponse;

// Resource
use App\Http\Resources\UserResource;

// DB
use DB;

class AuthController extends Controller
{

	/**
     * @var $model
     */
    protected $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }
    /**
     * Attempt login with the credentials.
     *
     * @method login
     *
     * @param LoginRequest Request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            // Authenticate with given credentials
            $this->userRepo->attempt($request->all());

            // Get access token
            $user = auth()->user();
            $token = $this->userRepo->createAccessToken($user);
        } catch (UserNotFoundException $exception) {
            return $exception->render();
        }
        $data = ['token' => $token];

        return $this->response(__('messages.LOGIN_SUCCESS'), compact('data'));
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
    public function register(RegistrationRequest $request): JsonResponse
    {
    	$userExists = $this->userRepo->checkEmailUserExists((array) $request->all());
        if($userExists)
        {
            throw new UserEmailException();
        }
        // Create new user
        $user = $this->userRepo->store($request->all());

        // Process record
        $response = new UserResource($user);

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
    public function show(): JsonResponse
    {
        try {
            $userId =auth()->user()->id;
            // Get single record
            $model = $this->userRepo->getUser((int) $userId);
            // Process record
            $response = new UserResource($model);
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
    public function update(UserProfileUpdateRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            // auth user id
            $userId = auth()->user()->id;

            $userRecord = $this->userRepo->getUser((int) $userId);
            if(empty($userRecord))
            {
                throw new ModelNotFoundException();
            }
            // Update record
            $this->userRepo->updateUser($request->all(), $userRecord);
             DB::Commit();
            $model = $this->userRepo->getUser((int) $userId);
            // Process record
            $response = new UserResource($model);
        } catch (ModelNotFoundException $exception) {
             DB::rollback();
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
        $model = $this->userRepo->userList($request);

        // Check record's available
        if ($model['list']->count() > 0) {
            $msg = 'RECORD_LISTED';
        }

        // Send Response
        $data = ['model' => $model];
        return $this->response(__('messages.'.$msg), compact('data'));
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request): JsonResponse
    {
        auth()->user()->token()->revoke();
        return response()->json([
            'success' => true,
            'message' => trans('User Logout successfully'),
        ]);
    }
}
