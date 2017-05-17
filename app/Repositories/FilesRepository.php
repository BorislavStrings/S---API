<?php
namespace App\Repositories;
use League\Flysystem\Exception;
use Storage;
use File;
use App\Models\Files;

class FilesRepository extends BaseRepository {

    public function add($file)
    {
        $success = true;
        $id = 0;
        $entry = null;

        try {
            //$original_name = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $original_name = $file->getClientOriginalName();

            $result = $this->addLink($file->getClientMimeType(), $original_name, $extension);

            if (!$result['success']) {
                throw new \Exception('Database Error');
            }

            Storage::disk('local')->put($result['entry']->fileindex, File::get($file));

            $id = $result['id'];
            $entry = $result['entry'];
        } catch (\Exception $e) {
            $success = false;
            $id = 0;
        } finally {
            return compact('success', 'id', 'entry');
        }
    }

    public function addLink($mime, $original_name, $extension, $external = 0)
    {
        $success = true;
        $id = 0;
        $entry = null;
        $name = '';

        try {
            $name = md5(time() . time() . rand(10000, 99999)) . '_' . $original_name;

            $entry = new Files();
            $entry->mime = $mime;
            $entry->original_filename = $original_name;
            $entry->filename = $name;
            $entry->fileindex = $name;
            $entry->external = $external;
            $entry->save();

            $id = $entry->id;
        } catch (\Exception $e) {
            $success = false;
            $id = 0;
        } finally {
            return compact('success', 'id', 'entry');
        }
    }

    public function download($filename)
    {
        $file = null;
        try {
            $entry = Files::where('filename', '=', $filename)->firstOrFail();
            $file = Storage::disk('local')->get($entry->filename);
        } catch (\Exception $e) {
            $file = null;
        } finally {
            return (new Response($file, 200))->header('Content-Type', $entry->mime);
        }
    }

    public function get($id)
    {
        $file = null;
        try {
            $entry = Files::find($id)->firstOrFail();
            $file = Storage::disk('local')->get($entry->filename);
        } catch (\Exception $e) {
            $file = null;
        } finally {
            return $file;
        }
    }

}