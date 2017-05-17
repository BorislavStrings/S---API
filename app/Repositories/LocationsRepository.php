<?php
namespace App\Repositories;
use App\Models\Locations;
use Auth;
use Validator;


class LocationsRepository extends BaseRepository {

    public function __construct(Locations $model)
    {
        $this->model = $model;
    }

    private function validatorLocation(&$request)
    {
        return Validator::make($request, [
            'name' => 'required|max:255',
            'lng' => 'numeric',
            'lat' => 'numeric',
            'place_id' => 'string'
        ]);
    }

    public function destroy($id)
    {
        $errors = [];
        $success = true;
        try {
            $user = Auth::user();

            if (!$user || $user->id < 1) {
                throw new \Exception('Incorrect User');
            }

            $this->model->where(['id' => $id, 'user_id' => $user->id])->delete();
        } catch (\Exception $e) {
            $success = false;
        } finally {
            return compact('success', 'errors');
        }
    }

    public function set(&$data)
    {
        $success = true;
        $id = 0;
        $errors = [];
        $entry = null;

        try {
            $validator = $this->validatorLocation($data);
            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                throw new \Exception('Incorrect Data');
            }

            $locations = new Locations();
            $locations->name = $data['name'];
            $locations->lng = isset($data['lng']) ? $data['lng'] : '';
            $locations->lat = isset($data['lat']) ? $data['lat'] : '';
            $locations->place_id = isset($data['place_id']);
            $locations->save();

            $entry = $locations;
            $id = $entry->id;
        } catch (\Exception $e) {
            $success = false;
            $id = 0;
            $entry = null;
        } finally {
            return compact('success', 'errors', 'id', 'entry');
        }
    }

    public function get()
    {
        $locations = null;

        try {
            $user = Auth::user();
            $locations = Locations::where(['user_id' => $user->id])->get();
        } catch (\Exception $e) {
            $locations = null;
        } finally {
            return $locations;
        }
    }
}