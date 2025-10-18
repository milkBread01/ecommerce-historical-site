<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemImageModel extends Model
{
    protected $table        = 'item_images';
    protected $primaryKey   = 'image_id';
    protected $returnType   = 'array';
    protected $useSoftDeletes = false;

    // Leave false unless your table actually has created_at/updated_at managed by CI
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'item_id',
        'file_path',      // e.g. uploads/products/123/main.jpg
        'url',            // optional CDN/external URL
        'title',
        'description',
        'alt_text',
        'image_order',    // integer sort within item
        'is_primary',     // tinyint(1)
        'width_px',
        'height_px',
        'checksum_sha1',
        'uploaded_at',    // if your schema stores this timestamp
    ];

    protected $validationRules = [
        'item_id'      => 'required|is_natural_no_zero',
        'file_path'    => 'permit_empty|max_length[255]',
        'url'          => 'permit_empty|valid_url_strict|max_length[500]',
        'title'        => 'permit_empty|max_length[255]',
        'description'  => 'permit_empty',
        'alt_text'     => 'permit_empty|max_length[255]',
        'image_order'  => 'permit_empty|integer|greater_than_equal_to[0]',
        'is_primary'   => 'permit_empty|in_list[0,1]',
        'width_px'     => 'permit_empty|integer|greater_than_equal_to[0]',
        'height_px'    => 'permit_empty|integer|greater_than_equal_to[0]',
        'checksum_sha1'=> 'permit_empty|exact_length[40]|alpha_numeric',
        'uploaded_at'  => 'permit_empty|valid_date[Y-m-d H:i:s]',
    ];

    protected $validationMessages = [
        'url' => [
            'valid_url_strict' => 'Please provide a valid URL starting with http/https.',
        ],
        'checksum_sha1' => [
            'exact_length' => 'Checksum must be a 40-character SHA1 string.',
        ],
    ];
}
