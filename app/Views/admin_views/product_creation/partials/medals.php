<!-- 
        'item_id',
        'type_label',              // medal, badge, ribbon bar, clasp...
        'campaign_award_name',
        'material',                // bronze, silver, enamel...
        'maker_manufacturer',
        'attachment_type',         // pinback, screwback, clasp, ribbon loop...
        'engravings',
        'serial_number',
        'ribbon_present',          // tinyint(1)
        'presentation_case_incl',  // tinyint(1) 

-->

<?php

    $medalData = $specs ?? [];

    // get field value where priorety is: current Data (old() function) > db data > default value
    $getValue = function($field, $default='') use($medalData){
        $oldValue = old($field);

        if( $oldValue !== null && $oldValue !== ''){
            return $oldValue;
        }

        if(isset($medalData[$field]) && $medalData[$field] !== null){
            return $medalData[$field];
        }

        return $default;
    };

    $isSelected = function($field, $value) use ($getValue) {
        return $getValue($field) == $value ? 'selected' : '';

    };

    $medalTypes = [
        '' => '-- Select --',
        'medal' => 'Medal',
        'badge' => 'Badge',
        'ribbon_bar' => 'Ribbon Bar',
        'clasp' => 'Clasp',
        'other' => 'Other',
    ];
    $materialTyoes = [
        '' => '-- Select --',
        'bronze' => 'Bronze',
        'silver' => 'Silver',
        'gold' => 'Gold',
        'enamel' => 'Enamel',
        'iron' => 'Iron',
        'other' => 'Other',
    ];
    $attachmentTypes = [
        '' => '-- Select --',
        'pinback' => 'Pinback',
        'screwback' => 'Screwback',
        'clasp' => 'Clasp',
        'ribbon_loop' => 'Ribbon Loop',
        'lanyard' => 'Lanyard',
        'brooch' => 'Brooch',
        'magnetic' => 'Magnetic',
        'none' => 'None',
        'other' => 'Other',
    ];
?>

<div class="partial-section">
    <h2 class="section-title">Medal Specifications</h2>
    <div class="form-group">
        <label for="type_label">Type/Label:</label>
        <select id="type_label" name="type_label">
            <?php foreach ($medalTypes as $value => $label): ?>
                <option 
                value="<?= esc($value) ?>" 
                <?= $isSelected('type_label',$value) ?>
            >
                <?= esc($label) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="campaign_award_name">Campaign/Award Name:</label>
        <input 
            type="text" 
            id="campaign_award_name" 
            name="campaign_award_name" 
            value="<?= $getValue('campaign_award_name') ?>"
            placeholder="e.g., Iron Cross, Purple Heart"
        >
    </div>
    <div class="form-group">
        <label for="material">Material:</label>
        <select id="material" name="material">
            <?php foreach ($materialTyoes as $value => $label): ?>
                <option 
                    value="<?= esc($value) ?>" 
                    <?= $isSelected('material',$value) ?>
                >
                    <?= esc($label) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="maker_manufacturer">Maker/Manufacturer:</label>
        <input 
            type="text" 
            id="maker_manufacturer" 
            name="maker_manufacturer" 
            value="<?= $getValue('maker_manufacturer') ?>"
            placeholder="e.g., R.S. Owens &amp; Co."
        >
    </div>
    <div class="form-group">
        <label for="attachment_type">Attachment Type:</label>
        <select id="attachment_type" name="attachment_type">
            <?php foreach ($attachmentTypes as $value => $label): ?>
                <option 
                    value="<?= esc($value) ?>" 
                    <?= $isSelected('attachment_type', $value) ?>
                >
                    <?= esc($label) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="engravings">Engravings:</label>
        <input 
            type="text" 
            id="engravings"
            name="engravings" 
            value="<?= $getValue('engravings') ?>"
        >
    </div>
    <div class="form-group">
        <label for="serial_number">Serial Number:</label>
        <input 
            type="text" 
            id="serial_number" 
            name="serial_number" 
            value="<?= $getValue('serial_number') ?>"
        >
    </div>
    <div class="form-group">
        <label for="ribbon_present">Ribbon Present:</label>
        <select id="ribbon_present" name="ribbon_present">
            <option value="">-- Select --</option>
            <option value="1" <?= $isSelected('ribbon_present', '1') ?>>Yes</option>
            <option value="0" <?= $isSelected('ribbon_present', '0') ?>>No</option>
        </select>
    </div>
    <div class="form-group">
        <label for="presentation_case_incl">Presentation Case Included:</label>
        <select id="presentation_case_incl" name="presentation_case_incl">
            <option value="">-- Select --</option>
            <option value="1" <?= $isSelected('presentation_case_incl', '1') ?>>Yes</option>
            <option value="0" <?= $isSelected('presentation_case_incl', '0') ?>>No</option>
        </select>
    </div>
</div>