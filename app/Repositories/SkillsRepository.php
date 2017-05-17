<?php
namespace App\Repositories;

use App\Models\TechSkillsGroups;
use App\Models\TechSkills;
use App\Models\TechSkillsLevels;
use App\Models\SoftSkillsLevels;
use App\Models\Languages;
use App\Models\LanguagesLevels;
use App\Models\SoftSkills;

class SkillsRepository extends BaseRepository {

    public function __construct(TechSkills $skills)
    {
        $this->model = $skills;
    }

    public function all() {
        $skills = null;
        $errors = [];
        $success = true;

        try {
            $tech_skills = TechSkillsGroups::with('skills')->get();
            $tech_skills_levels = TechSkillsLevels::all();
            $soft_skills = SoftSkills::all();
            $soft_skills_levels = SoftSkillsLevels::all();
            $languages = Languages::all();
            $languages_levels = LanguagesLevels::all();

            $skills = [
                'tech_skills' => [
                    'items' => $tech_skills,
                    'levels' => $tech_skills_levels
                ],
                'soft_skills' => [
                    'items' => $soft_skills,
                    'levels' => $soft_skills_levels
                ],
                'languages' => [
                    'items' => $languages,
                    'levels' => $languages_levels
                ]
            ];
        } catch (\Exception $e) {
            $success = false;
            $skills = null;
        } finally {
            return compact('success', 'errors', 'skills');
        }
    }

}