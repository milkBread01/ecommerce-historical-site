<!-- 
        'item_id', // fk from pk of items table
        'doc_type',           // letter, ID, manual, photo, map, certificate, etc.
        'date_of_issue',      // freeform (e.g., "1943-05-12", "May 1943", "c.1943")
        'language_label',
        'content_format',     // handwritten|typed|printed|photo|map|other
        'signature_present',  // tinyint(1)
        'stamp_seal_present', // tinyint(1)
        'page_count',        // int

-->
<?php 
    $docData = $specs ?? [];

    // get field value where priorety is: current Data (old() function) > db data > default value
    $getValue = function($field, $default='') use($docData){
        $oldValue = old($field);

        if( $oldValue !== null && $oldValue !== ''){
            return $oldValue;
        }

        if(isset($docData[$field]) && $docData[$field] !== null){
            return $docData[$field];
        }

        return $default;
    };

    $isSelected = function($field, $value) use ($getValue) {
        return $getValue($field) == $value ? 'selected' : '';

    };

    $contentFormats = [
        '' => '-- Select --',
        'handwritten' => 'Handwritten',
        'typed' => 'Typed',
        'printed' => 'Printed',
        'photo' => 'Photo',
        'map' => 'Map',
        'other' => 'Other',
    ];
?>

<div class = "partial-section">
    <h2 class="section-title">Document Specifications</h2>
    <div class="form-group">
        <label for="doc_type">Document Type:</label>
        <input 
            type="text" 
            id="doc_type" 
            name="doc_type" 
            value="<?= esc($getValue('doc_type')) ?>"
            placeholder="e.g., letter, ID, manual, photo, map, certificate"
        >
    </div>
    <div class="form-group">
        <label for="date_of_issue">Date of Issue:</label>
        <input 
            type="text" 
            id="date_of_issue" 
            name="date_of_issue" 
            value="<?= esc($getValue('date_of_issue')) ?>"
            placeholder="e.g., 1943-05-12, May 1943, c.1943"
        >
    </div>
    <div class="form-group">
        <label for="language_label">Language:</label>
        <input 
            type="text" 
            id="language_label" 
            name="language_label" 
            value="<?= esc($getValue('language_label')) ?>"
            placeholder="e.g., English, German"
        >
    </div>
    <div class="form-group">
        <label for="content_format">Content Format:</label>
        <select id="content_format" name="content_format">
            <?php foreach($contentFormats as $value => $label): ?>
                <option
                    value = "<?=esc($value)?>"
                    <?= $isSelected('content_format', $value)?>
                >
                    <?= esc($label)?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="signature_present">Signature Present:</label>
        <select id="signature_present" name="signature_present">
            <option value="">-- Select --</option>
            <option value="1" <?= $isSelected('signature_present', '1') ?>>Yes</option>
            <option value="0" <?= $isSelected('signature_present', '0') ?>>No</option>
        </select>
    </div>
    <div class="form-group">
        <label for="stamp_seal_present">Stamp/Seal Present:</label>
        <select id="stamp_seal_present" name="stamp_seal_present">
            <option value="">-- Select --</option>
            <option value="1" <?= $isSelected('stamp_seal_present', '1') ?>>Yes</option>
            <option value="0" <?= $isSelected('stamp_seal_present', '0') ?>>No</option>
        </select>
    </div>

    <div class="form-group">
        <label for="page_count">Page Count:</label>
        <input 
            type="number" 
            id="page_count" 
            name="page_count" 
            value="<?= esc($getValue('page_count')) ?>"
            min="0"
        >
    </div>

</div>
