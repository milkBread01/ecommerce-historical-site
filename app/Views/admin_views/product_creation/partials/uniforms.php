<!-- 
        'item_id',
        'uniform_type',            // field tunic, parade jacket, overcoat...
        'rank_insignia_present',   // tinyint(1)
        'unit_patches_present',    // tinyint(1)
        'buttons_original',        // tinyint(1)
        'label_maker_tag',
        'size_marked',
        'measure_chest',
        'measure_sleeve',
        'measure_length',
        'lining_material',
        'damage_notes', 
-->

<?php

    $uniformData = $specs ?? [];

    // get field value where priorety is: current Data (old() function) > db data > default value
    $getValueUniform = function($field, $default='') use($uniformData){
        $oldValue = old($field);

        if( $oldValue !== null && $oldValue !== ''){
            return $oldValue;
        }

        if(isset($uniformData[$field]) && $uniformData[$field] !== null){
            return $uniformData[$field];
        }

        return $default;
    };

    $isSelectedUniform = function($field, $value) use ($getValueUniform) {
        return $getValueUniform($field) == $value ? 'selected' : '';

    };

    $uniformTypes = [

        // combat / field wear
        'combat' => [
            'field_jacket'    => 'Field Jacket',
            'field_tunic'     => 'Field Tunic',
            'field_blouse'    => 'Field Blouse',
            'camouflage_smock'=> 'Camouflage Smock',
            'parka'           => 'Parka',
            'anorak'          => 'Anorak',
            'raincoat'        => 'Raincoat',
            'poncho'          => 'Poncho',
            'tanker_jacket'   => 'Tanker Jacket',
            'utility_jacket'  => 'Utility Jacket',
            'fatigue_jacket'  => 'Fatigue Jacket',
            'hbt_jacket'      => 'HBT Jacket (Herringbone Twill)',
            'bush_jacket'     => 'Bush Jacket',
            'tropical_tunic'  => 'Tropical Tunic',
            'jump_smock'      => 'Paratrooper Jump Smock',
            'coveralls'       => 'Coveralls / Work Suit',
        ],

        // service / dress
        'service' => [
            'service_tunic'   => 'Service Dress Tunic',
            'dress_tunic'     => 'Dress Tunic',
            'mess_dress'      => 'Mess Dress',
            'waist_length_jacket' => 'Waist-Length Jacket (Ike Style)',
            'blazer_coat'     => 'Blazer / Coat',
        ],

        // flight / armor
        'flight' => [
            'flight_jacket'   => 'Flight Jacket',
            'flying_suit'     => 'Flying Suit',
            'flight_overalls' => 'Flight Overalls',
            'tankers_overalls'=> 'Tankers Overalls',
        ],

        // naval
        'naval' => [
            'pea_coat'        => 'Pea Coat',
            'reefer_jacket'   => 'Reefer Jacket',
            'sailor_jumper'   => 'Sailor Jumper',
            'naval_tunic'     => 'Naval Tunic',
        ],
        // accessories-as-garments (optional; include only if you list them as “uniform” items)
        'accessories' => [
            'greatcoat'       => 'Greatcoat',
            'cloak_cape'      => 'Cloak / Cape',
            'winter_coat'     => 'Winter Coat',
            'parade_jacket'   => 'Parade Jacket',
        ],
    ];

?>

<div class="partial-section">
    <h2 class="section-title">Uniform Specifications</h2>
    <div class="form-group">
        <label for="uniform_type">Uniform Type/Model:</label>
        <select id="uniform_type" name="uniform_type">
            <option value="">-- Select Uniform Type --</option>
            <?php foreach ($uniformTypes as $category => $types): ?>
                <optgroup label="<?= esc(ucfirst($category)) ?>">
                    <?php foreach ($types as $value => $label): ?>
                        <option 
                            value="<?= esc($value) ?>" 
                            <?= $isSelectedUniform('uniform_type', $value) ?>
                        >
                            <?= esc($label) ?>
                        </option>
                    <?php endforeach; ?>
                </optgroup>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="rank_insignia_present">Rank Insignia Present:</label>
        <select id="rank_insignia_present" name="rank_insignia_present">
            <option value="">-- Select --</option>
            <option value="1" <?= $isSelectedUniform('rank_insignia_present','1') ?>>Yes</option>
            <option value="0" <?= $isSelectedUniform('rank_insignia_present','0') ?>>No</option>
        </select>
    </div>
    <div class="form-group">
        <label for="unit_patches_present">Unit Patches Present:</label>
        <select id="unit_patches_present" name="unit_patches_present">
            <option value="">-- Select --</option>
            <option value="1" <?= $isSelectedUniform('unit_patches_present','1') ?>>Yes</option>
            <option value="0" <?= $isSelectedUniform('unit_patches_present','0') ?>>No</option>
        </select>
    </div>
    <div class="form-group">
        <label for="buttons_original">Buttons Original:</label>
        <select id="buttons_original" name="buttons_original">
            <option value="">-- Select --</option>
            <option value="1" <?= $isSelectedUniform('buttons_original','1') ?>>Yes</option>
            <option value="0" <?= $isSelectedUniform('buttons_original','0') ?>>No</option>
        </select>
    </div>
    <div class="form-group">
        <label for="label_maker_tag">Label/Maker Tag:</label>
        <input 
            type="text" 
            id="label_maker_tag" 
            name="label_maker_tag" 
            value="<?= esc($getValueUniform('label_maker_tag')) ?>"
            placeholder="e.g., manufacturer name, country of origin"
        >
    </div>

    <div class="form-group">
        <label for="size_marked">Size Marked:</label>
        <input 
            type="text" 
            id="size_marked" 
            name="size_marked" 
            value="<?= esc($getValueUniform('size_marked')) ?>"
            placeholder="e.g., 38R, M, L"
        >
    </div>

    <div class="form-group">
        <label for="measure_chest">Chest Measurement:</label>
        <input 
            type="text" 
            id="measure_chest" 
            name="measure_chest" 
            value="<?= esc($getValueUniform('measure_chest')) ?>"
            placeholder="e.g., 38 inches, 96 cm"
        >
    </div>

    <div class="form-group">
        <label for="measure_sleeve">Sleeve Length:</label>
        <input 
            type="text" 
            id="measure_sleeve" 
            name="measure_sleeve" 
            value="<?= esc($getValueUniform('measure_sleeve')) ?>"
            placeholder="e.g., 25 inches, 64 cm"
        >
    </div>

    <div class="form-group">
        <label for="measure_length">Overall Length:</label>
        <input 
            type="text" 
            id="measure_length" 
            name="measure_length" 
            value="<?= esc($getValueUniform('measure_length')) ?>"
            placeholder="e.g., 30 inches, 76 cm"
        >
    </div>

    <div class="form-group">
        <label for="lining_material">Lining Material:</label>
        <input 
            type="text" 
            id="lining_material" 
            name="lining_material" 
            value="<?= esc($getValueUniform('lining_material')) ?>"
            placeholder="e.g., cotton, polyester"
        >
    </div>
    <div class="form-group">
        <label for="damage_notes">Damage Notes:</label>
        <textarea 
            id="damage_notes" 
            name="damage_notes" 
            rows="4" 
            placeholder="e.g., tears, stains, missing buttons"
        ><?= esc($getValueUniform('damage_notes')) ?></textarea>
    </div>
</div>