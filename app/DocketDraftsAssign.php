<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocketDraftsAssign extends Model
{
    protected $table="docket_drafts_assign";

    protected $fillable = [
        'assign_docket_user_id',
        'docket_id',
        'docket_draft_id',
        'user_id',
        'machine_id'
    ];
}
