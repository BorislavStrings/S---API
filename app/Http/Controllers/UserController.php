<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Helpers\JSONResponse;

use App\Http\Requests;
use Response;
use JWTAuth;

class UserController extends Controller
{

    protected $repository = null;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        return JSONResponse::send(true, 'Strings API is working properly!', 200);
    }

    public function login(Request $request) {
        $response = [];
        $errors = [];
        $success = true;
        try {
            if ($request->has('facebook')) {
                $result = $this->repository->loginSocial($request, 'facebook');
            } else if ($request->has('linkedin')) {
                $result = $this->repository->loginSocial($request, 'linkedin');
            } else {
                $result = $this->repository->login($request);
            }
            if (!$result['success']) {
                $errors = $result['errors'];
                throw new \Exception('Incorrect Credentials');
            }

            $status_code = 200;
            $response = $result;
        } catch(\Exception $e) {
            if (empty($errors)) {
                $errors['error'] = 'Error Occurred';
            }

            $success = false;
            $status_code = 404;
            $response = $errors;
        } finally {
            return JSONResponse::send($success, $response, $status_code);
        }
    }

    public function register(Request $request) {
        $response = [];
        $errors = [];
        $success = true;

        try {
            $result = $this->repository->register($request);

            if (!$result['success']) {
                $errors = $result['errors'];
                throw new \Exception('Error Occurred');
            }

            $status_code = 200;
            $response = $result;
        } catch (\Exception $e) {
            if (empty($errors)) {
                $errors['error'] = 'Error Occurred';
                $status_code = 500;
            } else {
                $status_code = 200;
            }

            $response = $errors;
            $success = false;
        } finally {
            return JSONResponse::send($success, $response, $status_code);
        }
    }

    public function get(Request $request) {
        $status_code = 200;
        $result = $this->repository->get($request);
        if ($result['success']) {
            $data = $result;
        } else {
            $errors = $result['errors'];
            if (empty($errors)) {
                $errors['error'] = 'Error Occurred';
                $status_code = 500;
            }

            $data = $errors;
        }

        return JSONResponse::send($result['success'], $data, $status_code);
    }

    public function update(Request $request) {
        $response = [];
        $errors = [];
        $success = true;

        try {
            $result = $this->repository->update($request);

            if (!$result['success']) {
                $errors = $result['errors'];
                throw new \Exception('Error Occurred');
            }

            $status_code = 200;
            $response = $result;
        } catch (\Exception $e) {
            $status_code = 200;

            if (empty($errors)) {
                $errors['error'] = 'Error Occurred';
                $status_code = 500;
            }

            $response = $errors;
            $success = false;
        } finally {
            return JSONResponse::send($success, $response, $status_code);
        }
    }

    public function setCV(Request $request) {
        $result = $this->repository->setCV($request);

        if ($result['success']) {
            $status_code = 200;
            $data = $result['cv'];
        } else {
            $status_code = 400;
            $data = $result['errors'];
        }

        return JSONResponse::send($result['success'], $data, $status_code);
    }

    public function getCV() {
        $result = $this->repository->getCV();

        if ($result['success']) {
            $status_code = 200;
            $data = $result['cv'];
        } else {
            $status_code = 400;
            $data = $result['errors'];
        }

        return JSONResponse::send($result['success'], $data, $status_code);
    }

    public function getUserAppliedOffers() {
        $result = $this->repository->getAppliedOffers();
        $status_code = 200;

        if ($result['success']) {
            $data = $result;
        } else {
            $errors = $result['errors'];
            if (empty($errors)) {
                $status_code = 400;
                $errors['system_error'] = "System Error";
            }
            $data = $errors;
        }

        return JSONResponse::send($result['success'], $data, $status_code);
    }

    public function getUserSkills(Request $request) {
        $result = $this->repository->getUserSkills($request);
        $status_code = 200;

        if ($result['success']) {
            $data = $result;
        } else {
            $errors = $result['errors'];
            if (empty($errors)) {
                $status_code = 500;
                $errors['system_error'] = 'System Error';
            }

            $data = $errors;
        }

        return JSONResponse::send($result['success'], $data, $status_code);
    }

    public function setSkills(Request $request) {
        $response = [];
        $errors = [];
        $success = true;

        try {
            $result = $this->repository->setSkills($request);

            if (!$result['success']) {
                $errors = $result['errors'];
                throw new \Exception('Error Occurred');
            }

            $status_code = 200;
            $response = $result;
        } catch (\Exception $e) {
            if (empty($errors)) {
                $errors['error'] = 'Error Occurred';
            }

            $response = $errors;
            $success = false;
            $status_code = 404;
        } finally {
            return JSONResponse::send($success, $response, $status_code);
        }
    }

    public function setLocations(Request $request) {
        $response = [];
        $errors = [];
        $success = true;

        try {
            $result = $this->repository->setLocations($request);

            if (!$result['success']) {
                $errors = $result['errors'];
                throw new \Exception('Error Occurred');
            }

            $status_code = 200;
            $response = $result;
        } catch (\Exception $e) {
            if (empty($errors)) {
                $errors['error'] = 'Error Occurred';
            }

            $response = $errors;
            $success = false;
            $status_code = 404;
        } finally {
            return JSONResponse::send($success, $response, $status_code);
        }
    }

    public function getLocations() {
        $response = [];
        $errors = [];
        $success = true;

        try {
            $result = $this->repository->getLocations();

            if (!$result['success']) {
                $errors = $result['errors'];
                throw new \Exception('Error Occurred');
            }

            $status_code = 200;
            $response = $result['locations'];
        } catch (\Exception $e) {
            if (empty($errors)) {
                $errors['error'] = 'Error Occurred';
            }

            $response = $errors;
            $success = false;
            $status_code = 404;
        } finally {
            return JSONResponse::send($success, $response, $status_code);
        }
    }

    public function refreshToken()
    {
        $errors = [];
        $success = true;
        $token = null;

        try {
            $token = JWTAuth::getToken();
            if (!$token) {
                $errors['token'] = 'Missing Token';
                throw new \Exception('Missing Token');
            }

            $token = JWTAuth::refresh($token);
        } catch (\Exception $e) {
            $success = false;
        } finally {
            return compact('success', 'errors', 'token');
        }
    }
}
