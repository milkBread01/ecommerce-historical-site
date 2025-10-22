<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemModel extends Model
{
    protected $table            = 'items';
    protected $primaryKey       = 'item_id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'category_id',
        'collection_id',
        'sku',
        'name',
        'description',
        'video_url',
        'teaser',
        'slug',
        'price',
        'discounted_price',
        'on_sale',
        'is_featured',
        'visible',
        'stock_quantity',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    /* -------------------------------
     * Validation
     * ----------------------------- */
    protected $validationRules = [
        'category_id'     => 'required|integer',
        'collection_id'   => 'permit_empty|integer',
        'sku'             => 'permit_empty|max_length[64]',
        'name'            => 'required|min_length[2]|max_length[255]',
        'description'     => 'permit_empty|max_length[2000]',
        'video_url'       => 'permit_empty|valid_url_strict|max_length[500]',
        'teaser'          => 'permit_empty|max_length[500]',
        'slug'            => 'permit_empty|alpha_dash|max_length[255]',
        'price'           => 'required|decimal',
        'discounted_price'=> 'permit_empty|decimal',
        'on_sale'         => 'permit_empty|in_list[0,1]',
        'is_featured'     => 'permit_empty|in_list[0,1]',
        'visible'         => 'permit_empty|in_list[0,1]',
        'stock_quantity'  => 'required|integer|greater_than_equal_to[0]',
    ];

    protected $validationMessages = [
        'slug' => [
            'alpha_dash' => 'Slug may contain only letters, numbers, dashes, and underscores.',
            'is_unique'  => 'This slug is already in use.',
        ],
        'sku' => [
            'is_unique'  => 'This SKU is already assigned to another item.',
        ],
    ];

    protected $beforeInsert = ['ensureSlugAndNormalize'];
    protected $beforeUpdate = ['ensureSlugAndNormalize'];

    protected function ensureSlugAndNormalize(array $data)
    {
        if (! isset($data['data'])) {
            return $data;
        }

        $row = &$data['data'];

        // Generate slug from name if not provided
        if ((empty($row['slug']) || trim((string)$row['slug']) === '') && ! empty($row['name'])) {
            $row['slug'] = $this->slugify($row['name']);
        } elseif (! empty($row['slug'])) {
            $row['slug'] = $this->slugify($row['slug']);
        }

        // Enforce uniqueness (with suffix -2, -3, …) when slug exists
        if (! empty($row['slug'])) {
            $ignoreId   = $row[$this->primaryKey] ?? null;
            $row['slug'] = $this->uniqueSlug($row['slug'], $ignoreId);
        }

        // Normalize booleanish flags
        if (isset($row['is_featured'])) $row['is_featured'] = (int) !!$row['is_featured'];
        if (isset($row['visible']))     $row['visible']     = (int) !!$row['visible'];

        // Guard stock & price
        if (isset($row['stock_quantity'])) $row['stock_quantity'] = max(0, (int)$row['stock_quantity']);
        if (isset($row['price']))          $row['price'] = number_format((float)$row['price'], 2, '.', '');

        return $data;
    }

    protected function slugify(string $text): string
    {
        $text = strtolower($text);
        $text = preg_replace('~[^a-z0-9]+~', '-', $text);
        $text = trim($text, '-');
        return $text ?: 'item';
    }

    protected function uniqueSlug(string $baseSlug, ?int $ignoreId = null): string
    {
        $slug  = $baseSlug;
        $count = 1;

        while (true) {
            $builder = $this->builder()->select($this->primaryKey)->where('slug', $slug);
            if ($ignoreId) $builder->where($this->primaryKey . ' !=', $ignoreId);
            $exists = $builder->get(1)->getFirstRow('array');

            if (! $exists) return $slug;

            $count++;
            $slug = $baseSlug . '-' . $count;
        }
    }

    /* -------------------------------
     * Finders & joins
     * ----------------------------- */

    public function findBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)->first();
    }

    /**
     * Fetch an item with category, manufacturer, primary image, and general info (if present).
     * $extraJoins can be used to add more joins from the controller if needed.
     */
    public function getWithDetails(int $itemId): ?array
    {
        $db = $this->db;

        $builder = $db->table('items i')
            ->select([
                'i.*',
                'c.name AS category_name',
                'c.slug AS category_slug',
                'm.name AS manufacturer_name',
                'gi.era_period',
                'gi.country_origin',
                'gi.branch_org',
                'gi.on_sale',
                'pi.url AS primary_image_url',
                'pi.file_path AS primary_image_path',
            ])
            ->join('categories c', 'c.category_id = i.category_id', 'left')
            ->join('manufacturers m', 'm.manufacturer_id = i.manufacturer_id', 'left')
            ->join('item_general_info gi', 'gi.item_id = i.item_id', 'left')
            // primary image: either flagged is_primary=1 or lowest image_order
            ->join(
                '(SELECT im1.item_id, im1.url, im1.file_path
                    FROM item_images im1
                    WHERE im1.is_primary = 1
                    UNION
                 SELECT im2.item_id, im2.url, im2.file_path
                    FROM item_images im2
                    WHERE im2.image_order IS NOT NULL
                    ORDER BY im2.image_order ASC
                ) pi',
                'pi.item_id = i.item_id',
                'left'
            )
            ->where('i.item_id', $itemId)
            ->limit(1);

        $row = $builder->get()->getFirstRow('array');
        return $row ?: null;
    }

    /**
     * Search by name/sku/slug (and optionally by category).
     */
    public function search(string $q, ?int $categoryId = null, int $limit = 20, int $offset = 0): array
    {
        $builder = $this->builder()->like('name', $q)
                                   ->orLike('sku', $q)
                                   ->orLike('slug', $q);

        if ($categoryId) {
            $builder->where('category_id', $categoryId);
        }

        return $builder->orderBy('created_at', 'DESC')
                       ->limit($limit, $offset)
                       ->get()
                       ->getResultArray();
    }

    /* -------------------------------
     * Images helpers (item_images)
     * ----------------------------- */

    public function getImages(int $itemId): array
    {
        return $this->db->table('item_images')
            ->where('item_id', $itemId)
            ->orderBy('is_primary', 'DESC')
            ->orderBy('image_order', 'ASC')
            ->orderBy('image_id', 'ASC')
            ->get()->getResultArray();
    }

    public function getPrimaryImage(int $itemId): ?array
    {
        $row = $this->db->table('item_images')
            ->where('item_id', $itemId)
            ->orderBy('is_primary', 'DESC')
            ->orderBy('image_order', 'ASC')
            ->orderBy('image_id', 'ASC')
            ->get(1)->getFirstRow('array');
        return $row ?: null;
    }

    public function setPrimaryImage(int $itemId, int $imageId): bool
    {
        $db = $this->db;
        $db->transStart();

        // Clear current primary
        $db->table('item_images')->where('item_id', $itemId)->set('is_primary', 0)->update();

        // Set new primary
        $db->table('item_images')->where('item_id', $itemId)->where('image_id', $imageId)->set('is_primary', 1)->update();

        $db->transComplete();
        return $db->transStatus();
    }

    public function reorderImages(int $itemId, array $imageIdToOrder): bool
    {
        // $imageIdToOrder = [image_id => order, ...]
        $db = $this->db;
        $db->transStart();
        foreach ($imageIdToOrder as $imgId => $ord) {
            $db->table('item_images')->where([
                'item_id'  => $itemId,
                'image_id' => (int)$imgId
            ])->set('image_order', (int)$ord)->update();
        }
        $db->transComplete();
        return $db->transStatus();
    }

    /* -------------------------------
     * Flags & stock helpers
     * ----------------------------- */

    public function toggleVisible(int $itemId, bool $visible): bool
    {
        return (bool) $this->update($itemId, ['visible' => $visible ? 1 : 0]);
    }

    public function markFeatured(int $itemId, bool $featured): bool
    {
        return (bool) $this->update($itemId, ['is_featured' => $featured ? 1 : 0]);
    }

    public function adjustStock(int $itemId, int $delta): bool
    {
        // Clamp to >= 0
        $item = $this->find($itemId);
        if (! $item) return false;

        $new = max(0, ((int)$item['stock_quantity']) + $delta);
        return (bool) $this->update($itemId, ['stock_quantity' => $new]);
    }

    /* -------------------------------
     * Listing utilities
     * ----------------------------- */

    public function listVisible(int $limit = 20, int $offset = 0, ?int $categoryId = null): array
    {
        $builder = $this->where('visible', 1);

        if ($categoryId) {
            $builder->where('category_id', $categoryId);
        }

        return $builder->orderBy('created_at', 'DESC')
                       ->limit($limit, $offset)
                       ->find();
    }
}
