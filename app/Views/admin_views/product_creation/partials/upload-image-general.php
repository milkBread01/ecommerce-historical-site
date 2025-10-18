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
<p>Upload product images below. The first image will be the primary image but image order can be changed later in the edit menu.</p>
<div class = "form-group">
    <label class="form-label">Select Images</label>
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

    <p class="sub-text">Note: After Uploading a set of images, if you want to append an image it will replace the previous set. This includes any input information such as image titles and descriptions. <br><br>If you have already added images, please re-upload the entire set with the new images included. If the existing images have associated titles and descriptions, <strong>they will be lost.</strong></p>
</div>

<!-- will be populated by JS -->
<div class="form-group" id="preview-container">

</div>

<script src="<?= site_url('assets/js/image-upload.js') ?>" defer></script>