<?php
namespace App\Repositories;

use App\Models\JobOffers;
use App\Models\User;
use App\Models\JobOffersTechSkills;
use App\Models\UserTechSkills;
use App\Models\UserApply;
use Auth;
use DB;
use Mockery\CountValidator\Exception;

class JobOffersRepository extends BaseRepository {

    public function __construct(JobOffers $offers, UserApply $user_apply,
                                UserTechSkills $user_tech_skills, JobOffersTechSkills $offer_tech_skills)
    {
        $this->model = $offers;
        $this->apply_model = $user_apply;
        $this->user_tech_skills = $user_tech_skills;
        $this->offer_tech_skills = $offer_tech_skills;
    }

    public function getByUserSkills() {
        $errors = [];
        $offers = null;
        $success = true;

        try {
            $user = Auth::user();

            if (empty($user) || empty($user->id)) {
                $errors[] = 'User Error';
                throw new \Exception('Missing User');
            }

            $user_tech_skills = $this->user_tech_skills->where(['user_id' => $user->id])->get();

            /*
            if (!empty($user_tech_skills)) {

                $job_offers = DB::table($this->model->getTable())
                    ->join($this->offer_tech_skills->getTable(), $this->offer_tech_skills->getTable() . '.offer_id', '=', $this->model->getTable() . '.id')
                    ->groupBy($this->model->getTable() . '.id')
                    ->get();

                //$job_offers = $this->model->with(['techSkills', 'softSkills', 'languages', 'sections', 'locations'])->get();

                $job_offers = $this->model->with(['techSkills' => function($q) use ($user_tech_skills) {
                    $q->whereIn('skill_id', $user_tech_skills);
                }, 'softSkills', 'languages', 'sections', 'locations'])->has('techSkills')->where('active', '=', 1)->get();
            } else {
                $job_offers = $this->model
                    ->with(['techSkills', 'softSkills', 'languages', 'sections', 'locations'])
                    ->where('active', '=', 1)
                    ->get();
            }
            */

            $job_offers = $this->model
                ->with(['techSkills', 'image', 'softSkills', 'languages', 'sections', 'locations.location'])
                ->where('active', '=', 1)
                ->get();

            /*
            if (!$user_tech_skills->isEmpty()) {
                $job_offers = $job_offers->filter(function ($offer, $key) use ($user_tech_skills) {
                    $result = false;
                    $offer_skills = $offer->techSkills;
                    if ($offer_skills) {
                        foreach ($user_tech_skills AS $user_skill) {
                            foreach ($offer_skills AS $offer_skill) {
                                if ($user_skill->skill_id == $offer_skill->skill_id) {
                                    $result = true;
                                    break;
                                }
                            }

                            if ($result) {
                                break;
                            }
                        }
                    }

                    return $result;
                });
            }
            */

            $filtered_offers = [];

            if (!$job_offers->isEmpty()) {
                $job_offers = $job_offers->each(function ($offer, $inx) use ($user_tech_skills, &$filtered_offers) {
                    $intersect_count = 0;
                    $offer_tech_skills = $offer->techSkills;
                    $intersect_skills = 0;

                    if ($user_tech_skills && $offer_tech_skills) {
                        foreach ($offer_tech_skills AS $offer_skill) {
                            foreach ($user_tech_skills AS $user_skill) {
                                if ($offer_skill->skill_id == $user_skill->skill_id) {
                                    $intersect_count++;
                                    if ($offer_skill->level_id <= $user_skill->level_id) {
                                        $intersect_skills++;
                                    }
                                }
                            }
                        }
                    }

                    $offer['coefficient'] = $intersect_count;
                    $offer['intersect_skills'] = $intersect_skills;

                    if ($intersect_count > 0) {
                        $filtered_offers[] = $offer;
                    }
                });

                foreach (collect($filtered_offers)->sortByDesc('coefficient') AS $offer) {
                    $offers[] = $offer;
                }
            } else {
                $offers = [];
            }
        } catch (\Exception $e) {
            $success = false;
        } finally {
            return compact('success', 'errors', 'offers');
        }
    }

    public function all() {
        $errors = [];
        $offers = null;
        $success = true;
        try {
            $offers = $this->model
                ->with('image', 'techSkills', 'softSkills', 'sections', 'languages', 'locations.location')
                ->where(['active' => 1])
                ->get();
        } catch (\Exception $e) {
            echo $e->getMessage();exit;
            $success = false;
            $errors[] = 'Error Occurred';
        } finally {
            return compact('success', 'errors', 'offers');
        }
    }

    public function getOffer($offer_id)
    {
        $errors = [];
        $offer = null;
        $success = true;
        try {
            if ($offer_id < 1) {
                $errors['offer'] = 'Incorrect Offer ID';
            }

            $offer = $this->model
                ->with('image', 'techSkills', 'softSkills', 'sections', 'languages', 'locations.location')
                ->where(['active' => 1])
                ->find($offer_id);
        } catch (\Exception $e) {
            $success = false;
            $errors[] = 'Error Occurred';
        } finally {
            return compact('success', 'errors', 'offer');
        }
    }

    public function apply($offer_id)
    {
        $errors = [];
        $success = true;
        try {
            $user = Auth::user();
            if (!$user || empty($user->id)) {
                $errors['user'] = 'Incorrect User Data';
                throw new \Exception('Incorrect User Data');
            }

            if ($offer_id < 1) {
                $errors['offer'] = 'Missing Offer ID';
                throw new \Exception('Incorrect Offer ID');
            }

            $apply = $this->apply_model->where(['offer_id' => $offer_id, 'user_id' => $user->id])->first();

            if ($apply) {
                if ($apply->status != 'applied') {
                    $apply->status = 'applied';
                    $apply->save();
                }
            } else {
                $this->apply_model->user_id = $user->id;
                $this->apply_model->offer_id = $offer_id;
                $this->apply_model->status = 'applied';
                $this->apply_model->save();
            }
        } catch (\Exception $e) {
            $success = false;
        } finally {
            return compact('success', 'errors');
        }
    }

    public function disapply($offer_id)
    {
        $errors = [];
        $success = true;
        try {
            $user = Auth::user();

            if (!$user || empty($user->id)) {
                $errors['user'] = 'Incorrect User Data';
                throw new \Exception('Incorrect User Data');
            }

            if ($offer_id < 1) {
                $errors['offer'] = 'Missing Offer ID';
                throw new \Exception('Incorrect Offer ID');
            }

            $apply = $this->apply_model->where(['offer_id' => $offer_id, 'user_id' => $user->id])->first();

            if ($apply) {
                $apply->status = null;
                $apply->save();
            }
        } catch (\Exception $e) {
            $success = false;
        } finally {
            return compact('success', 'errors');
        }
    }
}