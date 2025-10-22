
<?php $this->extend('layouts/main'); ?>

<?php $this->section('main-page-wrapper'); ?>
    <main class="main-home-container">
        <h1 class = "category-title">CLICK ME TO GO TO ADMIN PORTAL</h1>
        <br>
        <div class="dash-buttons">
            <a href="<?= site_url('admin/dashboard') ?>" class="btn btn-primary">Admin Dashboard</a>
        </div>

    </main>
<?php $this->endSection(); ?>

