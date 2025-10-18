<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemBookSpecsModel extends Model
{
    protected $table          = 'item_book_specs';
    protected $primaryKey     = 'item_id';      // 1:1 with items
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    // Set to true only if your table actually has created_at/updated_at columns
    protected $useTimestamps  = false;
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';

    protected $allowedFields = [
        'item_id',
        'title',
        'author',
        'publisher',
        'publication_year',   // e.g., "1943" or "c.1943"
        'edition_label',      // e.g., "1st ed.", "Revised"
        'binding_type',       // e.g., "Hardcover", "Softcover"
        'language_label',     // e.g., "English", "German"
        'pages_count',
        'condition_notes',
    ];

    protected $validationRules = [
        'item_id'         => 'required|is_natural_no_zero',
        'title'           => 'permit_empty|max_length[255]',
        'author'          => 'permit_empty|max_length[255]',
        'publisher'       => 'permit_empty|max_length[255]',
        // accept 4-digit year or short freeform like "c.1943" up to 10 chars
        'publication_year'=> 'permit_empty|max_length[10]|regex_match[/^[0-9]{4}$|^.{1,10}$/]',
        'edition_label'   => 'permit_empty|max_length[50]',
        'binding_type'    => 'permit_empty|max_length[120]',
        'language_label'  => 'permit_empty|max_length[80]',
        'pages_count'     => 'permit_empty|integer|greater_than_equal_to[0]',
        'condition_notes' => 'permit_empty',
    ];

    protected $validationMessages = [
        'publication_year' => [
            'regex_match' => 'Use a 4-digit year (e.g., 1943) or a short note like "c.1943".',
        ],
    ];
}
