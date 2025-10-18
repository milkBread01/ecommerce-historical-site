<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemMedalSpecsModel extends Model
{
    protected $table        = 'item_medal_specs';
    protected $primaryKey   = 'item_id';   // 1:1 with items
    protected $returnType   = 'array';
    protected $useSoftDeletes = false;

    // Enable only if your table actually has timestamp columns
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'item_id',
        'type_label',              // medal, badge, ribbon bar, clasp...
        'campaign_award_name',
        'material',                // bronze, silver, enamel...
        'maker_manufacturer',
        'attachment_type',         // pinback, screwback, clasp, ribbon loop...
        'engravings',
        'serial_number',
        'ribbon_present',          // tinyint(1)
        'presentation_case_incl',  // tinyint(1)
    ];

    protected $validationRules = [
        'item_id'               => 'required|is_natural_no_zero',
        'type_label'            => 'permit_empty|max_length[100]',
        'campaign_award_name'   => 'permit_empty|max_length[255]',
        'material'              => 'permit_empty|max_length[120]',
        'maker_manufacturer'    => 'permit_empty|max_length[255]',
        'attachment_type'       => 'permit_empty|max_length[120]',
        'engravings'            => 'permit_empty|max_length[255]',
        'serial_number'         => 'permit_empty|max_length[120]',
        'ribbon_present'        => 'permit_empty|in_list[0,1]',
        'presentation_case_incl'=> 'permit_empty|in_list[0,1]',
    ];

    protected $validationMessages = [];
}
