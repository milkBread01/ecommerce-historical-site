
<?php $this->extend('layouts/main'); ?>

<?php $this->section('main-page-wrapper'); ?>
    <main class="main-category-wrapper">
        <div class = "product-card-container">
            
            <div class="category-header">
                <h1 class="category-title"><?= esc($category['name']) ?></h1>
                
                <?php if (!empty($categories)): ?>
                <nav class="category-nav">
                    <?php foreach ($categories as $cat): ?>
                        <a href="<?= site_url('products/category/' . esc($cat['slug'])) ?>" 
                        class="category-link <?= $cat['category_id'] == $category['category_id'] ? 'active' : '' ?>">
                            <?= esc($cat['name']) ?>
                        </a>
                    <?php endforeach; ?>
                </nav>
                <?php endif; ?>
            </div>

            <?php if (empty($items)): ?>
                <div class="no-items">
                    <h3>No Products Available</h3>
                    <p>There are currently no products in this category. Please check back soon.</p>
                </div>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach ($items as $item): ?>
                        <div class="product-card">
                            <!-- Badge Section - only show if item has special flags -->
                            <div class="badge-section <?= empty($item['is_on_sale']) && empty($item['is_featured']) && empty($item['collection_id']) ? 'hidden' : '' ?>">
                                <?php if (!empty($item['is_on_sale'])): ?>
                                    <span class="badge sale">Sale</span>
                                <?php endif; ?>
                                <?php if (!empty($item['is_featured'])): ?>
                                    <span class="badge featured">Featured</span>
                                <?php endif; ?>
                                <?php if (!empty($item['collection_id'])): ?>
                                    <span class="badge collection">Collection</span>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Product Content -->
                            <div class="product-content">
                                <div class="product-image">
                                    <?php if (!empty($item['primary_image_path'])): ?>
                                        <img src="<?= base_url($item['primary_image_path']) ?>" 
                                            alt="<?= esc($item['name']) ?>">
                                    <?php else: ?>
                                        <span class="product-image-placeholder">No Image</span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="product-details">
                                    <div class="product-header">
                                        <h2 class="product-name"><?= esc($item['name']) ?></h2>
                                        <?php if (!empty($item['country_origin'])): ?>
                                            <div class="product-origin"><?= esc($item['country_origin']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <p class="product-teaser">
                                        <?= esc($item['teaser'] ?? 'Quality military collectible item') ?>
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Bottom Section -->
                            <div class="product-bottom">
                                <div class="stock-section">
                                    <?php 
                                    $stockClass = 'in-stock';
                                    $stockText = 'In Stock';
                                    
                                    if (empty($item['stock_quantity']) || $item['stock_quantity'] <= 0) {
                                        $stockClass = 'out-of-stock';
                                        $stockText = 'Out of Stock';
                                    } elseif ($item['stock_quantity'] <= 5) {
                                        $stockClass = 'low-stock';
                                        $stockText = 'Low Stock';
                                    }
                                    ?>
                                    <span class="stock-label <?= $stockClass ?>"><?= $stockText ?></span>
                                </div>
                                
                                <div class="price-section">
                                    <?php if (!empty($item['is_on_sale']) && !empty($item['sale_price'])): ?>
                                        <span class="price-old">$<?= number_format($item['price'], 2) ?></span>
                                        <span class="price-current">$<?= number_format($item['sale_price'], 2) ?></span>
                                    <?php else: ?>
                                        <span class="price-current">$<?= number_format($item['price'], 2) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="product-actions">
                                <a href="<?= site_url('products/category/' . esc($item['slug'])) ?>" class="pbtn btn-view">
                                    View Details
                                </a>
                                <button type="button" class="pbtn btn-add" onclick="addToCart(<?= $item['item_id'] ?>)">
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </main>
<?php $this->endSection(); ?>

