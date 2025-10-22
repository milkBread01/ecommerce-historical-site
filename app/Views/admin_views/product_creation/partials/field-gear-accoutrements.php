<!-- 
        'item_id',
        'gear_type',                // canteen, belt, pouch, gas mask, binoculars, compass, etc.
        'material',
        'maker_markings',
        'functionality',            // working | display only | unknown
        'completeness_notes',       // text about missing parts, etc.
        'straps_attachments_pres',  // tinyint(1) 

-->

<?php

    $gearData = $specs ?? [];

    // get field value where priorety is: current Data (old() function) > db data > default value
    $getValue = function($field, $default='') use($gearData){
        $oldValue = old($field);

        if( $oldValue !== null && $oldValue !== ''){
            return $oldValue;
        }

        if(isset($gearData[$field]) && $gearData[$field] !== null){
            return $gearData[$field];
        }

        return $default;
    };

    $isSelected = function($field, $value) use ($getValue) {
        return $getValue($field) == $value ? 'selected' : '';

    };

    $gearTypes = [
        '' => '-- Select --',
        'canteen' => 'Canteen',
        'belt' => 'Belt',
        'pouch' => 'Pouch',
        'flask' => 'Flask',
        'coin_purse' => 'Coin Purse',
        'coin' => 'Coin',
        'dog_tags' => 'Dog Tags',
        'watch' => 'Watch',
        'bracelet' => 'Bracelet',
        'necklace' => 'Necklace',
        'ring' => 'Ring',
        'tie_clip' => 'Tie Clip',
        'gas_mask' => 'Gas Mask',
        'binoculars' => 'Binoculars',
        'compass' => 'Compass',
        'other' => 'Other',
    ];
    $functionalityOptions = [
        '' => '-- Select --',
        'working' => 'Working',
        'display only' => 'Display Only',
        'unknown' => 'Unknown',
    ];
?>


<div class = "partial-section">
    <h2 class="section-title">Gear Specifications</h2>
    <div class="form-group">
        <label for="gear_type">Gear Type:</label>
        <select id="gear_type" name="gear_type">
            <?php foreach ($gearTypes as $value => $label): ?>
                <option 
                    value="<?= esc($value) ?>" 
                    <?= $isSelected('gear_type', $value) ?>
                >
                    <?= esc($label) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="material">Material:</label>
        <input 
            type="text" 
            id="material" 
            name="material" 
            value="<?= esc($getValue('material')) ?>"
            placeholder="e.g., leather, canvas, metal"
        >
    </div>
    <div class="form-group">
        <label for="maker_markings">Maker/Markings:</label>
        <input 
            type="text" 
            id="maker_markings" 
            name="maker_markings" 
            value="<?= esc($getValue('maker_markings')) ?>"
            placeholder="e.g., manufacturer, date, country, military acceptance marks"
        >
    </div>
    <div class="form-group">
        <label for="functionality">Functionality:</label>
        <select id="functionality" name="functionality">
            <?php foreach ($functionalityOptions as $value => $label): ?>
                <option 
                    value="<?= esc($value) ?>" 
                    <?= $isSelected('functionality', $value) ?>
                >
                    <?= esc($label) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="completeness_notes">Completeness Notes:</label>
        <textarea 
            id="completeness_notes" 
            name="completeness_notes" 
            rows="4"
            placeholder="Notes about missing parts, condition, etc."
        ><?= esc($getValue('completeness_notes')) ?></textarea>
    </div>
    <div class="form-group">
        <label for="straps_attachments_pres">Straps/Attachments Present:</label>
        <select id="straps_attachments_pres" name="straps_attachments_pres">
            <option value="">-- Select --</option>
            <option value="1" <?= $isSelected('straps_attachments_pres', '1') ?>>Yes</option>
            <option value="0" <?= $isSelected('straps_attachments_pres', '0') ?>>No</option>
        </select>
    </div>
</div>