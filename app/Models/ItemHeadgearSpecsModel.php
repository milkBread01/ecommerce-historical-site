<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemHeadgearSpecsModel extends Model
{
    protected $table        = 'item_headgear_specs';
    protected $primaryKey   = 'item_id';   // 1:1 with items
    protected $returnType   = 'array';
    protected $useSoftDeletes = false;

    // Enable only if your table actually has timestamp columns
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'item_id',
        'headgear_type',     // visor cap, helmet, beret, field cap, etc.
        'insignia_present',  // tinyint(1)
        'liner_material',
        'size_marked',
        'chinstrap_present', // tinyint(1)
        'shell_material',
    ];

    protected $validationRules = [
        'item_id'           => 'required|is_natural_no_zero',
        'headgear_type'     => 'permit_empty|max_length[120]',
        'insignia_present'  => 'permit_empty|in_list[0,1]',
        'liner_material'    => 'permit_empty|max_length[120]',
        'size_marked'       => 'permit_empty|max_length[50]',
        'chinstrap_present' => 'permit_empty|in_list[0,1]',
        'shell_material'    => 'permit_empty|max_length[120]',
    ];

    protected $validationMessages = [];
}
