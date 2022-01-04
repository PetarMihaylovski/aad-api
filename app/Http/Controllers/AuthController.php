<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use App\Services\UserService;
use App\Services\ValidatorService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;


class AuthController extends Controller
{

    private $userService;
    private $authService;
    private $validatorService;

    public function __construct(UserService      $userService,
                                AuthService      $authService,
                                ValidatorService $validatorService)
    {
        $this->userService = $userService;
        $this->authService = $authService;
        $this->validatorService = $validatorService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       $this->validatorService->validate($request->all(), [
            'email' => 'required|unique:users|email',
            'username' => 'required|string',
            'password' => 'required',
            'address' => 'string',
            'postal' => 'string'
        ]);

        $user = $this->userService->createUser(
            $request['username'],
            $request['email'],
            $request['password'],
            $request['address'],
            $request['postal']
        );
        return response(new UserResource($user), ResponseAlias::HTTP_CREATED);
    }

    public function login(Request $request)
    {
         $this->validatorService->validate($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        //check email
        $user = $this->userService->getUserByEmail($request['email']);
        //check password
        $passwordCorrect = $this->authService->verifyPassword($request['password'], $user->password);

        if (!$user || !$passwordCorrect) {
            throw new CustomException("Wrong Credentials Provided",
                ResponseAlias::HTTP_UNAUTHORIZED);
        }

        // user is valid as of this point
        $token = $this->authService->generateToken($user);

        $response = [
            'token' => $token
        ];

        return response($response, ResponseAlias::HTTP_OK);
    }

    public function logout()
    {
        $this->userService->getAuthUser()->tokens()->delete();
        return response(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}
