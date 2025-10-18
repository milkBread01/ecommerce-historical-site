<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemGearSpecsModel extends Model
{
    protected $table        = 'item_gear_specs';
    protected $primaryKey   = 'item_id';   // 1:1 with items
    protected $returnType   = 'array';
    protected $useSoftDeletes = false;

    // Enable only if your table actually has these timestamp columns
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'item_id',
        'gear_type',                // canteen, belt, pouch, gas mask, binoculars, compass, etc.
        'material',
        'maker_markings',
        'functionality',            // working | display only | unknown
        'completeness_notes',       // text about missing parts, etc.
        'straps_attachments_pres',  // tinyint(1)
    ];

    protected $validationRules = [
        'item_id'                => 'required|is_natural_no_zero',
        'gear_type'              => 'permit_empty|max_length[120]',
        'material'               => 'permit_empty|max_length[120]',
        'maker_markings'         => 'permit_empty|max_length[255]',
        'functionality'          => 'permit_empty|in_list[working,display only,unknown]',
        'completeness_notes'     => 'permit_empty',
        'straps_attachments_pres'=> 'permit_empty|in_list[0,1]',
    ];

    protected $validationMessages = [
        'functionality' => [
            'in_list' => 'Functionality must be one of: working, display only, unknown.',
        ],
    ];
}
