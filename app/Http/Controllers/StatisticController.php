<?php

namespace App\Http\Controllers;

use App\Repositories\StatisticRepository;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Repository\DevicesRepository;

class StatisticController extends Controller
{
    public function __construct(StatisticRepository $repository,
                                DevicesRepository $device)
    {
        $this->repository = $repository;
        $this->repository_device = $device;
    }

    public function deleteDevice($device)
    {
        $response = [];
        $errors = [];
        $success = true;

        try {
            $result = $this->repository_device->deleteDevice($device);

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

    public function setStatistic(Request $request)
    {
        $response = [];
        $errors = [];
        $success = true;

        try {
            $result = $this->repository->setStatistic($request);

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
        //on item clicked
        //device, item_id, item name
    }
}
