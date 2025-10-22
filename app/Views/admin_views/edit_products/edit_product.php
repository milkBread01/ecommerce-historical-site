<?php $this->extend('layouts/main'); ?>

<?php $this->section('main-page-wrapper'); ?>
    <div class="npf-container">
        <div class="npf-form-container">
            <h2 class="form-title">Edit Product</h2>
            <h3 class="form-subtitle">Change Product Specs as Needed</h3>
            <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-primary">
                Return to Admin Dashboard
            </a>
            <br>
            <br>
            <hr>
            <br>

            <form 
                action="<?= site_url('admin/validate_edit_product') ?>" 
                method="post" 
                class="npf-form"
            >
                <?= csrf_field() ?>
                
                <!-- Hidden field for item_id -->
                <input type="hidden" name="item_id" value="<?= esc($item['item_id']) ?>">
                
                <script>
                    window.APP = {
                        routes: {
                            productFields: "<?= site_url('admin/partials/product-fields') ?>",
                        },
                        csrf: {
                            name: "<?= csrf_token() ?>",
                            value: "<?= csrf_hash() ?>",
                        }
                    };
                </script>

                <script src="<?= site_url('assets/js/new-product.js') ?>" defer></script>
                <script src="<?= site_url('assets/js/collection.js') ?>" defer></script>
                <script src="<?= site_url('assets/js/conditional-load.js') ?>" defer></script>
                
                <!-- load general information -->
                <?= view('admin_views/product_creation/partials/general') ?>
                
                <!-- load product specific information based on category -->
                <div id="product-specific-fields">
                    <?php 
                    // Load the category-specific partial with specs on initial page load
                    // Use $parentSlug which was calculated in the controller
                    if (!empty($parentSlug)) {
                        try {
                            echo view('admin_views/product_creation/partials/' . $parentSlug, [
                                'specs' => $specs
                            ]);
                        } catch (\Exception $e) {
                            log_message('error', 'Could not load partial for slug: ' . $parentSlug . ' - ' . $e->getMessage());
                            echo '<p>Could not load category-specific fields.</p>';
                        }
                    }
                    ?>
                </div>

                <div class="form-group">
                    <button type="submit" class="submit-button">Update Product</button>
                </div>
            </form>

        </div>

        <br><br><br>

        <div class ="edit-image-container">
            <h2 class = "section-header-1">Edit the order of remove, and add new images. Change the title/description of each image. </h2>
            <form 
                action="<?= site_url('admin/validate_npf_images') ?>" 
                method="post" 
                class="npf-form"
                enctype="multipart/form-data"
            >
                <?= csrf_field() ?>

                <input type="hidden" name="item_id" value="<?= esc($item['item_id'] ?? 0) ?>">
                
                <!-- load general information -->
                <?= view('admin_views/product_creation/partials/upload-image-general') ?>
                <br><br>
                <hr>
                <br><br>
                <p class="sub-text">Note: To save edits, please click the 'Save Edits' button. </p>
                <div class="form-group">
                    <button type="submit" class="submit-button">Save Edits</button>
                </div>
            </form>
        </div>
    </div>
<?php $this->endSection(); ?>