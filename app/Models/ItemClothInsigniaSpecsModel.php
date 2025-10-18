<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemClothInsigniaSpecsModel extends Model
{
    protected $table          = 'item_cloth_insignia_specs';
    protected $primaryKey     = 'item_id';   // 1:1 with items
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    // Enable only if your table actually has timestamp columns
    protected $useTimestamps  = false;
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';

    protected $allowedFields = [
        'item_id',
        'insignia_type',  // cloth patch, armband, shoulder board, collar tab, etc.
        'material',       // wool, cotton, bullion, embroidered, etc.
        'backing_type',   // cloth, paper, cardboard
        'stitch_type',    // hand, machine
        'damage_notes',   // text (fraying, stains, etc.)
    ];

    protected $validationRules = [
        'item_id'       => 'required|is_natural_no_zero',
        'insignia_type' => 'permit_empty|max_length[120]',
        'material'      => 'permit_empty|max_length[120]',
        'backing_type'  => 'permit_empty|max_length[120]',
        'stitch_type'   => 'permit_empty|max_length[120]',
        'damage_notes'  => 'permit_empty',
    ];

    protected $validationMessages = [];
}
