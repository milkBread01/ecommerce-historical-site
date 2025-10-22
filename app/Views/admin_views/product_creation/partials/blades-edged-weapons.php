<!-- 
        'item_id', // fk from pk of items table
        'blade_type_label',
        'blade_length',
        'overall_length',
        'blade_material',
        'handle_material',
        'edge_condition',      // enum [sharp|dull|blunted|unknown]
        'scabbard_included',   // tinyint(1)
        'markings_label',
        'serial_number',
        'maker_arsenal',
        'finish_condition', 
-->
<?php
/**
 * Reusable Blade Specifications Form Partial
 * 
 * Usage in Controller:
 * 
 * // For NEW product:
 * return view('your_view', ['blade_data' => null]);
 * 
 * // For EDIT product:
 * $bladeSpecs = $bladeSpecsModel->find($itemId);
 * return view('your_view', ['blade_data' => $bladeSpecs]);
 */

// Get existing data or use empty array
$bladeData = $specs ?? [];

// Helper function to get field value (prioritizes: old input > existing data > default)
$getValue = function($field, $default = '') use ($bladeData) {
    // First check for old input (validation errors)
    $oldValue = old($field);
    if ($oldValue !== null && $oldValue !== '') {
        return $oldValue;
    }
    
    // Then check existing data
    if (isset($bladeData[$field]) && $bladeData[$field] !== null) {
        return $bladeData[$field];
    }
    
    // Finally return default
    return $default;
};

// Helper for select options
$isSelected = function($field, $value) use ($getValue) {
    return $getValue($field) == $value ? 'selected' : '';
};

$bladeTypes = [
    '' => '-- Select --',
    'dagger' => 'Dagger',
    'knife' => 'Knife',
    'bayonet' => 'Bayonet',
    'sword' => 'Sword',
    'saber' => 'Saber',
    'cutlass' => 'Cutlass',
    'rapier' => 'Rapier',
    'scimitar' => 'Scimitar',
    'katana' => 'Katana',
    'wakizashi' => 'Wakizashi',
    'tanto' => 'Tanto',
    'bowie' => 'Bowie Knife',
    'gravity_knife' => 'Gravity Knife',
    'switchblade' => 'Switchblade',
    'machete' => 'Machete',
    'custom' => 'Custom',
    'other' => 'Other',
];

$lengthUnits = [
    '' => '-- Select --',
    'feet' => 'Feet',
    'inches' => 'Inches',
    'mils' => 'Mils',
    'meters' => 'Meters',
    'cm' => 'Centimeters',
    'mm' => 'Millimeters',
];

$edgeConditions = [
    '' => '-- Select --',
    'sharp' => 'Sharp',
    'dull' => 'Dull',
    'blunted' => 'Blunted',
    'unknown' => 'Unknown',
];
?>

<div class="partial-section">
    <h2 class="section-title">Blade Specifications</h2>
    
    <div class="form-group">
        <label for="blade_type_label">Blade Type/Model:</label>
        <select id="blade_type_label" name="blade_type_label">
            <?php foreach ($bladeTypes as $value => $label): ?>
                <option value="<?= esc($value) ?>" <?= $isSelected('blade_type_label', $value) ?>>
                    <?= esc($label) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="blade_length">Blade Length:</label>
        <div class="input-group">
            <input 
                type="number" 
                id="blade_length" 
                name="blade_length" 
                step="0.01"
                value="<?= esc($getValue('blade_length')) ?>"
                placeholder="e.g., 12.5"
            >
            <select id="blade_length_unit" name="blade_length_unit">
                <?php foreach ($lengthUnits as $value => $label): ?>
                    <option 
                        value="<?= esc($value) ?>" 
                        <?= $isSelected('blade_length_unit', $value) ?>
                    >
                        <?= esc($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label for="overall_length">Overall Length:</label>
        <div class="input-group">
            <input 
                type="number" 
                id="overall_length" 
                name="overall_length" 
                step="0.01"
                value="<?= esc($getValue('overall_length')) ?>"
                placeholder="e.g., 18.0"
            >
            <select id="overall_length_unit" name="overall_length_unit">
                <?php foreach ($lengthUnits as $value => $label): ?>
                    <option 
                        value="<?= esc($value) ?>" 
                        <?= $isSelected('overall_length_unit', $value) ?>
                    >
                        <?= esc($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label for="blade_material">Blade Material:</label>
        <input 
            type="text" 
            id="blade_material" 
            name="blade_material" 
            value="<?= esc($getValue('blade_material')) ?>"
            placeholder="e.g., steel, Damascus steel, carbon steel"
        >
    </div>

    <div class="form-group">
        <label for="handle_material">Handle Material:</label>
        <input 
            type="text" 
            id="handle_material" 
            name="handle_material" 
            value="<?= esc($getValue('handle_material')) ?>"
            placeholder="e.g., wood, leather, ivory, bone"
        >
    </div>

    <div class="form-group">
        <label for="edge_condition">Edge Condition:</label>
        <select id="edge_condition" name="edge_condition">
            <?php foreach ($edgeConditions as $value => $label): ?>
                <option 
                    value="<?= esc($value) ?>" 
                    <?= $isSelected('edge_condition', $value) ?>
                >
                    <?= esc($label) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="scabbard_included">Scabbard Included:</label>
        <select id="scabbard_included" name="scabbard_included">
            <option value="">-- Select --</option>
            <option value="1" <?= $isSelected('scabbard_included', '1') ?>>Yes</option>
            <option value="0" <?= $isSelected('scabbard_included', '0') ?>>No</option>
        </select>
    </div>

    <div class="form-group">
        <label for="markings_label">Markings:</label>
        <input 
            type="text" 
            id="markings_label" 
            name="markings_label" 
            value="<?= esc($getValue('markings_label')) ?>"
            placeholder="Describe any markings, stamps, or inscriptions"
        >
    </div>

    <div class="form-group">
        <label for="serial_number">Serial Number:</label>
        <input 
            type="text" 
            id="serial_number" 
            name="serial_number" 
            value="<?= esc($getValue('serial_number')) ?>"
            placeholder="Enter serial number if present"
        >
    </div>

    <div class="form-group">
        <label for="maker_arsenal">Maker/Arsenal:</label>
        <input 
            type="text" 
            id="maker_arsenal" 
            name="maker_arsenal" 
            value="<?= esc($getValue('maker_arsenal')) ?>"
            placeholder="e.g., Solingen, Springfield Arsenal"
        >
    </div>

    <div class="form-group">
        <label for="finish_condition">Finish/Condition:</label>
        <input 
            type="text" 
            id="finish_condition" 
            name="finish_condition" 
            value="<?= esc($getValue('finish_condition')) ?>"
            placeholder="Describe the finish and overall condition"
        >
    </div>
</div>
