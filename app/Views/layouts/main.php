<!-- layouts/main.php -->
<?= view('shared_views/header', ['userName' => session()->get('username')]) ?>

<main class="main-content-wrapper">
    <?= $this->renderSection('main-page-wrapper') ?>
</main>

<?= view('shared_views/footer') ?>
