<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\SkillsRepository;
use App\Http\Requests;
Use App\Helpers\JSONResponse;

class SkillsController extends Controller
{
    public function __construct(SkillsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function all() {
        $response = [];
        $errors = [];
        $success = true;

        try {
            $result = $this->repository->all();

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
}
