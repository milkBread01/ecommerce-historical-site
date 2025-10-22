<!-- 
 
        'item_id',
        'file_path',      // e.g. uploads/products/123/main.jpg
        'url',            // optional CDN/external URL
        'title',
        'description',
        'alt_text',
        'image_order',    // integer sort within item
        'is_primary',     // tinyint(1)
        'width_px',
        'height_px',
        'checksum_sha1',
        'uploaded_at',    // if your schema stores this timestamp

-->
<br>
<div class = "form-group">
    <label class="form-label">Select Images</label><br>
    <div class="upload-area" id="upload-trigger">
        <div class="upload-icon">+</div>
        <div class="upload-text">Browse…</div>
    </div>

    <input 
        type="file" 
        id="fileInput"
        name="product_images[]" 
        multiple 
        accept="image/*" 
        style="display: none;"
    >
</div>

<!-- will be populated by JS -->
<div class="form-group" id="preview-container">
    <?php if(!empty($images)):?>
        <?php foreach($images as $idx => $image): ?>
            <div class="image-card" data-index="<?= $idx ?>" data-existing-id="<?= esc($image['image_id']) ?>">
                <div class="order-badge">Order <?= $idx + 1 ?></div>
                <div class="drag-handle">⋮⋮</div>
                <img src="<?= base_url($image['file_path']) ?>" class="thumb" />
                
                <div class="meta-fields">
                    <label>
                        Title
                        <input 
                            type="text" 
                            name="title[<?= $idx ?>]" 
                            value="<?= esc($image['title'] ?? '') ?>"
                        />
                    </label>

                    <label>
                        Description
                        <textarea name="description[<?= $idx ?>]" rows="3"><?= esc($image['description'] ?? '') ?></textarea>
                    </label>
                </div>

                <button 
                    type="button" 
                    class="remove-btn"
                >Remove</button>

                <!-- hidden fields -->
                <input 
                    type="hidden" 
                    name="remove[<?= $idx ?>]" 
                    value="0"
                />
                <input 
                    type="hidden" 
                    name="image_order[<?= $idx ?>]" 
                    value="<?= $idx ?>"
                />
                <!-- Store the existing image ID so backend knows which DB record to update -->
                <input 
                    type="hidden" 
                    name="existing_image_id[<?= $idx ?>]" 
                    value="<?= esc($image['image_id']) ?>"
                />
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

<script src="<?= site_url('assets/js/image-upload.js') ?>" defer></script>