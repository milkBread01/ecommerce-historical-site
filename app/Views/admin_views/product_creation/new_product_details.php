<?php $this->extend('layouts/main'); ?>

<?php $this->section('main-page-wrapper'); ?>
    <div class="npf-container">
        <div class="npf-form-container">
            <h2 class="form-title">Add New Product - Step 1: Product Details</h2>
            <h3 class="form-subtitle">Enter the product specifications below:</h3>

            <form 
                action="<?= site_url('admin/validate_npf_details') ?>" 
                method="post" 
                class="npf-form"
            >
                <?= csrf_field() ?>
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
                    <!-- Dynamic fields will be loaded here based on selected category -->
                </div>

                <div class="form-group">
                    <button type="submit" class="submit-button">Next Page. Product Images</button>
                </div>
            </form>

        </div>
    </div>
<?php $this->endSection(); ?>
