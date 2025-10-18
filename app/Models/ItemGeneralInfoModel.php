<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemGeneralInfoModel extends Model
{
    protected $table          = 'item_general_info';
    protected $primaryKey     = 'item_id';        // 1:1 with items
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'item_id',
        'era_period',
        'country_origin',
        'branch_org',
        'unit_regiment',
        'authenticity',      // ENUM('Original','Reproduction','Replica w/ period parts','Unknown')
        'condition',         // ENUM('Mint','Excellent','Very Good','Good','Fair','Poor')
        'dimensions_label',
        'weight_label',
        'materials',
        'markings',
        'serial_numbers',
        'provenance_source',
        'documentation',     // TINYINT(1)
        'documentation_type',  // VARCHAR(255)
        'certificate_auth',  // TINYINT(1)
        'certificate_type',  // VARCHAR(255)
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /* -------------------------------
     * Validation
     * ----------------------------- */
    protected $validationRules = [
        'item_id'          => 'required|is_natural_no_zero',
        'era_period'       => 'permit_empty|max_length[255]',
        'country_origin'   => 'permit_empty|max_length[120]',
        'branch_org'       => 'permit_empty|max_length[120]',
        'unit_regiment'    => 'permit_empty|max_length[255]',

        'authenticity'     => "permit_empty|in_list[Original,Reproduction,Replica w/ period parts,Unknown]",
        'condition'        => "permit_empty|in_list[Mint,Excellent,Very Good,Good,Fair,Poor]",

        'dimensions_label' => 'permit_empty|max_length[255]',
        'weight_label'     => 'permit_empty|max_length[255]',
        'materials'        => 'permit_empty|max_length[255]',
        'markings'         => 'permit_empty|max_length[255]',
        'serial_numbers'   => 'permit_empty|max_length[255]',


        'provenance_source'=> 'permit_empty|max_length[255]',
        'documentation'    => 'permit_empty|in_list[0,1]',
        'documentation_type'=> 'permit_empty|max_length[255]',
        'certificate_auth' => 'permit_empty|in_list[0,1]',
        'certificate_type' => 'permit_empty|max_length[255]',

    ];

    protected $beforeInsert = ['normalizeFlags', 'normalizeEmptyToNull'];
    protected $beforeUpdate = ['normalizeFlags', 'normalizeEmptyToNull'];

    protected function normalizeFlags(array $data)
    {
        if (! isset($data['data'])) return $data;

        $row = &$data['data'];
        foreach (['documentation','certificate_auth','on_sale'] as $flag) {
            if (array_key_exists($flag, $row)) {
                $row[$flag] = (int) !!$row[$flag];
            }
        }
        return $data;
    }

    protected function normalizeEmptyToNull(array $data)
    {
        if (! isset($data['data'])) return $data;

        $row = &$data['data'];
        $nullable = [
            'era_period','country_origin','branch_org','unit_regiment',
            'dimensions_label','weight_label','materials','markings',
            'serial_numbers','description','provenance_source','video_url'
        ];

        foreach ($nullable as $col) {
            if (array_key_exists($col, $row) && is_string($row[$col]) && trim($row[$col]) === '') {
                $row[$col] = null;
            }
        }
        return $data;
    }

    /* -------------------------------
     * Convenience helpers
     * ----------------------------- */

    /**
     * Get general info by item_id (returns null if not found).
     */
    public function getByItemId(int $itemId): ?array
    {
        $row = $this->find($itemId);
        return $row ?: null;
    }

    /**
     * Upsert by item_id (create row if missing, otherwise update).
     * Returns true on success.
     */
    public function upsertForItem(int $itemId, array $data): bool
    {
        $data['item_id'] = $itemId;

        // If exists, update; else insert
        $exists = $this->find($itemId);
        if ($exists) {
            return (bool) $this->update($itemId, $data);
        }
        return (bool) $this->insert($data, false);
    }

    /**
     * Toggle on_sale for an item.
     */
    public function setOnSale(int $itemId, bool $onSale): bool
    {
        return (bool) $this->update($itemId, ['on_sale' => $onSale ? 1 : 0]);
    }

    /**
     * Mark presence of documentation / certificate of authenticity.
     */
    public function setDocumentationFlags(int $itemId, bool $hasDocs, bool $hasCOA): bool
    {
        return (bool) $this->update($itemId, [
            'documentation'   => $hasDocs ? 1 : 0,
            'certificate_auth'=> $hasCOA  ? 1 : 0,
        ]);
    }

    /**
     * Quick setter for provenance and notes.
     */
    public function setProvenance(int $itemId, ?string $source, ?string $notes = null): bool
    {
        $payload = ['provenance_source' => $source];
        if ($notes !== null) $payload['description'] = $notes; // optional: append/replace as you prefer
        return (bool) $this->update($itemId, $payload);
    }

    /**
     * Attach or update a video URL (e.g., 360° spin).
     */
    public function setVideo(int $itemId, ?string $url): bool
    {
        return (bool) $this->update($itemId, ['video_url' => $url]);
    }
}
