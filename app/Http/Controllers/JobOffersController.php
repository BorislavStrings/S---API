<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\JobOffersRepository;
use App\Http\Requests;
use App\Model;
use App\Helpers\JSONResponse;

class JobOffersController extends Controller {

    public function __construct(JobOffersRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getOffer($offer_id)
    {
        $response = [];
        $errors = [];
        $success = true;
        $status_code = 200;

        try {
            $result = $this->repository->getOffer($offer_id);

            if (!$result['success']) {
                $errors = $result['errors'];
                throw new \Exception('Error Occurred');
            }

            $response = $result['offer'];
        } catch (\Exception $e) {
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


    public function allSorted()
    {
        $response = [];
        $errors = [];
        $success = true;

        try {
            $result = $this->repository->getByUserSkills();

            if (!$result['success']) {
                $errors = $result['errors'];
                throw new \Exception('Error Occurred');
            }

            $status_code = 200;
            $response = $result['offers'];
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

    public function all()
    {
        $response = [];
        $errors = [];
        $success = true;
        $status_code = 200;

        try {
            $result = $this->repository->all();

            if (!$result['success']) {
                $errors = $result['errors'];
                throw new \Exception('Error Occurred');
            }

            $response = $result['offers'];
        } catch (\Exception $e) {
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

    public function apply($offer_id)
    {
        $response = [];
        $errors = [];
        $success = true;

        try {
            $result = $this->repository->apply($offer_id);

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

    public function disapply($offer_id)
    {
        $response = [];
        $errors = [];
        $success = true;

        try {
            $result = $this->repository->disapply($offer_id);

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
