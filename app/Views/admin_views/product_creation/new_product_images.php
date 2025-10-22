
<?php $this->extend('layouts/main'); ?>

<?php $this->section('main-page-wrapper'); ?>
    <div class="npf-container">
        <div class="npf-form-container">
            <h2 class="form-title">Add New Product - Step 2: Product Images</h2>
            <h3 class="form-subtitle">Upload images for the product below:</h3>
            <p class = "sub-text">Upload product images below. The first image will be the primary image but image order can be changed later in the edit menu.</p>
            <form 
                action="<?= site_url('admin/validate_npf_images') ?>" 
                method="post" 
                class="npf-form"
                enctype="multipart/form-data"
            >
                <?= csrf_field() ?>

                <input type="hidden" name="item_id" value="<?= esc($item_id ?? 0) ?>">
                
                <!-- load general information -->
                <?= view('admin_views/product_creation/partials/upload-image-general') ?>
                <br><br>
                <hr>
                <br><br>
                <p class="sub-text">Note: After Uploading a set of images, if you want to append an image it will replace the previous set. This includes any input information such as image titles and descriptions. <br><br>If you have already added images, please re-upload the entire set with the new images included. If the existing images have associated titles and descriptions, <strong>they will be lost.</strong></p>

                <div class="form-group">
                    <button type="submit" class="submit-button">Upload Product Images</button>
                </div>
            </form>

        </div>
    </div>
<?php $this->endSection(); ?>

