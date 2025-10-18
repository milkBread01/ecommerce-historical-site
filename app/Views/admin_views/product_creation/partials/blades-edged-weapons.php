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
    $bladeTypes = [
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
?>
<div class = "partial-section">
    <h2 class="section-title">Blade Specifications</h2>
    <div class="form-group">
        <label for="blade_type_label">Blade Type/Model:</label>
        <select id="blade_type_label" name="blade_type_label">
            <option value="">-- Select --</option>
            <?php foreach ($bladeTypes as $key => $label): ?>
                <option value="<?= $key ?>" <?= old('blade_type_label') === $key ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="blade_length">Blade Length:</label>
        <input 
            type="number" 
            id="blade_length" 
            name="blade_length" 
            value="<?= old('blade_length') ?>"
        >
        <select id="blade_length_unit" name="blade_length_unit">
            <option value="feet" <?= old('blade_length_unit') === 'feet' ? 'selected' : '' ?>>Feet</option>
            <option value="inches" <?= old('blade_length_unit') === 'inches' ? 'selected' : '' ?>>Inches</option>
            <option value="mils" <?= old('blade_length_unit') === 'mils' ? 'selected' : '' ?>>Mils</option>
            <option value="meters" <?= old('blade_length_unit') === 'meters' ? 'selected' : '' ?>>Meters</option>
            <option value="cm" <?= old('blade_length_unit') === 'cm' ? 'selected' : '' ?>>Centimeters</option>
            <option value="mm" <?= old('blade_length_unit') === 'mm' ? 'selected' : '' ?>>Millimeters</option>
        </select>
    </div>
    <div class="form-group">
        <label for="overall_length">Overall Length:</label>
        <input 
            type="number" 
            id="overall_length" 
            name="overall_length" 
            value="<?= old('overall_length') ?>"
        >
        <select id="overall_length_unit" name="overall_length_unit">
            <option value="feet" <?= old('overall_length_unit') === 'feet' ? 'selected' : '' ?>>Feet</option>
            <option value="inches" <?= old('overall_length_unit') === 'inches' ? 'selected' : '' ?>>Inches</option>
            <option value="mils" <?= old('overall_length_unit') === 'mils' ? 'selected' : '' ?>>Mils</option>
            <option value="meters" <?= old('overall_length_unit') === 'meters' ? 'selected' : '' ?>>Meters</option>
            <option value="cm" <?= old('overall_length_unit') === 'cm' ? 'selected' : '' ?>>Centimeters</option>
            <option value="mm" <?= old('overall_length_unit') === 'mm' ? 'selected' : '' ?>>Millimeters</option>
        </select>
    </div>
    <div class="form-group">
        <label for="blade_material">Blade Material:</label>
        <input 
            type="text" 
            id="blade_material" 
            name="blade_material" 
            value="<?= old('blade_material') ?>"
        >
    </div>
    <div class="form-group">
        <label for="handle_material">Handle Material:</label>
        <input 
            type="text" 
            id="handle_material" 
            name="handle_material" 
            value="<?= old('handle_material') ?>"
        >
    </div>
    <div class="form-group">
        <label for="edge_condition">Edge Condition:</label>
        <select id="edge_condition" name="edge_condition">
            <option value="" <?= old('edge_condition') === null ? 'selected' : '' ?>>-- Select --</option>
            <option value="sharp" <?= old('edge_condition') === 'sharp' ? 'selected' : '' ?>>Sharp</option>
            <option value="dull" <?= old('edge_condition') === 'dull' ? 'selected' : '' ?>>Dull</option>
            <option value="blunted" <?= old('edge_condition') === 'blunted' ? 'selected' : '' ?>>Blunted</option>
            <option value="unknown" <?= old('edge_condition') === 'unknown' ? 'selected' : '' ?>>Unknown</option>
        </select>
    </div>
    <div class="form-group">
        <label for="scabbard_included">Scabbard Included:</label>
        <select id="scabbard_included" name="scabbard_included">
            <option value="" <?= old('scabbard_included') === null ? 'selected' : '' ?>>-- Select --</option>
            <option value="1" <?= old('scabbard_included') === '1' ? 'selected' : '' ?>>Yes</option>
            <option value="0" <?= old('scabbard_included') === '0' ? 'selected' : '' ?>>No</option>
        </select>
    </div>
    <div class="form-group">
        <label for="markings_label">Markings:</label>
        <input 
            type="text" 
            id="markings_label" 
            name="markings_label" 
            value="<?= old('markings_label') ?>"
        >
    </div>
    <div class="form-group">
        <label for="serial_number">Serial Number:</label>
        <input 
            type="text" 
            id="serial_number" 
            name="serial_number" 
            value="<?= old('serial_number') ?>"
        >
    </div>
    <div class="form-group">
        <label for="maker_arsenal">Maker/Arsenal:</label>
        <input 
            type="text" 
            id="maker_arsenal" 
            name="maker_arsenal" 
            value="<?= old('maker_arsenal') ?>"
        >
    </div>
    <div class="form-group">
        <label for="finish_condition">Finish/Condition:</label>
        <input 
            type="text" 
            id="finish_condition" 
            name="finish_condition" 
            value="<?= old('finish_condition') ?>"
        >
    </div>
</div>