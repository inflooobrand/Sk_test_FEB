<?php

namespace App\Repositories;

use App\Models\User;

// Exception
use App\Exceptions\UserNotFoundException;

class UserRepository
{
    /**
     * @type User
     */
    protected $userModel;

    /**
     * @method __construct
     */
    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * return all row 
     * @method all
     * @return Illuminate\Database\Eloquent\Collection  [\App\Models\User]
     */
    public function all(): ?User
    {
        return $this->userModel->all();
    }

    /**
     * find to row 
     * @method 
     * @param  integer          $id [int]
     */
    public function getUser($id): ?User
    {
        return $this->userModel->find($id);
    }

    /**
     * Stored row 
     * @method store
     * @param  Illuminate\Http\Request $request [App\Http\Requests]
     * @return bool
     */
    public function store($request): ?User
    {
        $request['password'] = bcrypt($request['password']);
        return $this->userModel->create($request);
    }

    /**
    * update user
    *
    * @method updateUser
    *
    * @param $userData, $user
    *
    * @return bool
    */
    public function updateUser(array $userData, object $user): ?bool
    {
        $userData = array_except($userData, ['password']);
        $userData['updated_at'] = now(config('constants.TIME_ZONE'));
        $userData['updated_by'] = auth()->user()->id ?? NULL;
        return $user->update($userData);
    }

    /**
     * Destroy row 
     * @method destroy
     * @param  integer  $id [int]
     * @return bool
     */
    public function destroy($id): ?bool
    {
        $this->userModel($id)->delete();

        return true;
    }

    /**
     * Attempt authentication
     *
     * @method attempt
     *
     * @param array $request
     *
     * @return bool
     *
    */
    public function attempt(array $request): bool
    {
        $credential = array('email' => $request['email'],'password' => $request['password']);
        $result = auth()->attempt($credential);
        if (!$result) {
            throw new UserNotFoundException();
        }
        return true;
    }

    /**
     * Create user auth token
     *
     * @method token
     *
     * @return string
     *
     */
    public function createAccessToken(object $user): string
    {
        $tokenResult = $user->createToken('access_token'); // create access token
        $accessToken = $tokenResult->accessToken;
        $token = $tokenResult->token;
        $token->expires_at = now()->addDays(config('constants.TOKEN_EXPIRY')); // set expiration
        $token->save();
        return $accessToken;
    }

    /**
    * Check user email exists
    *
    * @method checkEmailUserExists
    * 
    * @param $requestData
    *
    * @return int;
    *
    */

    public function checkEmailUserExists(array $requestData): ?int
    {
        return $this->userModel->where('email',$requestData['email'])->count();
    }   

    /**
     * List all records
     *
     * @method userList
     *
     * @return array
     *
    */
    public function userList(object $request): ?array
    {
        $filters = $request->get('filters');
        $filters['order_by'] = isset($filters['order_by'])?$filters['order_by']:'desc';
        $filters['sort_by'] = isset($filters['sort_by'])?$filters['sort_by']:'created_at';
        $method = $filters['order_by'] =='asc'?'sortBy':'sortByDesc';
        $filters['start'] = isset($filters['start'])?$filters['start']:0;
        $filters['limit'] = isset($filters['limit'])?$filters['limit']:50;
        
        if($request->filled('search')) {
            $search = $request->get('search');
            $list = $this->userModel->select('*')->where(function ($query) use ($search) {
                $searchFields = ['first_name','last_name','email','mobile'];
                foreach ($searchFields as $field) {
                    $query->orWhere($field, 'like', "%{$search}%");
                }
            })->get();
        }
        else {
            $list = $this->userModel->select('*')->get();
        }
        $count = $list->count();
        $filterList = $list->{$method}($filters['sort_by'])->skip($filters['start'])->take($filters['limit'])->values();
        $filterList = [
            'list' => $filterList,
            'total' => $count
        ];
        return $filterList;
    }
}
