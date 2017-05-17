<?php
namespace App\Repositories;
use App\Models\StatisticEvents;
use App\Models\StatisticEventsCategories;
use App\Models\UsersDevices;


class StatisticRepository extends BaseRepository {

    public function __construct(StatisticEvents $events,
                                StatisticEventsCategories $categories,
                                StatisticEventsCaptured $events_captured)
    {
        $this->model_events = $events;
        $this->model_categories = $categories;
        $this->model_events_captured = $events_captured;
    }

    private function validatorStatistic(&$items)
    {
        return Validator::make($items->all(), [
            'event_id' => 'required|numeric|exists:statistic_events,id',
            'device' => 'required|string|exists:users_devices,device'
        ]);
    }

    public function setStatistic($data)
    {
        $errors = [];
        $success = true;
        try {
            $user = Auth::user();

            if (!$user || $user->id < 1) {
                throw new \Exception('Incorrect User');
            }

            $validator = $this->validatorStatistic($data);
            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                throw new \Exception('Incorrect Data');
            }

            $this->model_events_captured->event_id = $data->input('event_id');
            $this->model_events_captured->device = $data->input('device');
            $this->model_events_captured->save();
        } catch (\Exception $e) {
            $success = false;
        } finally {
            return compact('success', 'errors');
        }
    }

}