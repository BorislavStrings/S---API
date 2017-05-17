<?php
namespace App\Repositories;
use App\Repositories\FilesRepository;
use App\Repositories\LocationsRepository;
use App\Models\User;
use App\Models\UserTechSkills;
use App\Models\UserCV;
use App\Models\UserLocations;
use App\Models\UserApply;
use JWTAuth;
use Validator;
use Hash;
use Auth;
use Socialite;
use DB;
use League\Flysystem\Exception;

class UserRepository extends BaseRepository
{
    /**
     * Create a new UserRepository instance.
     *
     * @param  App\Models\User $user
     * @return void
     */
    public function __construct(User $user, FilesRepository $file_repository,
                                LocationsRepository $location_repository,
                                DevicesRepository $devices_repository)
    {
        $this->model = $user;
        $this->file_repository = $file_repository;
        $this->location_repository = $location_repository;
        $this->devices_repository = $devices_repository;
    }
    /**
     * Save the User.
     *
     * @param  App\Models\User $user
     * @param  Array  $inputs
     * @return void
     */

    private function validatorRegister(&$request)
    {
        return Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed|',
            //'profession' => 'string|max:255',
            //'phone' => 'string|max:255',
            //'image' => 'image|max:5120'
        ]);
    }

    private function validatorLocation(&$location)
    {
        return Validator::make($location, [
            'name' => 'required|max:255',
            'type' => 'required|in:job,home'
        ]);
    }

    private function validatorCV(&$request)
    {
        return Validator::make($request->all(), [
            'cv' => 'required|max:5120|mimes:jpg,jpeg,png,gif,bmp,zip,rar,txt,sql,doc,docx,xls,xlsx,pdf,xsl,txt,html,htm'
        ]);
    }

    private function validatorTechSkills(&$items)
    {
        return Validator::make($items, [
            'skill_id' => 'required|integer|exists:tech_skills,id',
            'level_id' => 'integer|exists:tech_skills_levels,id',
            'min_experience' => 'integer|min:0',
            'max_experience' => 'integer|min:0',
        ]);
    }

    private function validatorLogin(&$request)
    {
        return Validator::make($request->all(), [
            'email' => 'required|email|max:255|',
            'password' => 'required|min:6|'
        ]);
    }

    private function createToken($user) {
        $token = null;

        if ($user) {
            $token = JWTAuth::fromUser($user);
        }

        return $token;
    }

    public function register(&$request)
    {
        $user = null;
        $errors = [];
        $token = null;
        $success = true;

        try {
            $validator = $this->validatorRegister($request);
            if ($validator->fails()) {
                $errors = $validator->errors();
                throw new \Exception('Incorrect Data');
            }

            DB::beginTransaction();
            //set image file
            $file_id = null;
            if (!empty($request->file('image'))) {
                $file = $this->file_repository->add($request->file('image'));
                if (!$file['success'] || $file['id'] < 1) {
                    $errors['image'] = 'Incorrect File';
                    throw new \Exception('Incorrect File');
                }
                $file_id = $file['id'];
            }

            $this->model->name = $request->input('name');
            $this->model->email = $request->input('email');
            $this->model->password = bcrypt($request->input('password'));
            $this->model->profession = $request->has('profession') ? $request->input('profession')  : '';
            $this->model->phone = $request->has('phone') ? $request->input('phone')  : '';
            $this->model->image_id = $file_id;

            $this->model->save();

            $token = $this->createToken($this->model);
            if (!$token) {
                $errors['token'] = 'Token Error';
                throw new \Exception('Incorrect Token');
            }

            $user = $this->model;
            if (!$user) {
                $errors['user'] = 'User Error';
                throw new \Exception('Incorrect User');
            }
            DB::commit();
        } catch (\Exception $e) {
            $success = false;
            DB::rollback();
        } finally {
            return compact('success', 'user', 'errors', 'token');
        }
    }

    private function setTechSkills($data) {
        $errors = [];
        $success = true;

        try {
            if ($data) {
                $data = json_decode($data, true);
                foreach ($data AS $inx => $row) {
                    $validator = $this->validatorTechSkills($row);
                    if ($validator->fails()) {
                        $errors[$inx] = $validator->errors()->all();
                    }

                    if (!empty($errors)) {
                        throw new \Exception('Incorrect Data');
                    }
                }
            }

            $user = Auth::user();
            $user_skills = UserTechSkills::where(['user_id' => $user->id])->get();
            $items = $this->intersectCollections($user_skills, $data, 'App\Models\UserTechSkills', 'skill_id');

            if (!$items['success']) {
                $errors = $items['errors'];
                throw new Exception('Parameters Error');
            }

            if (!empty($items['data']['update_items'])) {
                foreach ($items['data']['update_items'] AS $item) {
                    $item->save();
                }
            }

            if (!empty($items['data']['insert_items'])) {
                foreach ($items['data']['insert_items'] AS $item) {
                    $item->user_id = $user->id;
                    $item->save();
                }
            }

            if (!empty($items['data']['delete_items'])) {
                foreach ($items['data']['delete_items'] AS $item) {
                    $item->delete();
                }
            }

        } catch (\Exception $e) {
            $success = false;
        } finally {
            return compact('success', 'errors');
        }
    }

    public function setSkills(&$request)
    {
        $errors = [];
        $success = true;

        try {
            DB::beginTransaction();

            if ($request->has('tech_skills')) {
                $result = $this->setTechSkills($request->input('tech_skills'));
                if (!$result['success']) {
                    $errors = $result['errors'];
                    throw new \Exception('Tech Skills Error');
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $success = false;
        } finally {
            return compact('success', 'errors');
        }
    }

    /**
     * Update a user.
     *
     * @param  array  $inputs
     * @param  App\Models\User $user
     * @return void
     */
    public function update($inputs)
    {
        $user_auth = Auth::user();
        $user = User::findOrFail($user_auth->id);
    }

    /**
     * Valid user.
     *
     * @param  bool  $valid
     * @param  int   $id
     * @return void
     */
    public function valid($id)
    {
        if ($this->getById($id)) {
            return true;
        }

        return false;
    }

    /**
     * Login user.
     *
     * @param  string   $email
     * @param  string   $password
     * @return User
     */
    public function login(&$request)
    {
        $errors = [];
        $token = null;
        $user = null;
        $success = true;
        try {
            $validator = $this->validatorLogin($request);
            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                throw new \Exception('Incorrect User Input');
            }

            $user = $this->model->where([
                'email' => $request->input('email')
            ])->first();

            if (!$user || !Hash::check($request->input('password'), $user->password)) {
                $errors['credentials'] = 'Incorrect Credentials';
                throw new \Exception('Incorrect Credentials');
            }

            $token = $this->createToken($user);

            if (!$token) {
                $errors['token'] = 'Token Error';
                throw new \Exception('Incorrect Token');
            }

            if ($request->has('device_data')) {
                $result = $this->setDevice($request->input('device_data'));
                if (!$result['success']) {
                    $errors['device_data'] = $result['errors'];
                }
            }

        } catch (\Exception $e) {
            $success = false;
        } finally {
            return compact('success', 'errors', 'token', 'user');
        }
    }

    private function setDevice(&$data)
    {
        return $this->devices_repository->set($data);
    }

    public function loginSocial(&$request, $network_type)
    {
        $errors = [];
        $token = null;
        $user = null;
        $success = true;
        try {
            if (!$request->has('access_token') || !$request->input('access_token')) {
                $errors['access_token'] = 'Incorrect Facebook Token';
                throw new \Exception('Incorrect Input');
            }

            $social_user = Socialite::driver($network_type)->userFromToken($request->input('access_token'));

            if (!$social_user || isset($social_user['error']) ||
                empty($social_user['email']) || filter_var($social_user['email'], FILTER_VALIDATE_EMAIL) === false) {
                $errors['credentials'] = 'Incorrect Credentials';
                throw new \Exception('Incorrect Credentials');
            }

            //Search for the user in the database
            $db_user = $this->model->where([
                'email' => $social_user['email']
            ])->first();

            //If user exists -> Login, Else Register and Login
            $user = $db_user;
            if (!$db_user) {
                DB::beginTransaction();

                //set image file
                $file_id = null;
                if (!empty($social_user['avatar_original'])) {
                    $info = pathinfo($social_user['avatar_original']);
                    $ext = $info['extension'];

                    $file = $this->file_repository->addLink($ext, $social_user['avatar_original'], 1);
                    if (!$file['success'] || $file['id'] < 1) {
                        $errors['image'] = 'Incorrect File';
                        throw new \Exception('Incorrect File');
                    }
                    $file_id = $file['id'];
                }

                $this->model->name = isset($social_user['name']) ? $social_user['name'] : '';
                $this->model->email = $social_user['email'];
                $this->model->image_id = $file_id;
                $this->model->save();

                $user = $this->model;
            }

            $token = $this->createToken($user);
            if (!$token) {
                $errors['token'] = 'Token Error';
                throw new \Exception('Incorrect Token');
            }

            DB::commit();
        } catch (\Exception $e) {
            $success = false;
            DB::rollback();
        } finally {
            return compact('success', 'errors', 'token', 'user');
        }
    }

    public function get($request)
    {
        $user = null;
        $errors = [];
        $success = true;

        try {
            $user_auth = Auth::user();
            $parameters = ['image', 'cv'];

            if ($request->has('all')) {
                $parameters = array_merge($parameters, ['techSkills', 'languages', 'softSkills', 'locations']);
            } else {
                if ($request->has('tech_skills')) {
                    $parameters[] = 'techSkills';
                }

                if ($request->has('languages')) {
                    $parameters[] = 'languages';
                }

                if ($request->has('soft_skills')) {
                    $parameters[] = 'softSkills';
                }

                if ($request->has('locations')) {
                    $parameters[] = 'locations';
                }
            }

            if ($parameters) {
                $user = User::with($parameters)->findOrFail($user_auth->id);
            } else {
                $user = User::findOrFail($user_auth->id);
            }
        } catch (\Exception $e) {
            $success = false;
            $errors['user'] = 'Error Occurred';
        } finally {
            return compact('success', 'errors', 'user');
        }
    }

    public function getUserSkills($request)
    {
        $skills = [];
        $errors = [];
        $success = true;

        try {
            $user_auth = Auth::user();
            $skills = User::with(['techSkills', 'languages', 'softSkills'])->findOrFail($user_auth->id);
        } catch (\Exception $e) {
            $success = false;
            $errors['skills'] = 'Error Occurred';
        } finally {
            return compact('success', 'errors', 'skills');
        }
    }

    public function getCV()
    {
        $cv = null;
        $errors = [];
        $success = true;

        try {
            $user = Auth::user();
            $cv = UserCV::where(['user_id' => $user->id])->with('file')->orderBy('version', 'desc')->first();
        } catch (\Exception $e) {
            $success = false;
        } finally {
            return compact('success', 'errors', 'cv');
        }
    }

    public function setCV(&$request)
    {
        $cv = null;
        $errors = [];
        $success = true;

        try {
            $validator = $this->validatorCV($request);
            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                throw new \Exception('Incorrect Data');
            }

            DB::beginTransaction();

            $file = $this->file_repository->add($request->file('cv'));
            if (!$file['success'] || $file['id'] < 1) {
                $errors['image'] = 'Incorrect File';
                throw new \Exception('Incorrect File');
            }
            $cv = $file['entry'];

            // set user image id
            $user = Auth::user();
            $version = 0;
            $last_version = UserCV::where(['user_id' => $user->id])->orderBy('version', 'desc')->first();
            if ($last_version) {
                $version = $last_version->version + 1;
            }

            $cv_obj = UserCV::create([
                'user_id' => $user->id,
                'file_id' => $file['id'],
                'version' => $version
            ]);

            DB::commit();
        } catch (\Exception $e) {
            $success = false;
            DB::rollback();
        } finally {
            return compact('success', 'errors', 'cv');
        }
    }

    public function setLocations($request)
    {
        $location = null;
        $errors = [];
        $success = true;

        try {
            $add_locations = [];
            $delete_locations = [];

            if ($request->has('locations_set') && !empty($request->input('locations_set'))) {
                $add_locations = $request->input('locations_set');
            }

            if ($request->has('locations_delete') && !empty($request->input('locations_delete'))) {
                $delete_locations = $request->input('locations_delete');
            }

            DB::beginTransaction();

            if ($delete_locations) {
                foreach ($delete_locations AS $loc) {
                    $result = $this->deleteLocation($loc);
                    if (!$result['success']) {
                        $errors = $result['errors'];
                        throw new \Exception('Incorrect Location Data');
                    }
                }
            }

            if ($add_locations) {
                foreach ($add_locations AS $loc) {
                    $validator = $this->validatorLocation($loc);
                    if ($validator->fails()) {
                        $errors = $validator->errors()->all();
                    }
                    $user = Auth::user();

                    if (!empty($errors) || $user->id < 1) {
                        throw new \Exception('Incorrect Data');
                    }

                    //set location
                    $location_id = null;
                    $result = $this->location_repository->set($loc);
                    if (!$result['success'] || $result['id'] < 1) {
                        $errors = $result['errors'];
                        throw new \Exception('Incorrect Location Data');
                    }
                    $location_id = $result['id'];
                    $location_repository = new UserLocations();
                    $location_repository->user_id = $user->id;
                    $location_repository->type = $loc['type'];
                    $location_repository->location_id = $location_id;
                    $location_repository->save();
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            $success = false;
            DB::rollback();
        } finally {
            return compact('success', 'errors');
        }
    }

    public function deleteLocation($id)
    {
        $errors = [];
        $success = true;
        try {
            $user = Auth::user();

            if (!$user || $user->id < 1) {
                $errors['user'] = 'User Error';
                throw new \Exception('Incorrect User');
            }

            UserLocations::where(['id' => $id, 'user_id' => $user->id])->delete();
        } catch (\Exception $e) {
            if (empty($errors)) {
                $errors['system'] = 'Error Occurred';
            }
            $success = false;
        } finally {
            return compact('success', 'errors');
        }
    }

    public function getLocations()
    {
        $locations = null;
        $errors = [];
        $success = true;

        try {
            $user = Auth::user();
            $locations = UserLocations::where(['user_id' => $user->id])->with('location')->get();
        } catch (\Exception $e) {
            $success = false;
        } finally {
            return compact('success', 'errors', 'locations');
        }
    }

    public function getAppliedOffers() {
        $offers = null;
        $errors = [];
        $success = true;

        try {
            $user = Auth::user();
            $offers = UserApply::where(['user_id' => $user->id, 'status' => 'applied'])->with('offer')->get();
        } catch (\Exception $e) {
            $success = false;
        } finally {
            return compact('success', 'errors', 'offers');
        }
    }
}