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


<div class = "partial-section">
    <h2 class="section-title">Document Specifications</h2>
    <div class="form-group">
        <label for="doc_type">Document Type:</label>
        <input 
            type="text" 
            id="doc_type" 
            name="doc_type" 
            value="<?= old('doc_type') ?>"
            placeholder="e.g., letter, ID, manual, photo, map, certificate"
        >
    </div>
    <div class="form-group">
        <label for="date_of_issue">Date of Issue:</label>
        <input 
            type="text" 
            id="date_of_issue" 
            name="date_of_issue" 
            value="<?= old('date_of_issue') ?>"
            placeholder="e.g., 1943-05-12, May 1943, c.1943"
        >
    </div>
    <div class="form-group">
        <label for="language_label">Language:</label>
        <input 
            type="text" 
            id="language_label" 
            name="language_label" 
            value="<?= old('language_label') ?>"
            placeholder="e.g., English, German"
        >
    </div>
    <div class="form-group">
        <label for="content_format">Content Format:</label>
        <select id="content_format" name="content_format">
            <option value="">-- Select --</option>
            <option value="handwritten" <?= old('content_format') === 'handwritten' ? 'selected' : '' ?>>Handwritten</option>
            <option value="typed" <?= old('content_format') === 'typed' ? 'selected' : '' ?>>Typed</option>
            <option value="printed" <?= old('content_format') === 'printed' ? 'selected' : '' ?>>Printed</option>
            <option value="photo" <?= old('content_format') === 'photo' ? 'selected' : '' ?>>Photo</option>
            <option value="map" <?= old('content_format') === 'map' ? 'selected' : '' ?>>Map</option>
            <option value="other" <?= old('content_format') === 'other' ? 'selected' : '' ?>>Other</option>
        </select>
    </div>
    <div class="form-group">
        <label for="signature_present">Signature Present:</label>
        <select id="signature_present" name="signature_present">
            <option value="">-- Select --</option>
            <option value="1" <?= old('signature_present') === '1' ? 'selected' : '' ?>>Yes</option>
            <option value="0" <?= old('signature_present') === '0' ? 'selected' : '' ?>>No</option>
        </select>
    </div>
    <div class="form-group">
        <label for="stamp_seal_present">Stamp/Seal Present:</label>
        <select id="stamp_seal_present" name="stamp_seal_present">
            <option value="">-- Select --</option>
            <option value="1" <?= old('stamp_seal_present') === '1' ? 'selected' : '' ?>>Yes</option>
            <option value="0" <?= old('stamp_seal_present') === '0' ? 'selected' : '' ?>>No</option>
        </select>
    </div>

    <div class="form-group">
        <label for="page_count">Page Count:</label>
        <input 
            type="number" 
            id="page_count" 
            name="page_count" 
            value="<?= old('page_count') ?>"
            min="0"
        >
    </div>

</div>
