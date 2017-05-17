<?php
namespace App\Repositories;
use App\Models\UsersDevices;


class DevicesRepository extends BaseRepository {

    public function __construct(UsersDevices $device)
    {
        $this->model = $device;
    }

    private function validatorDevices(&$items)
    {
        return Validator::make($items, [
            'device' => 'required|string',
            'os' => 'required|in:android,ios,null',
            'version' => 'numeric'
        ]);
    }

    public function deleteDevice($device)
    {
        $errors = [];
        $success = true;
        try {
            $user = Auth::user();

            if (!$user || $user->id < 1) {
                $errors['user'] = 'Incorrect User';
                throw new \Exception('Incorrect User');
            }

            if (!$device) {
                $errors['device'] = 'Incorrect Device ID';
                throw new \Exception('Incorrect Device ID');
            }

            $device_entity = $this->model->where(['user_id' => $user->id, 'device' => $device])->first();
            if ($device_entity) {
                $device_entity->is_deleted = 1;
                $device_entity->save();
            } else {
                $errors['device'] = 'Incorrect Device ID';
                throw new \Exception('Incorrect Device ID');
            }
        } catch (\Exception $e) {
            $success = false;
        } finally {
            return compact('success', 'errors');
        }
    }

    public function set(&$data)
    {
        $errors = [];
        $success = true;
        try {
            $user = Auth::user();

            if (!$user || $user->id < 1) {
                $errors['user'] = 'Incorrect User';
                throw new \Exception('Incorrect User');
            }

            $validator = $this->validatorDevices($data);
            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                throw new \Exception('Incorrect Data');
            }

            $device_entity = $this->model->where(['user_id' => $user->id, 'device' => $data['device']])->first();

            if ($device_entity) {
                if ($device_entity->is_deleted == 1) {
                    $device_entity->is_deleted = 0;
                    $device_entity->save();
                }
            } else {
                $this->model->device = $data['device'];
                $this->model->os = $data['os'];
                $this->model->version = $data['version'];
                $this->model->save();
            }
        } catch (\Exception $e) {
            $success = false;
        } finally {
            return compact('success', 'errors');
        }
    }

}