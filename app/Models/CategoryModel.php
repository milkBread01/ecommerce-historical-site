<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\BaseBuilder;

class CategoryModel extends Model
{
    protected $table            = 'categories';
    protected $primaryKey       = 'category_id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    // If your table has these columns, keep them. Remove any that don't exist.

    /* 
Parents
    ('uniforms',                 'Uniforms',                 NULL, 10),
    ('headgear',                 'Headgear',                 NULL, 20),
    ('insignia-awards',          'Insignia & Awards',        NULL, 30),
    ('blades-edged-weapons',     'Blades & Edged Weapons',   NULL, 40),
    ('field-gear-accoutrements', 'Field Gear & Accoutrements', NULL, 50),
    ('documents-paper',          'Documents & Paper',        NULL, 60),
    ('books-manuals',            'Books & Manuals',          NULL, 70),
    ('collections-lots',         'Collections / Lots',       NULL, 80),
    ('misc',                     'Miscellaneous',            NULL, 90);
    */
    protected $allowedFields    = [
        'slug',
        'name',
        'sort_order',
        'parent_id',       // nullable
        'is_visible',      // tinyint(1) default 1
        'created_at',
        'updated_at',      // add this column if you want automatic updates
    ];

    protected $useTimestamps    = true;      // requires created_at/updated_at columns
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = null;

    // Validation
    protected $validationRules      = [
        'name'       => 'required|min_length[2]|max_length[255]',
        'slug'       => 'permit_empty|alpha_dash|max_length[100]|is_unique[categories.slug,category_id,{category_id}]',
        'parent_id'  => 'permit_empty|integer',
        'is_visible' => 'permit_empty|in_list[0,1]',
        'sort_order' => 'permit_empty|integer',
    ];
    protected $validationMessages   = [
        'slug' => [
            'alpha_dash' => 'Slug may only contain letters, numbers, dashes and underscores.',
            'is_unique'  => 'That slug is already in use.',
        ],
    ];

    protected $skipValidation   = false;

    // Callbacks: auto-generate slug if empty, normalize case
    protected $beforeInsert = ['ensureSlug'];
    protected $beforeUpdate = ['ensureSlug'];

    protected function ensureSlug(array $data)
    {
        if (! isset($data['data'])) {
            return $data;
        }

        $row = &$data['data'];

        // If slug not provided, generate from name
        if ((empty($row['slug']) || trim((string)$row['slug']) === '') && ! empty($row['name'])) {
            $row['slug'] = $this->slugify($row['name']);
        } else if (! empty($row['slug'])) {
            $row['slug'] = $this->slugify($row['slug']);
        }

        // Make sure slug is unique; if taken, add suffix -2, -3, …
        if (! empty($row['slug'])) {
            $row['slug'] = $this->uniqueSlug($row['slug'], $row[$this->primaryKey] ?? null);
        }

        // Normalize booleans
        if (isset($row['is_visible']))   $row['is_visible']   = (int) !!$row['is_visible'];
        if (isset($row['is_temporary'])) $row['is_temporary'] = (int) !!$row['is_temporary'];

        // Null parent_id if empty string
        if (isset($row['parent_id']) && ($row['parent_id'] === '' || $row['parent_id'] === null)) {
            $row['parent_id'] = null;
        }

        return $data;
    }

    protected function slugify(string $text): string
    {
        $text = strtolower($text);
        // Replace non alphanum with dashes
        $text = preg_replace('~[^a-z0-9]+~', '-', $text);
        $text = trim($text, '-');
        return $text ?: 'category';
    }

    protected function uniqueSlug(string $baseSlug, ?int $ignoreId = null): string
    {
        $slug  = $baseSlug;
        $count = 1;

        while (true) {
            $builder = $this->builder()->select($this->primaryKey)
                ->where('slug', $slug);

            if ($ignoreId) {
                $builder->where($this->primaryKey . ' !=', $ignoreId);
            }

            $exists = $builder->get(1)->getFirstRow('array');
            if (! $exists) {
                return $slug;
            }

            $count++;
            $slug = $baseSlug . '-' . $count;
        }
    }

    /* -------------------------------
     * Convenience finders
     * ----------------------------- */

    public function findBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)->first();
    }

    public function getChildren(int $parentId, bool $onlyVisible = true): array
    {
        $builder = $this->where('parent_id', $parentId)->orderBy('name', 'ASC');
        if ($onlyVisible && $this->fieldExists('is_visible')) {
            $builder->where('is_visible', 1);
        }
        return $builder->findAll();
    }

    public function getParent(int $categoryId): ?array
    {
        $cat = $this->find($categoryId);
        if (! $cat || empty($cat['parent_id'])) {
            return null;
        }
        return $this->find((int)$cat['parent_id']);
    }

    /**
     * Returns a breadcrumb array from root -> ... -> current
     * Each element: ['category_id' => ..., 'name' => ..., 'slug' => ...]
     */
    public function getBreadcrumb(int $categoryId): array
    {
        $trail = [];
        $current = $this->find($categoryId);

        while ($current) {
            array_unshift($trail, [
                'category_id' => $current['category_id'],
                'name'        => $current['name'],
                'slug'        => $current['slug'],
            ]);

            if (empty($current['parent_id'])) {
                break;
            }
            $current = $this->find((int)$current['parent_id']);
        }
        return $trail;
    }

    /**
     * Returns a tree of categories.
     * @param bool $onlyVisible Return only categories with is_visible=1 (if column exists)
     * @return array Nested tree: each node has 'children' => [...]
     */
    public function getTree(bool $onlyVisible = true): array
    {
        // Fetch all relevant rows
        $builder = $this->orderBy('name', 'ASC');
        if ($onlyVisible && $this->fieldExists('is_visible')) {
            $builder->where('is_visible', 1);
        }
        $rows = $builder->findAll();

        // Group by parent
        $byParent = [];
        foreach ($rows as $row) {
            $pid = $row['parent_id'] ?? null;
            $byParent[$pid][] = $row;
        }

        // Build recursively
        $build = function($parentId) use (&$build, &$byParent) {
            $branch = $byParent[$parentId] ?? [];
            foreach ($branch as &$node) {
                $node['children'] = $build($node['category_id']);
            }
            return $branch;
        };

        // Root nodes have parent_id = NULL
        return $build(null);
    }

    public function toggleVisibility(int $categoryId, bool $visible): bool
    {
        if (! $this->fieldExists('is_visible')) return false;
        return (bool) $this->update($categoryId, ['is_visible' => $visible ? 1 : 0]);
    }

    /* -------------------------------
     * Small utility
     * ----------------------------- */

    protected function fieldExists(string $field): bool
    {
        static $cache = [];

        if (! isset($cache[$this->table])) {
            $cache[$this->table] = array_map('strtolower', array_keys($this->getFieldData()));
        }
        return in_array(strtolower($field), $cache[$this->table], true);
    }
}
