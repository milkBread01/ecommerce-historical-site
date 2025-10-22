<!-- 
        'item_id',
        'headgear_type',     // visor cap, helmet, beret, field cap, etc.
        'insignia_present',  // tinyint(1)
        'liner_material',
        'size_marked',
        'chinstrap_present', // tinyint(1)
        'shell_material', 
-->

<?php
/* 
    Anonymos function

    $getValue = function ($field, $default = '') use ($Data) {
        // ...
        return $default;
    };


    function() {...} creates a function with no name and assigns it to some variable, here $getValue. 
    You can call it like a normal function $getValue('key).
    
    use ($Data) : imports variables and they become available inside the function. this is a 'by value' copy of the variable, to pass by reference use the & operator

*/
    $headgearData = $specs ?? [];

    // get field value where priorety is: current Data (old() function) > db data > default value
    $getValue = function($field, $default='') use($headgearData){
        $oldValue = old($field);

        if( $oldValue !== null && $oldValue !== ''){
            return $oldValue;
        }

        if(isset($headgearData[$field]) && $headgearData[$field] !== null){
            return $headgearData[$field];
        }

        return $default;
    };

    $isSelected = function($field, $value) use ($getValue) {
        return $getValue($field) == $value ? 'selected' : '';

    };


    $headgearTypes = [
        '' => '-- Select --',
        'visor_cap' => 'Visor Cap',
        'helmet' => 'Helmet',
        'beret' => 'Beret',
        'field_cap' => 'Field Cap',
        'side_cap' => 'Side Cap',
        'busby' => 'Busby',
        'shako' => 'Shako',
        'pickelhaube' => 'Pickelhaube',
        'other' => 'Other',
    ];
?>

<div class = "partial-section">
    
    <h2 class="section-title">Headgear Specifications</h2>
    <div class="form-group">
        <label for="headgear_type">Headgear Type/Model:</label>
        <select id="headgear_type" name="headgear_type">
            <?php foreach ($headgearTypes as $value => $label): ?>
                <option 
                    value="<?= esc($value) ?>" 
                    <?= $isSelected('headgear_type',$value) ?>
                >
                    <?= esc($label) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="insignia_present">Insignia Present:</label>
        <select id="insignia_present" name="insignia_present">
            <option value="">-- Select --</option>
            <option value="1" <?= $isSelected('insignia_present', '1') ?>>Yes</option>
            <option value="0" <?= $isSelected('insignia_present', '0') ?>>No</option>
        </select>
    </div>
    
    <div class="form-group">
        <label for="liner_material">Liner Material:</label>
        <input 
            type="text" 
            id="liner_material" 
            name="liner_material" 
            value="<?= $getValue('liner_material') ?>"
            placeholder="e.g., leather, fabric"
        >
    </div>

    <div class="form-group">
        <label for="size_marked">Size Marked:</label>
        <input 
            type="text" 
            id="size_marked" 
            name="size_marked" 
            value="<?= $getValue('size_marked') ?>"
            placeholder="e.g., 57, 7 1/8"
        >
    </div>

    <div class="form-group">
        <label for="chinstrap_present">Chinstrap Present:</label>
        <select id="chinstrap_present" name="chinstrap_present">
            <option value="">-- Select --</option>
            <option value="1" <?= $isSelected('chinstrap_present', '1') ?>>Yes</option>
            <option value="0" <?= $isSelected('chinstrap_present', '0') ?>>No</option>
        </select>
    </div>

    <div class="form-group">
        <label for="shell_material">Shell Material:</label>
        <input 
            type="text" 
            id="shell_material" 
            name="shell_material" 
            value="<?= $getValue('shell_material') ?>"
            placeholder="e.g., steel, plastic, leather"
        >
    </div>
</div>