<?php namespace Pensoft\Mailsadministration\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Illuminate\Support\Facades\DB;
use Pensoft\Mailsadministration\Models\Groups as GroupsModel;

class Groups extends Controller
{
    public $implement = [        'Backend\Behaviors\ListController',        'Backend\Behaviors\FormController'    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
    }

    public function onSync() {
        //update from alias table
        $aliasData = DB::connection('vmail')->select('SELECT * FROM alias a LEFT JOIN list_replace_options lro ON lro.list_address = a.address WHERE a.domain = \'' . trim(env('GROUPS_DOMAIN') ?? $_SERVER['SERVER_NAME']) . '\' AND a.islist = 1');
        if(count($aliasData)){
            foreach($aliasData as $alias){
                $groups = GroupsModel::where('address', $alias->address)->first();

                if(!$groups){
                    $groups = new GroupsModel();
                }
                $groups->address = $alias->address;
                $groups->goto = $alias->goto;
                $groups->group_moderators = $alias->moderators;
                $groups->accesspolicy = $alias->accesspolicy;
                $groups->domain = $alias->domain;
                $groups->active = (int)$alias->active;
                $groups->reply_to = $alias->reply_to;
                $groups->replace_from = $alias->replace_from;
                $groups->replace_to = $alias->replace_to;
                $groups->name_append = $alias->name_append;
                $groups->add_reply_to = $alias->add_reply_to;
                $groups->save();
            }
        }
        \Flash::info('Synced with vmail!');
        return $this->asExtension('ListController')->listRefresh();

    }

    public function formExtendFields($form){
        $backendUser = \BackendAuth::getUser();
        if(!$backendUser->is_superuser) {
            $form->removeField('replace_from');
            $form->removeField('replace_to');
            $form->removeField('name_append');
            $form->removeField('add_reply_to');
            $form->addFields([
                'address' => [
                    'label' => 'Address',
                    'span' => 'full',
                    'type' => 'text',
                    'attributes' => [
                        'disabled' => true
                    ]
                ]
            ]);

            $form->addFields([
                'use_moderators_type' => [
                    'label' => 'Use moderators type',
                    'span' => 'auto',
                    'type' => 'radio',
                    'required' => 1,
                    'default' => 1,
                    'options' => [
                        1 => 'All Moderators',
                        2 => 'Group Moderators'
                    ],
                    'attributes' => [
                        'disabled' => true
                    ]
                ]
            ]);

            $form->addFields([
                'accesspolicy' => [
                    'label' => 'Accesspolicy',
                    'span' => 'auto',
                    'type' => 'dropdown',
                    'options' => [
                        'public' => 'public',
                        'membersAndModeratorsOnly' => 'membersAndModeratorsOnly'
                    ],
                    'attributes' => [
                        'disabled' => true
                    ]
                ]
            ]);

//            $form->addFields([
//                'all_moderators' => [
//                    'label' => 'All moderators',
//                    'mode' => 'string',
//                    'separator' => 'comma',
//                    'customTags' => true,
//                    'useKey' => true,
//                    'span' => 'full',
//                    'type' => 'taglist',
//                    'comment' => 'All the group emails that use all moderators field will be updated with this value!',
//                    'dependsOn' => 'use_moderators_type',
//                    'trigger' => [
//                        'action' => 'hide',
//                        'field' => 'use_moderators_type',
//                        'condition' => 'value[2]',
//                    ]
//                ]
//            ]);
        }
    }

    public function formExtendModel($model){
        if ($this->formGetContext() === 'create') {
            $allModeratorsValue = GroupsModel::where('use_moderators_type', 1)->first();
            $model->all_moderators = $allModeratorsValue->all_moderators;
            $this->domain = env('GROUPS_DOMAIN') ?? $_SERVER['SERVER_NAME'];
        }
    }
}
