<!-- 
        'item_id',
        'insignia_type',  // cloth patch, armband, shoulder board, collar tab, etc.
        'material',       // wool, cotton, bullion, embroidered, etc.
        'backing_type',   // cloth, paper, cardboard
        'stitch_type',    // hand, machine
        'damage_notes',   // text (fraying, stains, etc.) 

-->

<?php

    $insigniaData = $specs ?? [];

    // get field value where priorety is: current Data (old() function) > db data > default value
    $getValue = function($field, $default='') use($insigniaData){
        $oldValue = old($field);

        if( $oldValue !== null && $oldValue !== ''){
            return $oldValue;
        }

        if(isset($insigniaData[$field]) && $insigniaData[$field] !== null){
            return $insigniaData[$field];
        }

        return $default;
    };

    $isSelected = function($field, $value) use ($getValue) {
        return $getValue($field) == $value ? 'selected' : '';

    };

    $insigniaTypes = [
        '' => '-- Select --',
        'cloth_patch' => 'Cloth Patch',
        'armband' => 'Armband',
        'shoulder_board' => 'Shoulder Board',
        'collar_tab' => 'Collar Tab',
        'cuff_title' => 'Cuff Title',
        'cap_badge' => 'Cap Badge',
        'other' => 'Other',
    ];
    $materialTypes = [
        '' => '-- Select --',
        'wool' => 'Wool',
        'cotton' => 'Cotton',
        'bullion' => 'Bullion',
        'embroidered' => 'Embroidered',
        'felt' => 'Felt',
        'silk' => 'Silk',
        'other' => 'Other',
    ];
    $backingTypes = [
        '' => '-- Select --',
        'cloth' => 'Cloth',
        'paper' => 'Paper',
        'cardboard' => 'Cardboard',
        'plastic' => 'Plastic',
        'other' => 'Other',
    ];
    $stitchTypes = [
        '' => '-- Select --',
        'hand' => 'Hand',
        'machine' => 'Machine',
        'other' => 'Other',
    ];
?>

<div class="partial-section">
    <h2 class="section-title">Insignia Specifications</h2>
    <div class="form-group">
        <label for="insignia_type">Insignia Type/Model:</label>
        <select id="insignia_type" name="insignia_type">
            <?php foreach ($insigniaTypes as $value => $label): ?>
                <option 
                    value="<?= esc($value) ?>" 
                    <?= $isSelected('insignia_type', $value) ?>
                >
                    <?= esc($label) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="material">Material:</label>
        <select id="material" name="material">
            <?php foreach ($materialTypes as $value => $label): ?>
                <option 
                    value="<?= esc($value) ?>" 
                    <?= $isSelected('material', $value) ?>
                    >
                        <?= esc($label) ?>
                    </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="backing_type">Backing Type:</label>
        <select id="backing_type" name="backing_type">
            <?php foreach ($backingTypes as $value => $label): ?>
                <option 
                    value="<?= esc($value) ?>" 
                    <?= $isSelected('backing_type', $value) ?>
                >
                    <?= esc($label) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="stitch_type">Stitch Type:</label>
        <select id="stitch_type" name="stitch_type">
            <?php foreach ($stitchTypes as $value => $label): ?>
                <option 
                    value="<?= esc($value) ?>" 
                    <?= $isSelected('stitch_type', $value) ?>
                >
                    <?= esc($label) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="damage_notes">Damage Notes:</label>
        <textarea 
            id="damage_notes" 
            name="damage_notes" 
            rows="4" 
            placeholder="e.g., fraying, stains, discoloration"
        ><?= $getValue('damage_notes') ?></textarea>
    </div>
</div>