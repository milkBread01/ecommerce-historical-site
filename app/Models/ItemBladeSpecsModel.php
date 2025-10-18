<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemBladeSpecsModel extends Model
{
    protected $table        = 'item_blade_specs';
    protected $primaryKey   = 'item_id';   // 1:1 with items
    protected $returnType   = 'array';
    protected $useSoftDeletes = false;

    // Adjust if you added timestamps to this table
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'item_id',
        'blade_type_label',
        'blade_length',
        'overall_length',
        'blade_material',
        'handle_material',
        'edge_condition',      // sharp|dull|blunted|unknown
        'scabbard_included',   // tinyint(1)
        'markings_label',
        'serial_number',
        'maker_arsenal',
        'finish_condition',
    ];

    protected $validationRules = [
        'item_id'           => 'required|is_natural_no_zero',
        'blade_type_label'  => 'permit_empty|max_length[120]',
        'blade_length'      => 'permit_empty|max_length[50]',
        'overall_length'    => 'permit_empty|max_length[50]',
        'blade_material'    => 'permit_empty|max_length[120]',
        'handle_material'   => 'permit_empty|max_length[120]',
        'edge_condition'    => 'permit_empty|in_list[sharp,dull,blunted,unknown]',
        'scabbard_included' => 'permit_empty|in_list[0,1]',
        'markings_label'    => 'permit_empty|max_length[255]',
        'serial_number'     => 'permit_empty|max_length[120]',
        'maker_arsenal'     => 'permit_empty|max_length[255]',
        'finish_condition'  => 'permit_empty|max_length[120]',
    ];

    protected $beforeInsert = ['normalize'];
    protected $beforeUpdate = ['normalize'];

    protected function normalize(array $data)
    {
        if (!isset($data['data'])) return $data;

        $row = &$data['data'];

        // Normalize boolean-like field
        if (array_key_exists('scabbard_included', $row)) {
            $row['scabbard_included'] = (int) !!$row['scabbard_included'];
        }

        // Lowercase/whitelist edge_condition
        if (!empty($row['edge_condition'])) {
            $val = strtolower(trim((string)$row['edge_condition']));
            $allowed = ['sharp','dull','blunted','unknown'];
            $row['edge_condition'] = in_array($val, $allowed, true) ? $val : 'unknown';
        }

        // Empty strings → NULL for optional text fields
        $nullable = [
            'blade_type_label','blade_length','overall_length','blade_material',
            'handle_material','markings_label','serial_number',
            'maker_arsenal','finish_condition'
        ];
        foreach ($nullable as $col) {
            if (array_key_exists($col, $row) && is_string($row[$col]) && trim($row[$col]) === '') {
                $row[$col] = null;
            }
        }

        return $data;
    }

    /* ---------- Convenience helpers ---------- */

    public function getByItemId(int $itemId): ?array
    {
        $row = $this->find($itemId);
        return $row ?: null;
    }

    /** Create or update the row for an item_id. */
    public function upsertForItem(int $itemId, array $data): bool
    {
        $data['item_id'] = $itemId;
        return $this->find($itemId)
            ? (bool) $this->update($itemId, $data)
            : (bool) $this->insert($data, false);
    }

    public function setEdgeCondition(int $itemId, string $condition): bool
    {
        $condition = strtolower($condition);
        if (!in_array($condition, ['sharp','dull','blunted','unknown'], true)) {
            $condition = 'unknown';
        }
        return (bool) $this->update($itemId, ['edge_condition' => $condition]);
    }

    public function setScabbardIncluded(int $itemId, bool $hasScabbard): bool
    {
        return (bool) $this->update($itemId, ['scabbard_included' => $hasScabbard ? 1 : 0]);
    }
}
