<?php

namespace App\Http\Controllers\Api\V2;

use App\AssignedDocket;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{


    /**
     * @var UserRepository
     */
    private $userRepository;


    public function __construct(UserRepositoryInterface $userRepository)
    {
       $this->userRepository = $userRepository;
    }








}
