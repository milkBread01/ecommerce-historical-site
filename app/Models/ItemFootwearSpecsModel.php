<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemFootwearSpecsModel extends Model
{
    protected $table        = 'item_footwear_specs';
    protected $primaryKey   = 'item_id';   // 1:1 with items
    protected $returnType   = 'array';
    protected $useSoftDeletes = false;

    // Enable only if your table actually includes these timestamp columns
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'item_id',
        'footwear_type',    // jackboot, ankle boot, officer boot, etc.
        'upper_material',   // leather, canvas, etc.
        'sole_material',
        'size_marked',
        'laces_present',    // tinyint(1)
        'straps_present',   // tinyint(1)
        'pair_matching',    // tinyint(1)
    ];

    protected $validationRules = [
        'item_id'         => 'required|is_natural_no_zero',
        'footwear_type'   => 'permit_empty|max_length[120]',
        'upper_material'  => 'permit_empty|max_length[120]',
        'sole_material'   => 'permit_empty|max_length[120]',
        'size_marked'     => 'permit_empty|max_length[50]',
        'laces_present'   => 'permit_empty|in_list[0,1]',
        'straps_present'  => 'permit_empty|in_list[0,1]',
        'pair_matching'   => 'permit_empty|in_list[0,1]',
    ];

    protected $validationMessages = [];
}
