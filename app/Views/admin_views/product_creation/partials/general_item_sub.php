<div class = "form-section">
    <h3 class="section-title">General Information</h3>
    <div class="form-group">
        <label for="product-name">Product Name:</label>
        <input 
            type="text" 
            id="product-name" 
            name="product_name" 
            value="<?= old('product_name') ?>"
            required
        >
        <?php if(isset($validation) && $validation->hasError('product_name')): ?>
            <div class="error-message"><?= $validation->getError('product_name') ?></div>
        <?php endif; ?>
        <div class="form-group">
            <label for="category">Category:</label>
            <select id="category" name="category_id" required>
                <option value="">-- Select Category --</option>
                <?php foreach($categories as $category): ?>
                    <option 
                        value="<?= $category['category_id'] ?>" 
                        <?= old('category_id') == $category['category_id'] ? 'selected' : '' ?>
                    >
                        <?= esc($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if(isset($validation) && $validation->hasError('category_id')): ?>
                <div class="error-message"><?= $validation->getError('category_id') ?></div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="sku">SKU:</label>
            <input 
                type="text" 
                id="sku" 
                name="sku" 
                value="<?= old('sku') ?>"
                required
            >
            <?php if(isset($validation) && $validation->hasError('sku')): ?>
                <div class="error-message"><?= $validation->getError('sku') ?></div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="price">Price (USD):</label>
            <input 
                type="number" 
                step="0.01" 
                id="price" 
                name="price" 
                value="<?= old('price') ?>" 
                required
            >
            <?php if(isset($validation) && $validation->hasError('price')): ?>
                <div class="error-message"><?= $validation->getError('price') ?></div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="stock-quantity">Stock Quantity:</label>
            <input 
                type="number" 
                id="stock-quantity" 
                name="stock_quantity" 
                value="<?= old('stock_quantity') ?>" 
                required
            >
            <?php if(isset($validation) && $validation->hasError('stock_quantity')): ?>
                <div class="error-message"><?= $validation->getError('stock_quantity') ?></div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <p>Is Product a Featured Item?</p>
            <label>
                <input 
                    type="radio" 
                    name="is_featured" 
                    value="1" 
                    <?= old('is_featured') == '1' ? 'checked' : '' ?>
                >
                Yes
            </label>
            <label>
                <input 
                    type="radio" 
                    name="is_featured" 
                    value="0" 
                    <?= old('is_featured') == '0' ? 'checked' : '' ?> 
                >
                No
            </label>
        </div>
        <div class="form-group">
            <p>Should Product be Visible in Store?</p>
            <label>
                <input 
                    type="radio" 
                    name="visible" 
                    value="1" 
                    <?= old('visible') == '1' ? 'checked' : '' ?>>
                Yes
            </label>
            <label>
                <input type="radio" name="visible" value="0" <?= old('visible') == '0' ? 'checked' : '' ?>>
                No
            </label>
            <div class="error-message">
                <?php if(isset($validation) && $validation->hasError('visible')): ?>
                    <?= $validation->getError('visible') ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>