<?php namespace Pensoft\Mailsadministration\Models;

use Illuminate\Support\Facades\DB;
use Model;

/**
 * Model
 */
class Groups extends Model
{
    use \October\Rain\Database\Traits\Validation;

    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];


    /**
     * @var string The database table used by the model.
     */
    public $table = 'pensoft_mailsadministration_groups';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
    public function beforeSave()
    {
        if (trim($this->address) != '') {
            $isValid = filter_var(trim($this->address), FILTER_VALIDATE_EMAIL); // boolean
            if(!$isValid){
                throw new \ValidationException([
                    'address' => $this->address. ' is not valid email address'
                ]);
            }
            $this->address = strtolower(trim($this->address));

        }else{
            throw new \ValidationException([
                'address' => 'Address is required!'
            ]);
        }

        $arrGoto = array();
        if ($this->goto != '') {
            $arrGoto = explode(',', $this->goto);
            foreach($arrGoto AS $gotoEmailAddress) {
                $isValid = filter_var($gotoEmailAddress, FILTER_VALIDATE_EMAIL); // boolean
                if(!$isValid){
                    throw new \ValidationException([
                        'goto' => $gotoEmailAddress. ' is not valid GOTO email'
                    ]);
                }
            }
            $arrGoto = array_map('strtolower', $arrGoto);
            $arrGoto = array_unique($arrGoto);
            $this->goto = implode(',', $arrGoto);

        }else{
            throw new \ValidationException([
                'goto' => 'Goto is required!'
            ]);
        }

        $arrReply = array();
        if ($this->reply_to != '') {
            $arrReply = explode(',', $this->reply_to);
            foreach($arrReply AS $replyEmailAddress) {
                $isValid = filter_var($replyEmailAddress, FILTER_VALIDATE_EMAIL); // boolean
                if(!$isValid){
                    throw new \ValidationException([
                        'reply_to' => $replyEmailAddress. ' is not valid REPLY TO email'
                    ]);
                }
            }
            $arrReply = array_map('strtolower', $arrReply);
            $arrReply = array_unique($arrReply);
            $this->reply_to = implode(',', $arrReply);
        }


        $arrReplaceFrom = array();
        if ($this->replace_from != '') {
            $arrReplaceFrom = explode(',', $this->replace_from);
            foreach($arrReplaceFrom AS $replaceFromEmailAddress) {
                $isValidReplaceFrom = filter_var($replaceFromEmailAddress, FILTER_VALIDATE_EMAIL); // boolean
                if(!$isValidReplaceFrom){
                    throw new \ValidationException([
                        'replace_from' => $replaceFromEmailAddress. ' is not valid REPLACE FROM email'
                    ]);
                }
            }
            $arrReplaceFrom = array_map('strtolower', $arrReplaceFrom);
            $arrReplaceFrom = array_unique($arrReplaceFrom);
            $this->replace_from = implode(',', $arrReplaceFrom);
        }

        $arrReplaceTo = array();
        if ($this->replace_to != '') {
            $arrReplaceTo = explode(',', $this->replace_to);
            foreach($arrReplaceTo AS $replaceToEmailAddress) {
                $isValidReplaceTo = filter_var($replaceToEmailAddress, FILTER_VALIDATE_EMAIL); // boolean
                if(!$isValidReplaceTo){
                    throw new \ValidationException([
                        'replace_to' => $replaceToEmailAddress. ' is not valid REPLACE TO email'
                    ]);
                }
            }
            $arrReplaceTo = array_map('strtolower', $arrReplaceTo);
            $arrReplaceTo = array_unique($arrReplaceTo);
            $this->replace_to = implode(',', $arrReplaceTo);
        }


        /**
         * Update moderators field
         * if use all moderators is checked -> get all groups using AM and update their all_moderators field too
         * if use all moderators is checked -> on VMAIL get all groups using AM and update their moderators field too
         * if use group moderators is checked -> update group_moderators field of current group only
         * if use group moderators is checked -> on VMAIL update current group list only
         */

        switch ($this->use_moderators_type){
            case 1: // all
            default:
                $arrModerators = array();
                if ($this->all_moderators != '') {
                    $arrModerators = explode(',', $this->all_moderators);
                    foreach($arrModerators AS $moderatorEmailAddress) {
                        $isValid = filter_var($moderatorEmailAddress, FILTER_VALIDATE_EMAIL); // boolean
                        if(!$isValid){
                            throw new \ValidationException([
                                'all_moderators' => $moderatorEmailAddress. ' is not valid Moderators email'
                            ]);
                        }
                    }
                    $arrModerators = array_map('strtolower', $arrModerators);
                    $arrModerators = array_unique($arrModerators);
                    $this->all_moderators = implode(',', $arrModerators);

                }else{
                    throw new \ValidationException([
                        'all_moderators' => 'All moderators is required!'
                    ]);
                }
                $allModerators = array_unique(array_merge(array('root@psweb.pensoft.net', 'messaging@pensoft.net'), $arrModerators));
                $allModerators = array_map('strtolower', $allModerators);
                $this->all_moderators = implode(',', $allModerators);

                Groups::where('use_moderators_type', 1)
                ->update([
                    'all_moderators' => $this->all_moderators
                ]);

                break;
            case 2: // group
                $arrModerators = array();
                if ($this->group_moderators != '') {
                    $arrModerators = explode(',', $this->group_moderators);
                    foreach($arrModerators AS $moderatorEmailAddress) {
                        $isValid = filter_var($moderatorEmailAddress, FILTER_VALIDATE_EMAIL); // boolean
                        if(!$isValid){
                            throw new \ValidationException([
                                'group_moderators' => $moderatorEmailAddress. ' is not valid Moderators email'
                            ]);
                        }
                    }
                    $arrModerators = array_map('strtolower', $arrModerators);
                    $arrModerators = array_unique($arrModerators);
                    $this->group_moderators = implode(',', $arrModerators);
                }else{
                    throw new \ValidationException([
                        'group_moderators' => 'Group moderators is required!'
                    ]);
                }
                $groupModerators = array_unique(array_merge(array('root@psweb.pensoft.net', 'messaging@pensoft.net'), $arrModerators));
                $groupModerators = array_map('strtolower', $groupModerators);
                $this->group_moderators = implode(',', $groupModerators);
                $this->all_moderators = '';
                break;
        }


        $this->domain = env('GROUPS_DOMAIN') ?? $_SERVER['SERVER_NAME'];

        // make sure we've got a valid email
        $isValidGroupEmail = filter_var($this->address, FILTER_VALIDATE_EMAIL); // boolean
        if(!$isValidGroupEmail){
            throw new \ValidationException([
                'address' => $this->address. ' is not valid GROUP email'
            ]);
        }

        $groupEmailDomain = substr(strrchr($this->address, "@"), 1);
        if($groupEmailDomain != $this->domain){
            throw new \ValidationException([
                'address' => $this->address. ' domain doesn\'t match the site domain '.$this->domain
            ]);
        }
        $backendUser = \BackendAuth::getUser();
        if(!$backendUser->is_superuser && $this->id == null) { //if create and not superuser
            $this->replace_from = $this->address;
        }
    }

    public function afterSave()
    {
        $groupEmail = $this->address;
        $groupMembers = $this->goto;
        $groupDomain = $this->domain;
        $replyTo = $this->reply_to;
        $active = $this->active;
        $accesspolicy = $this->accesspolicy;


        switch ($this->use_moderators_type){
            case 1:// all
            default:
                $groupModerators = $this->all_moderators;
                $emals = Groups::where('use_moderators_type', 1)->get('address')->toArray();
                $allEmails = implode(',', array_column($emals, 'address'));
                DB::connection('vmail')->select('UPDATE alias SET moderators = \'' . trim($groupModerators) . '\' WHERE domain = \'' . $groupDomain . '\' AND address = ANY(\'{' . $allEmails . '}\')');
                break;
            case 2:
                $groupModerators = $this->group_moderators;
                break;
        }
        DB::connection('vmail')->select('SELECT * FROM savemailgroup(\'' . $groupEmail . '\', \'' . trim($groupMembers) . '\', \'' . $groupDomain . '\',  \'' . trim($groupModerators) . '\',  \'' . trim($replyTo) . '\', ' . (int)$active . ',  \'' . trim($accesspolicy) . '\')');

        $replaceFrom = $this->replace_from;
        $replaceTo = $this->replace_to;
        $nameAppend = $this->name_append;
        $addReplyTo = $this->add_reply_to;

        DB::connection('vmail')->select('SELECT * FROM savereplaceoptions(\'' . $groupEmail . '\', \'' . trim($replaceFrom) . '\', \'' . trim($replaceTo) . '\', \'' . trim($nameAppend) . '\', \'' . trim($addReplyTo) . '\', ' . (int)$active . ')');

        return \Redirect::refresh();
    }

}
