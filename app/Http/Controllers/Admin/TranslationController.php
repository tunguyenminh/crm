<?php

namespace App\Http\Controllers\Admin;


use App\Classes\Common;
use Barryvdh\TranslationManager\Manager;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Barryvdh\TranslationManager\Models\Translation;
use Illuminate\Support\Collection;

use App\Classes\Reply;
use App\Models\User;


class TranslationController extends AdminBaseController
{
     /**
	 * UserController constructor.
	 */

    public function __construct(Manager $manager)
    {
        parent::__construct();

        $this->manager = $manager;

        $this->pageTitle = trans('module_settings.translationManager');
        $this->pageIcon = 'fa fa-language';
        $this->settingMenuActive = 'active';
        $this->translationManagerActive = 'active';
    }

    public function getIndex($group = null)
    {
        $locales = $this->manager->getLocales();
        $groups = Translation::groupBy('group');
        $excludedGroups = $this->manager->getConfig('exclude_groups');
        if($excludedGroups){
            $groups->whereNotIn('group', $excludedGroups);
        }

        $groups = $groups->select('group')->orderBy('group')->get()->pluck('group', 'group');
        if ($groups instanceof Collection) {
            $groups = $groups->all();
        }
        $groups = [''=>'Choose a group'] + $groups;
        $numChanged = Translation::where('group', $group)->where('status', Translation::STATUS_CHANGED)->count();


        $allTranslations = Translation::where('group', $group)->orderBy('key', 'asc')->get();
        $numTranslations = count($allTranslations);
        $translations = [];
        foreach($allTranslations as $translation){
            $translations[$translation->key][$translation->locale] = $translation;
        }

        $editUrl = $group != null ? action('\Barryvdh\TranslationManager\Controller@postEdit', [$group]) : '';

        return view('admin.settings.translations.locale', $this->data)
            ->with('translations', $translations)
            ->with('locales', $locales)
            ->with('groups', $groups)
            ->with('group', $group)
            ->with('numTranslations', $numTranslations)
            ->with('numChanged', $numChanged)
            ->with('editUrl', $editUrl)
            ->with('deleteEnabled', $this->manager->getConfig('delete_enabled'));
    }

    public function getView($group = null)
    {
        return $this->getIndex($group);
    }
}
