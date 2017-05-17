<?php

namespace App\Repositories;

abstract class BaseRepository {
    /**
     * The Model instance.
     *
     * @var Illuminate\Database\Eloquent\Model
     */
    protected $model;
    /**
     * Get number of records.
     *
     * @return array
     */
    public function getNumber()
    {
        return $this->model->count();
    }
    /**
     * Destroy a model.
     *
     * @param  int $id
     * @return void
     */
    public function destroy($id)
    {
        $this->getById($id)->delete();
    }
    /**
     * Get Model by id.
     *
     * @param  int  $id
     * @return App\Models\Model
     */
    public function getById($id)
    {
        return $this->model->findOrFail($id);
    }

    protected function intersectCollections($old_collection, $new_collection, $obj_class, $compare_inx = 'id') {
        $errors = [];
        $success = true;
        $data = [
            'insert_items' => [],
            'update_items' => [],
            'delete_items' => []
        ];

        try {
            $old_collection = collect($old_collection);
            $empty_old = $old_collection->isEmpty();
            $pull_inx = [];
            if (!empty($new_collection)) {
                foreach ($new_collection AS $inx_new => $new_item) {
                    // new item
                    if ($empty_old || !$old_collection->contains($compare_inx, $new_item[$compare_inx])) {
                        $insert_item = new $obj_class();
                        foreach ($new_item AS $inx => $value) {
                            //if (property_exists($insert_item, $inx)) {
                                $insert_item->{$inx} = $value;
                            //}
                        }
                        $data['insert_items'][] = $insert_item;
                    } else {
                        $old_collection->each(function($item, $key) use (&$pull_inx, $new_item, $compare_inx, &$data) {
                            if ($item->{$compare_inx} == $new_item[$compare_inx]) {
                                $pull_inx[] = $key;
                                foreach ($new_item AS $inx => $value) {
                                    //if (property_exists($update_item, $inx)) {
                                    $item->{$inx} = $value;
                                    //}
                                }
                                $data['update_items'][] = $item;

                                return false;
                            }
                        });
                    }
                }
            }

            //remove items for update
            if (!empty($pull_inx)) {
                foreach ($pull_inx AS $inx) {
                    $old_collection->pull($inx);
                }
            }

            $data['delete_items'] = $old_collection;
        } catch (\Exception $e) {
            $errors['parameters'] = 'Wrong Input';
            $success = false;
        } finally {
            return compact('success', 'errors', 'data');
        }
    }
}