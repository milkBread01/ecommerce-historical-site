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
                <option value="<?= esc($value) ?>" <?= old('gear_type') === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="material">Material:</label>
        <input 
            type="text" 
            id="material" 
            name="material" 
            value="<?= old('material') ?>"
            placeholder="e.g., leather, canvas, metal"
        >
    </div>
    <div class="form-group">
        <label for="maker_markings">Maker/Markings:</label>
        <input 
            type="text" 
            id="maker_markings" 
            name="maker_markings" 
            value="<?= old('maker_markings') ?>"
            placeholder="e.g., manufacturer, date, country, military acceptance marks"
        >
    </div>
    <div class="form-group">
        <label for="functionality">Functionality:</label>
        <select id="functionality" name="functionality">
            <?php foreach ($functionalityOptions as $value => $label): ?>
                <option value="<?= esc($value) ?>" <?= old('functionality') === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
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
        ><?= old('completeness_notes') ?></textarea>
    </div>
    <div class="form-group">
        <label for="straps_attachments_pres">Straps/Attachments Present:</label>
        <select id="straps_attachments_pres" name="straps_attachments_pres">
            <option value="" <?= old('straps_attachments_pres') === '' ? 'selected' : '' ?>>-- Select --</option>
            <option value="1" <?= old('straps_attachments_pres') === '1' ? 'selected' : '' ?>>Yes</option>
            <option value="0" <?= old('straps_attachments_pres') === '0' ? 'selected' : '' ?>>No</option>
        </select>
    </div>
</div>