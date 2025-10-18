<!-- 
 
        view file to load form to upload product images

-->


<?php $this->extend('layouts/main'); ?>

<?php $this->section('main-page-wrapper'); ?>
    <div class="npf-container">
        <div class="npf-form-container">
            <h2 class="form-title">Add New Product - Step 2: Product Images</h2>
            <h3 class="form-subtitle">Upload images for the product below:</h3>

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

                <div class="form-group">
                    <button type="submit" class="submit-button">Upload Product Images</button>
                </div>
            </form>

        </div>
    </div>
<?php $this->endSection(); ?>

