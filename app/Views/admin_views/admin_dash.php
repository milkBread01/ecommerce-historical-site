<?php $this->extend('layouts/main'); ?>

<?php $this->section('main-page-wrapper'); ?>
    <main class="main-dashboard">
        <header class="dash-header">
            <h1>Welcome!</h1>
            <p>This is your admin dashboard. From here, you can manage products, view orders, and access various administrative functions.</p>
        </header>
        
        <div class="dash-body-wrapper">
            <!-- Product Management Section -->
            <section class="dash-category">
                <h2 class="category-title">Product Management</h2>
                <div class="dash-cards-grid">
                    <div class="dash-section">
                        <h3>Products</h3>
                        <p>Manage your product listings, including adding new products, editing existing ones, and removing outdated items.</p>
                        <div class="dash-buttons">
                            <a href="<?= site_url('admin/new-product-details') ?>" class="btn btn-primary">Add New Product</a>
                            <a href="<?= site_url('admin/edit-products-dash') ?>" class="btn btn-secondary">Edit Existing Products</a>
                        </div>
                    </div>

                    <div class="dash-section">
                        <h3>Category Management</h3>
                        <p>Create and manage product categories to organize your historical memorabilia collection effectively.</p>
                        <div class="dash-buttons">
                            <a href="#" class="btn btn-primary">Edit Categories</a>
                            <a href="#" class="btn btn-secondary">Add New Category</a>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Special Items Section -->
            <section class="dash-category">
                <h2 class="category-title">Special Items</h2>
                <div class="dash-cards-grid">
                    <div class="dash-section">
                        <h3>Featured Products</h3>
                        <p>Quickly manage featured products to showcase special items in your collection.</p>
                        <div class="dash-buttons">
                            <a href="#" class="btn btn-primary">Edit Featured Products</a>
                            <a href="#" class="btn btn-secondary">Add New Featured Product</a>
                        </div>
                    </div>

                    <div class="dash-section">
                        <h3>On Sale </h3>
                        <p>Quickly manage products on sale to highlight special offers and discounts.</p>
                        <div class="dash-buttons">
                            <a href="#" class="btn btn-primary">Edit On Sale Products</a>
                            <a href="#" class="btn btn-secondary">Add New On Sale Product</a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Orders & Fulfillment Section -->
            <section class="dash-category">
                <h2 class="category-title">Orders & Fulfillment</h2>
                <div class="dash-cards-grid">
                    <div class="dash-section">
                        <h3>Order Management</h3>
                        <p>View and manage customer orders, update order statuses, and handle returns or exchanges.</p>
                        <div class="dash-buttons">
                            <a href="#" class="btn btn-primary">View Orders</a>
                            <a href="#" class="btn btn-secondary">Manage Returns</a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Customer Relations Section -->
            <section class="dash-category">
                <h2 class="category-title">Customer Relations</h2>
                <div class="dash-cards-grid">
                    <div class="dash-section">
                        <h3>Customer Management</h3>
                        <p>Access customer information, manage customer accounts, and view customer activity.</p>
                        <div class="dash-buttons">
                            <a href="#" class="btn btn-primary">View Customers</a>
                            <a href="#" class="btn btn-secondary">Manage Accounts</a>
                        </div>
                    </div>
                    
                    <div class="dash-section">
                        <h3>Customer Inquiries</h3>
                        <p>Respond to customer inquiries and manage support tickets to ensure customer satisfaction.</p>
                        <div class="dash-buttons">
                            <a href="#" class="btn btn-primary">View Inquiries</a>
                            <a href="#" class="btn btn-secondary">Manage Tickets</a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Analytics & Reporting Section -->
            <section class="dash-category">
                <h2 class="category-title">Analytics & Reporting</h2>
                <div class="dash-cards-grid">
                    <div class="dash-section">
                        <h3>Reports & Analytics</h3>
                        <p>Generate reports on sales, inventory, and customer behavior to help make informed business decisions.</p>
                        <div class="dash-buttons">
                            <a href="#" class="btn btn-primary">View Reports</a>
                            <a href="#" class="btn btn-secondary">Export Data</a>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
<?php $this->endSection(); ?>