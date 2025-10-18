<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemUniformSpecsModel extends Model
{
    protected $table        = 'item_uniform_specs';
    protected $primaryKey   = 'item_id';   // 1:1 with items
    protected $returnType   = 'array';
    protected $useSoftDeletes = false;

    // Enable only if your table actually has these timestamp columns
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'item_id',
        'uniform_type',            // field tunic, parade jacket, overcoat...
        'rank_insignia_present',   // tinyint(1)
        'unit_patches_present',    // tinyint(1)
        'buttons_original',        // tinyint(1)
        'label_maker_tag',
        'size_marked',
        'measure_chest',
        'measure_sleeve',
        'measure_length',
        'lining_material',
        'damage_notes',
    ];

    protected $validationRules = [
        'item_id'               => 'required|is_natural_no_zero',
        'uniform_type'          => 'permit_empty|max_length[120]',
        'rank_insignia_present' => 'permit_empty|in_list[0,1]',
        'unit_patches_present'  => 'permit_empty|in_list[0,1]',
        'buttons_original'      => 'permit_empty|in_list[0,1]',
        'label_maker_tag'       => 'permit_empty|max_length[255]',
        'size_marked'           => 'permit_empty|max_length[50]',
        'measure_chest'         => 'permit_empty|max_length[50]',
        'measure_sleeve'        => 'permit_empty|max_length[50]',
        'measure_length'        => 'permit_empty|max_length[50]',
        'lining_material'       => 'permit_empty|max_length[120]',
        'damage_notes'          => 'permit_empty',
    ];

    protected $validationMessages = [
        'item_id' => [
            'required' => 'Item ID is required for uniform specs',
            'is_natural_no_zero' => 'Item ID must be a valid positive number'
        ],
        'rank_insignia_present' => [
            'in_list' => 'Rank insignia must be either Yes (1) or No (0)'
        ],
        'unit_patches_present' => [
            'in_list' => 'Unit patches must be either Yes (1) or No (0)'
        ],
        'buttons_original' => [
            'in_list' => 'Buttons original must be either Yes (1) or No (0)'
        ]
    ];
}
