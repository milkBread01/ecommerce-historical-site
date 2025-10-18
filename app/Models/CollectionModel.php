<?php

namespace App\Models;

use CodeIgniter\Model;

class CollectionModel extends Model
{
    protected $table          = 'collections';
    protected $primaryKey     = 'collection_id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $useTimestamps  = true;
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';

    protected $allowedFields = [
        'collection_name',
        'collection_slug',
        'collection_description',
        'is_bundle_only',     // 0 = items can be sold individually, 1 = set only
        'bundle_price',       // nullable
        'created_at',
        'updated_at',
    ];

    // -----------------------------
    // Validation
    // -----------------------------
    protected $validationRules = [
        'collection_name'        => 'required|min_length[2]|max_length[255]|is_unique[collections.collection_name,collection_id,{collection_id}]',
        'collection_slug'        => 'permit_empty|alpha_dash|max_length[255]|is_unique[collections.collection_slug,collection_id,{collection_id}]',
        'collection_description' => 'permit_empty|max_length[1000]',
        'is_bundle_only'         => 'permit_empty|in_list[0,1]',
        'bundle_price'           => 'permit_empty|decimal',
    ];

    protected $validationMessages = [
        'collection_slug' => [
            'alpha_dash' => 'Slug may contain only letters, numbers, dashes and underscores.',
            'is_unique'  => 'This slug is already in use.',
        ],
        'collection_name' => [
            'is_unique'  => 'A collection with this name already exists.',
        ],
    ];

    protected $beforeInsert = ['normalizeAndSlug'];
    protected $beforeUpdate = ['normalizeAndSlug'];

    protected function normalizeAndSlug(array $data)
    {
        if (! isset($data['data'])) return $data;
        $row = & $data['data'];

        // Normalize booleans
        if (array_key_exists('is_bundle_only', $row)) {
            $row['is_bundle_only'] = (int) !!$row['is_bundle_only'];
        }

        // Slugify if empty, or sanitize if provided
        if ((empty($row['collection_slug']) || trim((string)$row['collection_slug']) === '') && ! empty($row['collection_name'])) {
            $row['collection_slug'] = $this->slugify($row['collection_name']);
        } elseif (! empty($row['collection_slug'])) {
            $row['collection_slug'] = $this->slugify($row['collection_slug']);
        }

        // Ensure slug uniqueness with -2, -3, ...
        if (! empty($row['collection_slug'])) {
            $ignoreId = $row[$this->primaryKey] ?? null;
            $row['collection_slug'] = $this->uniqueSlug($row['collection_slug'], $ignoreId);
        }

        // Empty bundle_price -> NULL
        if (array_key_exists('bundle_price', $row) && $row['bundle_price'] === '') {
            $row['bundle_price'] = null;
        }

        return $data;
    }

    protected function slugify(string $text): string
    {
        $text = strtolower($text);
        $text = preg_replace('~[^a-z0-9]+~', '-', $text);
        return trim($text, '-') ?: 'collection';
    }

    protected function uniqueSlug(string $baseSlug, ?int $ignoreId = null): string
    {
        $slug  = $baseSlug;
        $count = 1;

        while (true) {
            $builder = $this->builder()->select($this->primaryKey)->where('collection_slug', $slug);
            if ($ignoreId) $builder->where($this->primaryKey . ' !=', $ignoreId);
            $exists = $builder->get(1)->getFirstRow('array');

            if (! $exists) return $slug;

            $count++;
            $slug = $baseSlug . '-' . $count;
        }
    }
}
