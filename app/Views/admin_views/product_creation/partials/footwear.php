<!-- 
        'item_id',
        'footwear_type',    // jackboot, ankle boot, officer boot, etc.
        'upper_material',   // leather, canvas, etc.
        'sole_material',
        'size_marked',
        'laces_present',    // tinyint(1)
        'straps_present',   // tinyint(1)
        'pair_matching',    // tinyint(1) 
-->
<?php
    $footwearTypes = [
        '' => '-- Select --',
        'jackboot' => 'Jackboot',
        'ankle_boot' => 'Ankle Boot',
        'officer_boot' => 'Officer Boot',
        'combat_boot' => 'Combat Boot',
        'dress_shoe' => 'Dress Shoe',
        'work_boot' => 'Work Boot',
        'other' => 'Other',
    ];
?>


<div class="partial-section">
    <h2>Footwear Specifications</h2>
    <div class="form-group">
        <label for="footwear_type">Footwear Type/Model:</label>
        <select id="footwear_type" name="footwear_type">
            <?php foreach ($footwearTypes as $value => $label): ?>
                <option value="<?= esc($value) ?>" <?= old('footwear_type') === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="upper_material">Upper Material:</label>
        <input 
            type="text" 
            id="upper_material" 
            name="upper_material" 
            value="<?= old('upper_material') ?>"
            placeholder="e.g., leather, canvas"
        >
    </div>
    <div class="form-group">
        <label for="sole_material">Sole Material:</label>
        <input 
            type="text" 
            id="sole_material" 
            name="sole_material" 
            value="<?= old('sole_material') ?>"
            placeholder="e.g., leather, canvas"
        >
    </div>

    <div class="form-group">
        <label for="size_marked">Size Marked:</label>
        <input 
            type="text" 
            id="size_marked" 
            name="size_marked" 
            value="<?= old('size_marked') ?>"
            placeholder="e.g., 42, 9 US"
        >
    </div>
    <div class="form-group">
        <label for="laces_present">Laces Present:</label>
        <select id="laces_present" name="laces_present">
            <option value="">-- Select --</option>
            <option value="1" <?= old('laces_present') === '1' ? 'selected' : '' ?>>Yes</option>
            <option value="0" <?= old('laces_present') === '0' ? 'selected' : '' ?>>No</option>
        </select>
    </div>
    <div class="form-group">
        <label for="straps_present">Straps Present:</label>
        <select id="straps_present" name="straps_present">
            <option value="">-- Select --</option>
            <option value="1" <?= old('straps_present') === '1' ? 'selected' : '' ?>>Yes</option>
            <option value="0" <?= old('straps_present') === '0' ? 'selected' : '' ?>>No</option>
        </select>
    </div>
    <div class="form-group">
        <label for="pair_matching">Pair Matching:</label>
        <select id="pair_matching" name="pair_matching">
            <option value="">-- Select --</option>
            <option value="1" <?= old('pair_matching') === '1' ? 'selected' : '' ?>>Yes</option>
            <option value="0" <?= old('pair_matching') === '0' ? 'selected' : '' ?>>No</option>
        </select>
    </div>
</div>