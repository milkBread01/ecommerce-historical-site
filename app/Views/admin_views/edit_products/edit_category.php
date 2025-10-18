<?php $this->extend('layouts/main'); ?>

<?php $this->section('main-page-wrapper'); ?>
    <main class="edit-category-wrapper">
        <div class="category-header">
            <h1><?= esc($category['name']) ?></h1>
            <p class="category-breadcrumb">
                <?php if ($is_parent_category): ?>
                    <span class="badge badge-primary">Parent Category</span>
                <?php else: ?>
                    <span class="badge badge-secondary">Child Category</span>
                <?php endif; ?>
            </p>
        </div>

        <div class="products-grid">
            <?php if (empty($items_with_details)): ?>
                <div class="no-products">
                    <p>No products found in this category.</p>
                    <a href="<?= base_url('admin/new-product-details') ?>" class="btn btn-primary">
                        Add New Product
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($items_with_details as $itemData): ?>
                    <?php 
                        $item = $itemData['item'];
                        $generalInfo = $itemData['general_info'];
                        $specs = $itemData['specs'];
                    ?>
                    <div class="product-card">
                        <div class="product-card-header">
                            <h3 class="product-name"><?= esc($item['name']) ?></h3>
                            <div class="product-status">
                                <?php if ($item['visible']): ?>
                                    <span class="status-badge published">Published</span>
                                <?php else: ?>
                                    <span class="status-badge unpublished">Unpublished</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="product-card-body">
                            <?php if (!empty($item['sku'])): ?>
                                <p class="product-sku">SKU: <?= esc($item['sku']) ?></p>
                            <?php endif; ?>
                            
                            <?php if (!empty($item['price'])): ?>
                                <p class="product-price">$<?= number_format($item['price'], 2) ?></p>
                            <?php endif; ?>

                            <?php if ($generalInfo): ?>
                                <div class="product-details">
                                    <?php if (!empty($generalInfo['era_period'])): ?>
                                        <span class="detail-badge"><?= esc($generalInfo['era_period']) ?></span>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($generalInfo['country_origin'])): ?>
                                        <span class="detail-badge"><?= esc($generalInfo['country_origin']) ?></span>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($generalInfo['condition'])): ?>
                                        <span class="detail-badge condition"><?= esc($generalInfo['condition']) ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="product-card-footer">
                            <a href="<?= base_url('admin/edit-product/' . $item['item_id']) ?>" 
                               class="btn btn-edit">
                                Edit
                            </a>
                            
                            <button type="button" 
                                    class="btn btn-toggle-visibility" 
                                    data-item-id="<?= $item['item_id'] ?>"
                                    data-visible="<?= $item['visible'] ?>">
                                <?= $item['visible'] ? 'Unpublish' : 'Publish' ?>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <script>
        // Toggle visibility functionality
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButtons = document.querySelectorAll('.btn-toggle-visibility');
            
            toggleButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = this.dataset.itemId;
                    const currentVisible = this.dataset.visible === '1';
                    
                    // Make AJAX request to toggle visibility
                    fetch(`<?= base_url('admin/toggle-product-visibility') ?>`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            item_id: itemId,
                            visible: !currentVisible
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update the page
                            location.reload();
                        } else {
                            alert('Error updating visibility');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error updating visibility');
                    });
                });
            });
        });
    </script>
<?php $this->endSection(); ?>