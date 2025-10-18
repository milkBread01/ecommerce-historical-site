<!-- 
        'item_id', // fk from pk of items table
        'title',
        'author',
        'publisher',
        'publication_year',   // e.g., "1943" or "c.1943"
        'edition_label',      // e.g., "1st ed.", "Revised"
        'binding_type',       // e.g., "Hardcover", "Softcover"
        'language_label',     // e.g., "English", "German"
        'pages_count',
        'condition_notes', 
-->

<div class = "partial-section">
    <h2 class="section-title">Book Specifications</h2>
    <div class="form-group">
        <label for="title">Title:</label>
        <input 
            type="text" 
            id="title" 
            name="title" 
            value="<?= old('title') ?>"
        >
    </div>
    <div class="form-group">
        <label for="author">Author:</label>
        <input 
            type="text" 
            id="author" 
            name="author" 
            value="<?= old('author') ?>"
        >
    </div>
    <div class="form-group">
        <label for="publisher">Publisher:</label>
        <input 
            type="text" 
            id="publisher" 
            name="publisher" 
            value="<?= old('publisher') ?>"
        >
    </div>
    <div class="form-group">
        <label for="publication_year">Publication Year:</label>
        <input 
            type="text" 
            id="publication_year" 
            name="publication_year" 
            value="<?= old('publication_year') ?>"
            placeholder="e.g., 1943 or c.1943"
        >
    </div>
    <div class="form-group">
        <label for="edition_label">Edition:</label>
        <input 
            type="text" 
            id="edition_label" 
            name="edition_label" 
            value="<?= old('edition_label') ?>"
            placeholder="e.g., 1st ed., Revised"
        >
    </div>
    <div class="form-group">
        <label for="binding_type">Binding Type:</label>
        <input 
            type="text" 
            id="binding_type" 
            name="binding_type" 
            value="<?= old('binding_type') ?>"
            placeholder="e.g., Hardcover, Softcover"
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
        <label for="pages_count">Number of Pages:</label>
        <input 
            type="number" 
            id="pages_count" 
            name="pages_count" 
            value="<?= old('pages_count') ?>"
            min="0"
        >
    </div>
    <div class="form-group">
        <label for="condition_notes">Condition Notes:</label>
        <textarea 
            id="condition_notes" 
            name="condition_notes"
        ><?= old('condition_notes') ?></textarea>
    </div>
</div>