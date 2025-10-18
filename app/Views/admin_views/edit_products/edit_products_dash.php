<?php $this->extend('layouts/main'); ?>

<?php $this->section('main-page-wrapper'); ?>
    <div class="main-dashboard">
        <header class="edit-dash-header">
            <h1>Edit Product Dashboard</h1>
            <p>Edit existing product information. Change product visibility status, price, image order, etc.</p>
            <br>
            <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-primary">
                Return to Dashboard
            </a>
        </header>

        <div class="edit-body-wrapper">
            <?php foreach ($categories as $parent_id => $catData): ?>
                <?php $parentUrl = site_url('admin/products/category/' . esc($catData['category']['slug'])); ?>

                <section class="category-card">
                <!-- full-card cover link (for parent) -->
                <a class="card-cover-link" href="<?= $parentUrl ?>" aria-label="Open <?= esc($catData['category']['name']) ?>"></a>

                <h2 class="card-title">
                    <a class="card-title-link" href="<?= $parentUrl ?>">
                    <?= esc($catData['category']['name']) ?>
                    </a>
                </h2>

                <?php if (!empty($catData['children'])): ?>
                    <ul class="child-list">
                    <?php foreach ($catData['children'] as $child): ?>
                        <li>
                        <a class="child-link" href="<?= site_url('admin/products/category/' . esc($child['slug'])) ?>">
                            <?= esc($child['name']) ?>
                        </a>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="child-empty">No subcategories</p>
                <?php endif; ?>
                </section>
            <?php endforeach; ?>
        </div>
    </div>
<?php $this->endSection(); ?>
