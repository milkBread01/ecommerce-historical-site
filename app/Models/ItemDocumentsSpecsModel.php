<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemDocumentSpecsModel extends Model
{
    protected $table        = 'item_document_specs';
    protected $primaryKey   = 'item_id';   // 1:1 with items
    protected $returnType   = 'array';
    protected $useSoftDeletes = false;

    // Enable only if your table actually has timestamp columns
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'item_id',
        'doc_type',           // letter, ID, manual, photo, map, certificate, etc.
        'date_of_issue',      // freeform (e.g., "1943-05-12", "May 1943", "c.1943")
        'language_label',
        'content_format',     // handwritten|typed|printed|photo|map|other
        'signature_present',  // tinyint(1)
        'stamp_seal_present', // tinyint(1)
        'page_count',

    ];

    protected $validationRules = [
        'item_id'            => 'required|is_natural_no_zero',
        'doc_type'           => 'permit_empty|max_length[120]',
        'date_of_issue'      => 'permit_empty|max_length[50]',
        'language_label'     => 'permit_empty|max_length[80]',
        'content_format'     => 'permit_empty|in_list[handwritten,typed,printed,photo,map,other]',
        'signature_present'  => 'permit_empty|in_list[0,1]',
        'stamp_seal_present' => 'permit_empty|in_list[0,1]',
        'page_count'         => 'permit_empty|integer|greater_than_equal_to[0]',

    ];

    protected $validationMessages = [
        'content_format' => [
            'in_list' => 'Content format must be one of: handwritten, typed, printed, photo, map, other.',
        ],
    ];
}
