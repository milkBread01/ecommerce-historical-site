<!-- 
Item Information Section
    This section includes fields for entering general product details such as:
        'category_id',
        'sku',
        'name',
        'slug',
        'price',
        'is_featured',
        'visible',
        'stock_quantity',

Additional General information handled in seperate for section is as follows:
    'era_period',
    'country_origin',
    'branch_org',
    'unit_regiment',
    'authenticity',      // ENUM('Original','Reproduction','Replica w/ period parts','Unknown')
    'condition',         // ENUM('Mint','Excellent','Very Good','Good','Fair','Poor')
    'dimensions_label',
    'weight_label',
    'materials',
    'markings',
    'serial_numbers',
    'description',
    'provenance_source',
    'documentation',     // TINYINT(1)
    'certificate_auth',  // TINYINT(1)
    'video_url',
    'on_sale',           // TINYINT(1)
    'created_at',
    'updated_at',

The product categories contain the information:
    'slug',
    'name',
    'parent_id',       // nullable
    'is_visible',      // tinyint(1) default 1
    'is_temporary',    // tinyint(1) default 0 
 -->
<?php
    $authOptions = [
        'Original' => 'Original',
        'Reproduction' => 'Reproduction',
        'Replica w/ period parts' => 'Replica w/ period parts',
        'Unknown' => 'Unknown'
    ];

    $conditionOptions = [
        'Mint' => 'Mint',
        'Excellent' => 'Excellent',
        'Very Good' => 'Very Good',
        'Good' => 'Good',
        'Fair' => 'Fair',
        'Poor' => 'Poor'
    ];

    $documentationType = [
        'manual'=>'Manual',
        'certificate'=>'Certificate',
        'other'=>'Other',
    ];

    $certificateTypes = [
        'authenticity'=>'Certificate of Authenticity',
        'appraisal'=>'Appraisal Document',
        'provenance'=>'Provenance Document',
        'other'=>'Other',
    ];

    $dimensionUnitOptions = [
        'feet' => 'Feet',
        'inches'=>'Inches',
        'mils' => 'Mils',
        'meters'=>'Meters',
        'cm'=>'Centimeter',
        'mm'=>'Millimeter',
    ];

    $weightUnits = [
        'lbs'=>'Pounds (lbs)',
        'kg'=>'Kilograms (kg)',
        'g'=>'Grams (g)',
        'oz'=>'Ounces (oz)'
    ];

    $generalData = $item ?? [];

    // Get field value where priority is: old() > db data > default value
    $getValue = function($field, $default = '') use ($generalData) {
        $oldValue = old($field);
        
        // Handle old() returning an array (shouldn't happen for text fields, but just in case)
        if (is_array($oldValue)) {
            $oldValue = null;
        }
        
        // If old value exists and is not empty string, use it
        if ($oldValue !== null && $oldValue !== '') {
            return $oldValue;
        }
        
        // Check database data
        if (isset($generalData[$field])) {
            // Handle array values in database data
            if (is_array($generalData[$field])) {
                return $default;
            }
            if ($generalData[$field] !== null) {
                return $generalData[$field];
            }
        }
        
        return $default;
    };

    $isSelected = function($field, $value) use ($getValue) {
        return $getValue($field) == $value ? 'selected' : '';
    };

    // Do the same for the other getValue functions:
    $historicalData = $generalInfo ?? [];

    $getValueHistorical = function($field, $default = '') use ($historicalData) {
        $oldValue = old($field);
        
        if (is_array($oldValue)) {
            $oldValue = null;
        }
        
        if ($oldValue !== null && $oldValue !== '') {
            return $oldValue;
        }
        
        if (isset($historicalData[$field])) {
            if (is_array($historicalData[$field])) {
                return $default;
            }
            if ($historicalData[$field] !== null) {
                return $historicalData[$field];
            }
        }
        
        return $default;
    };

    $isSelectedHist = function($field, $value) use ($getValueHistorical) {
        return $getValueHistorical($field) == $value ? 'selected' : '';
    };

    $collectionData = $collection ?? [];

    $getValueCollection = function($field, $default = '') use ($collectionData) {
        $oldValue = old($field);
        
        if (is_array($oldValue)) {
            $oldValue = null;
        }
        
        if ($oldValue !== null && $oldValue !== '') {
            return $oldValue;
        }
        
        if (isset($collectionData[$field])) {
            if (is_array($collectionData[$field])) {
                return $default;
            }
            if ($collectionData[$field] !== null) {
                return $collectionData[$field];
            }
        }
        
        return $default;
    };

    $isSelectedCollection = function($field, $value) use ($getValueCollection) {
        return $getValueCollection($field) == $value ? 'selected' : '';
    };

    // Determine if we're editing an existing item with a collection
    $hasCollection = !empty($item['collection_id']);

    // Default entry type based on existing data
    $defaultEntryType = 'individual_item';

?>


<!-- Collection Information -->
<!-- COLLECTION ENTRY TYPE TOGGLE -->
<div class="form-group form-group--toggle">
    <p><strong>How would you like to proceed?</strong></p>
    <label>
        <input 
            type="radio" 
            name="entry_type" 
            value="individual_item" 
            <?= old('entry_type', $defaultEntryType) === 'individual_item' ? 'checked' : '' ?>
            id="individual-item"
            class="entry-type-radio"
        >
        <?= $hasCollection ? 'Individual Item (currently in a collection)' : 'Add Individual Item (optionally assign to existing collection)' ?>
    </label>
    <label>
        <input 
            type="radio" 
            name="entry_type" 
            value="create_collection"
            <?= old('entry_type') === 'create_collection' ? 'checked' : '' ?>
            id="create-collection"
            class="entry-type-radio"
        >
        Create New Collection
    </label>
</div>

<!-- SECTION 1: ASSIGN TO EXISTING COLLECTION (shown for individual items) -->
<div id="assign-to-collection-section" class="form-section" style="<?= old('entry_type', $defaultEntryType) === 'individual_item' ? 'display: block;' : 'display: none;' ?>">
    <h3 class="section-title">Collection Assignment (Optional)</h3>
    
    <?php if($hasCollection && !empty($collectionData)): ?>
        <div class="form-group info-box info">
            <p><strong>📦 Current Collection</strong></p>
            <p>This item is currently part of: <strong><?= esc($getValueCollection('collection_name')) ?></strong></p>
            <p>
                Sale Type: <strong><?= $getValueCollection('is_bundle_only') == '1' ? 'Bundle Only' : 'Individual Sales Allowed' ?></strong>
            </p>
            <?php if($getValueCollection('bundle_price')): ?>
                <p>Bundle Price: <strong>$<?= number_format($getValueCollection('bundle_price'), 2) ?></strong></p>
            <?php endif; ?>
            <p class="help-text">You can change or remove the collection assignment below.</p>
        </div>
    <?php else: ?>
        <p class="help-text">This item can be sold individually. Optionally assign it to an existing collection.</p>
    <?php endif; ?>
    
    <?php if($isNewCollection ?? false): ?>
        <div class="form-group info-box success">
            <p><strong>✓ Collection created!</strong></p>
            <p>It's now pre-selected below. Fill in the item details to add the first piece to this collection.</p>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <label for="collection-select">Select Collection (or leave blank for no collection):</label>
        <select id="collection-select" name="collection_id">
            <option value="">-- None / Not Part of Collection --</option>
            <?php foreach($collections as $coll): ?>
                <option 
                    value="<?= $coll['collection_id'] ?>"
                    <?php 
                        // Priority: old() > preSelectedCollectionId > current collection_id from item
                        $selectedCollectionId = old('collection_id') 
                            ?: ($preSelectedCollectionId ?? $getValue('collection_id', ''));
                        echo $selectedCollectionId == $coll['collection_id'] ? 'selected' : '';
                    ?>
                >
                    <?= esc($coll['collection_name']) ?>
                    <?php if($coll['is_bundle_only']): ?>
                        (Bundle Only)
                    <?php else: ?>
                        (Individual Sales Allowed)
                    <?php endif; ?>
                    <?php if(!empty($coll['bundle_price'])): ?>
                        - Bundle: $<?= number_format($coll['bundle_price'], 2) ?>
                    <?php endif; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="help-text">
            <?php if($hasCollection): ?>
                <strong>Tip:</strong> Select "None" to remove this item from its current collection.
            <?php else: ?>
                <strong>Tip:</strong> Collections help you group related items together.
            <?php endif; ?>
        </p>
        <?php if(isset($validation) && $validation->hasError('collection_id')): ?>
            <div class="error-message"><?= $validation->getError('collection_id') ?></div>
        <?php endif; ?>
    </div>
    
    <!-- Display selected collection info dynamically -->
    <div id="selected-collection-info" class="form-group info-box" style="display: none;">
        <p><strong>Selected Collection Details:</strong></p>
        <div id="collection-details-content"></div>
    </div>
</div>

<!-- SECTION 2: CREATE NEW COLLECTION (shown when creating collection) -->
<div id="create-collection-section" class="form-section" style="<?= old('entry_type') === 'create_collection' ? 'display: block;' : 'display: none;' ?>">
    <h3 class="section-title">New Collection Details</h3>
    
    <div class="form-group">
        <label for="collection-name">Collection Name:</label>
        <input 
            type="text" 
            id="collection-name" 
            name="collection_name" 
            value="<?= esc($getValueCollection('collection_name')) ?>"
        >
        <p class="help-text">e.g., "American Civil War Officer's Kit", "WWI German Helmet Set"</p>
        <?php if(isset($validation) && $validation->hasError('collection_name')): ?>
            <div class="error-message"><?= $validation->getError('collection_name') ?></div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="collection-description">Collection Historical Context:</label>
        <textarea 
            id="collection-description" 
            name="collection_description" 
            rows="4"
        ><?= esc($getValueCollection('collection_description')) ?></textarea>
        <p class="help-text">Explain the historical significance and why these items belong together</p>
        <?php if(isset($validation) && $validation->hasError('collection_description')): ?>
            <div class="error-message"><?= $validation->getError('collection_description') ?></div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <p><strong>How should this collection be sold?</strong></p>
        <label>
            <input 
                type="radio" 
                name="is_bundle_only" 
                value="0"
                <?= $getValueCollection('is_bundle_only', '0') == '0' ? 'checked' : '' ?>
                class="bundle-option"
            >
            Items can be purchased individually OR as a collection set
        </label>
        <label>
            <input 
                type="radio" 
                name="is_bundle_only" 
                value="1"
                <?= $getValueCollection('is_bundle_only', '0') == '1' ? 'checked' : '' ?>
                class="bundle-option"
            >
            Collection set only (no individual item sales)
        </label>
        <?php if(isset($validation) && $validation->hasError('is_bundle_only')): ?>
            <div class="error-message"><?= $validation->getError('is_bundle_only') ?></div>
        <?php endif; ?>
    </div>

    <div id="bundle-price-section" class="form-group" style="<?= $getValueCollection('is_bundle_only') == '1' ? 'display: block;' : 'display: none;' ?>">
        <label for="collection-price">Bundle Price (USD) - Optional:</label>
        <input 
            type="number" 
            step="0.01" 
            id="collection-price" 
            name="collection_price" 
            value="<?= esc($getValueCollection('bundle_price')) ?>"
        >
        <p class="help-text">Leave blank to calculate from individual item prices. Set a custom price for bundle discounts.</p>
        <?php if(isset($validation) && $validation->hasError('collection_price')): ?>
            <div class="error-message"><?= $validation->getError('collection_price') ?></div>
        <?php endif; ?>
    </div>

    <div class="form-group info-box">
        <p><strong>Next Steps:</strong></p>
        <p>After creating this collection, you'll be able to add multiple items to it. You can then assign this first item to the collection or create it as a standalone item.</p>
    </div>
</div>

<!-- General Information Section -->
<div class = "form-section">
    <h3 class="section-title">General Information</h3>

    <!-- PRODUCT NAME -->
    <div class="form-group">
        <label for="name">Product Name:</label>
        <input 
            type="text" 
            id="name" 
            name="name" 
            value="<?= esc($getValue('name')) ?>"
            required
        >
        <?php if(isset($validation) && $validation->hasError('name')): ?>
            <div class="error-message"><?= $validation->getError('name') ?></div>
        <?php endif; ?>
    </div>

    <!-- CATEGORY -->
    <div class="form-group">
        <label for="category_id">Category:</label>
        <select id="category_id" name="category_id" required>
            <option value="">-- Select Category --</option>
            
            <?php foreach($categories as $parentId => $categoryData): ?>
                <!-- Parent Category -->
                <optgroup label="<?= esc($categoryData['category']['name']) ?>">
                    <!-- Add parent as option if needed -->
                    <option 
                        value="<?= $categoryData['category']['category_id'] ?>"
                        <?= $isSelected('category_id', $categoryData['category']['category_id']) ?>
                    >
                        <?= esc($categoryData['category']['name']) ?>
                    </option>
                    
                    <!-- Child Categories -->
                    <?php foreach($categoryData['children'] as $child): ?>
                        <option 
                            value="<?= $child['category_id'] ?>"
                            <?= $isSelected('category_id', $child['category_id']) ?>
                        >
                            &nbsp;&nbsp;→ <?= esc($child['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </optgroup>
            <?php endforeach; ?>
        </select>
        
        <?php if(isset($validation) && $validation->hasError('category_id')): ?>
            <div class="error-message"><?= $validation->getError('category_id') ?></div>
        <?php endif; ?>
    </div>

    <!-- DESCRIPTION -->
    <div class="form-group">
        <label for="description">Please provide a detailed description of the product:</label>
        <br>
        <textarea 
            id="description" 
            name="description" 
            rows="4"
        ><?= esc($getValue('description')) ?></textarea>

        <?php if(isset($validation) && $validation->hasError('description')): ?>
            <div class="error-message"><?= $validation->getError('description') ?></div>
        <?php endif; ?>
    </div>

    <!-- Teaser -->
    <div class="form-group">
        <label for="short-description">Short Description (Teaser):</label>
        <br>
        <textarea 
            id="short-description" 
            name="teaser" 
            rows="2"
        ><?= esc($getValue('teaser')) ?></textarea>

        <?php if(isset($validation) && $validation->hasError('teaser')): ?>
            <div class="error-message"><?= $validation->getError('teaser') ?></div>
        <?php endif; ?>
    </div>

    <!-- SKU -->
    <div class="form-group">
        <label for="sku">SKU:</label>
        <input 
            type="text" 
            id="sku" 
            name="sku" 
            value="<?= esc($getValue('sku')) ?>"
            required
        >
        <?php if(isset($validation) && $validation->hasError('sku')): ?>
            <div class="error-message"><?= $validation->getError('sku') ?></div>
        <?php endif; ?>
    </div>

    <!-- PRICE -->
    <div class="form-group">
        <label for="price">Price (USD):</label>
        <input 
            type="number" 
            step="0.01" 
            id="price" 
            name="price" 
            value="<?= esc($getValue('price')) ?>" 
            required
        >
        <?php if(isset($validation) && $validation->hasError('price')): ?>
            <div class="error-message"><?= $validation->getError('price') ?></div>
        <?php endif; ?>
    </div>

    <!-- Is Product on Sale? -->
    <div class="form-group">
        <label for="on_sale">Is Product on Sale?</label>
        <select id="on_sale" name="on_sale">
            <option value="">-- Select Option --</option>
            <option value="1" <?= esc($getValue('on_sale', '1')) ?>>Yes</option>
            <option value="0" <?= esc($getValue('on_sale', '0')) ?>>No</option>
        </select>
        <?php if(isset($validation) && $validation->hasError('on_sale')): ?>
            <div class="error-message"><?= $validation->getError('on_sale') ?></div>
        <?php endif; ?>
    </div>

    <!-- Is Product Discounted by amount or percentage? -->
     <div class="form-group">
        <div class = "on-sale-info" id = "on-sale-info" style = "display:none">
            <p>Enter the Percentage or the Total Amount the Product Will Cost When on Sale:</p>
            <label for="percentage-discount">Percentage Discount (%):</label>
            <input 
                type="number" 
                step="0.01" 
                id="percentage-discount" 
                name="percentage_discount" 
                value="<?= esc($getValue('percentage_discount')) ?>"
            >
            <br><br>
            <label for="amount-discount">Total Cost of Product After Discount (USD):</label>
            <input 
                type="number" 
                step="0.01" 
                id="amount-discount" 
                name="amount_discount" 
                value="<?= esc($getValue('amount_discount')) ?>"
            >
        </div>
     </div>

    <!-- STOCK QUANTITY -->
    <div class="form-group">
        <label for="stock-quantity">Stock Quantity:</label>
        <input 
            type="number" 
            id="stock-quantity" 
            name="stock_quantity" 
            value="<?= esc($getValue('stock_quantity')) ?>" 
            required
        >
        <?php if(isset($validation) && $validation->hasError('stock_quantity')): ?>
            <div class="error-message"><?= $validation->getError('stock_quantity') ?></div>
        <?php endif; ?>
    </div>

    <!-- IS FEATURED -->
    <div class="form-group">
        <label for="is_featured">Is Product a Featured Item?</label>
        <select id="is_featured" name="is_featured">
            <option value="">-- Select Option --</option>
            <option value="1" <?= esc($getValue('is_featured', '0')) ?>>Yes</option>
            <option value="0" <?= esc($getValue('is_featured', '0')) ?>>No</option>
        </select>
    </div>

    <!-- IS VISIBLE -->
    <div class="form-group">
        <label for="visible">Should Product be Visible in Store?</label>
        <select id="visible" name="visible">
            <option value="">-- Select Option --</option>
            <option value="1" <?= esc($getValue('visible', '1')) ?>>Yes (Visible)</option>
            <option value="0" <?= esc($getValue('visible', '1'))  ?>>No (Hidden)</option>
        </select>
        <div class="error-message">
            <?php if(isset($validation) && $validation->hasError('visible')): ?>
                <?= $validation->getError('visible') ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Video URL -->
    <div class="form-group">
        <label for="video-url">(optional) Enter Video URL:</label>
        <input 
            type="url" 
            id="video-url" 
            name="video_url" 
            value="<?= esc($getValue('video_url')) ?>" 
        >
        <?php if(isset($validation) && $validation->hasError('video_url')): ?>
            <div class="error-message"><?= $validation->getError('video_url') ?></div>
        <?php endif; ?>
    </div>
</div>

<!-- Historical Information -->
<div class = "form-section">
    <h3 class="section-title">Historical Information Regarding Product </h3>

    <!-- ERA/PERIOD -->
    <div class="form-group">
        <label for="era-period">Era/Period:</label>
        <input 
            type="text" 
            id="era-period" 
            name="era_period" 
            value="<?= esc($getValueHistorical('era_period')) ?>" 
        >
        <?php if(isset($validation) && $validation->hasError('era_period')): ?>
            <div class="error-message"><?= $validation->getError('era_period') ?></div>
        <?php endif; ?>

    </div>

    <!-- COUNTRY OF ORIGIN -->
    <div class="form-group">
        <label for="country-origin">Country of Origin:</label>
        <input 
            type="text" 
            id="country-origin" 
            name="country_origin" 
            value="<?= esc($getValueHistorical('country_origin')) ?>" 
        >
        <?php if(isset($validation) && $validation->hasError('country_origin')): ?>
            <div class="error-message"><?= $validation->getError('country_origin') ?></div>
        <?php endif; ?>
    </div>
    
    <!-- BRANCH/ORGANIZATION -->
    <div class="form-group">
        <label for="branch-org">Branch/Organization:</label>
        <input 
            type="text" 
            id="branch-org" 
            name="branch_org" 
            value="<?= esc($getValueHistorical('branch_org')) ?>" 
        >
        <?php if(isset($validation) && $validation->hasError('branch_org')): ?>
            <div class="error-message"><?= $validation->getError('branch_org') ?></div>
        <?php endif; ?>
    </div>

    <!-- UNIT/REGIMENT -->
    <div class="form-group">
        <label for="unit-regiment">Unit/Regiment:</label>
        <input 
            type="text" 
            id="unit-regiment" 
            name="unit_regiment" 
            value="<?= esc($getValueHistorical('unit_regiment')) ?>" 
        >
        <?php if(isset($validation) && $validation->hasError('unit_regiment')): ?>
            <div class="error-message"><?= $validation->getError('unit_regiment') ?></div>
        <?php endif; ?>
    </div>

    <!-- AUTHENTICITY -->
    <div class="form-group">
        <label for="authenticity">Authenticity:</label>
        <select id="authenticity" name="authenticity">
            <option value="">-- Select Authenticity --</option>
            <?php foreach($authOptions as $value =>$label):  ?>
                <option 
                    value="<?= esc($value) ?>" 
                    <?= $isSelectedHist('authenticity', $value) ?>
                >
                    <?= esc($label) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if(isset($validation) && $validation->hasError('authenticity')): ?>
            <div class="error-message"><?= $validation->getError('authenticity') ?></div>
        <?php endif; ?>
    </div>

    <!-- Fix the CONDITION select field too -->
    <div class="form-group">
        <label for="condition">Condition:</label>
        <select id="condition" name="condition">
            <option value="">-- Select Condition --</option>
            <?php foreach($conditionOptions as $value => $label): ?>
                <option 
                    value="<?= esc($value) ?>"
                    <?= $isSelectedHist('condition', $value) ?>
                >
                    <?= esc($label) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if(isset($validation) && $validation->hasError('condition')): ?>
            <div class="error-message"><?= $validation->getError('condition') ?></div>
        <?php endif; ?>
    </div>

    <!-- DIMENSIONS -->
    <div class="form-group">
        <label class="help-text">Please enter the dimensions in the format: L x W x H (e.g., 10 x 5 x 3 cm)</label>
        <div class="dimension-inputs">
            <input 
                type="number" 
                step="0.01" 
                name="dimension_length" 
                placeholder="Length" 
                value="<?= esc($dimensionLength) ?? 0 ?>"
            >
            <input 
                type="number" 
                step="0.01" 
                name="dimension_width" 
                placeholder="Width" 
                value="<?= esc($dimensionWidth) ?? 0 ?>" 
            >
            <input 
                type="number" 
                step="0.01" 
                name="dimension_height" 
                placeholder="Height" 
                value="<?= esc($dimensionHeight) ?? 0 ?>" 
            >
            <select name="dimension_unit">
                <?php foreach($dimensionUnitOptions as $value => $label): ?>
                    <option 
                        value="<?= esc($value) ?>" 
                        <?= esc($isSelectedHist('dimension_unit', $value)) ?? 'inches' ?>
                    >
                        <?= esc($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php if(isset($validation) && ($validation->hasError('dimension_length') || $validation->hasError('dimension_width') || $validation->hasError('dimension_height') || $validation->hasError('dimension_unit'))): ?>
            <div class="error-message">
                <?= $validation->getError('dimension_length') ?>
                <?= $validation->getError('dimension_width') ?>
                <?= $validation->getError('dimension_height') ?>
                <?= $validation->getError('dimension_unit') ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- WEIGHT -->
    <div class="form-group">
        <p class="help-text">Please enter the weight and select a unit.</p>
        <div class="weight-inputs">
            <input 
                type="number" 
                step="0.01" 
                name="weight_value" 
                placeholder="Weight" 
                value="<?= esc($getValueHistorical('weight_value')) ?? 0?>" 
            >
            <select name="weight_unit">
                <?php foreach($weightUnits as $value => $label): ?>
                    <option 
                        value="<?= esc($value) ?>" 
                        <?= esc($isSelectedHist('weight_unit', $value)) ?? 'g' ?>
                    >
                        <?= esc($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php if(isset($validation) && ($validation->hasError('weight_value') || $validation->hasError('weight_unit'))): ?>
            <div class="error-message">
                <?= $validation->getError('weight_value') ?>
                <?= $validation->getError('weight_unit') ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- MATERIALS -->
    <div class="form-group">
        <p class="help-text">Please enter the materials used in the product</p>
        <div class="material-inputs">
            <input 
                type="text" 
                name="material_1" 
                placeholder="Material 1" 
                value="<?= esc($material1) ?? '-' ?>" 
            >
            <input 
                type="text" 
                name="material_2" 
                placeholder="Material 2" 
                value="<?= esc($material2) ?? '-'?>" 
            >
            <input 
                type="text" 
                name="material_3" 
                placeholder="Material 3" 
                value="<?= esc($material3) ?? '-'?>" 
            >
        </div>
        <?php if(isset($validation) && ($validation->hasError('material_1') || $validation->hasError('material_2') || $validation->hasError('material_3'))): ?>
            <div class="error-message">
                <?= $validation->getError('material_1') ?>
                <?= $validation->getError('material_2') ?>
                <?= $validation->getError('material_3') ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- MARKINGS -->
    <div class="form-group">
        <p class="help-text">Please enter any markings or inscriptions on the product, separated by commas.</p>
        <div class="marking-inputs">
            <input 
                type="text" 
                name="marking_1" 
                placeholder="Marking 1" 
                value="<?= esc($markings1) ?? '-'?>" 
            >
            <input 
                type="text" 
                name="marking_2" 
                placeholder="Marking 2" 
                value="<?= esc($markings2) ?? '-'?>" 
            >
            <input 
                type="text" 
                name="marking_3" 
                placeholder="Marking 3" 
                value="<?= esc($markings3) ?? '-'?>" 
            >
        </div>
        <?php if(isset($validation) && ($validation->hasError('marking_1') || $validation->hasError('marking_2') || $validation->hasError('marking_3'))): ?>
            <div class="error-message">
                <?= $validation->getError('marking_1') ?>
                <?= $validation->getError('marking_2') ?>
                <?= $validation->getError('marking_3') ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- SERIAL NUMBERS -->
    <div class="form-group">
        <label for="serial-numbers">Serial Number:</label>
        <input 
            type="text" 
            id="serial-numbers" 
            name="serial_numbers" 
            value="<?= ($getValueHistorical('serial_numbers')) ?>" 
        >
        <?php if(isset($validation) && $validation->hasError('serial_numbers')): ?>
            <div class="error-message"><?= $validation->getError('serial_numbers') ?></div>
        <?php endif; ?>
    </div>


    <!-- PROVENANCE SOURCE -->
    <div class="form-group">
        <label for="provenance-source">Provenance Source:</label>
        <input 
            type="text" 
            id="provenance-source" 
            name="provenance_source" 
            value="<?= esc($getValueHistorical('provenance_source')) ?>" 
        >
        <?php if(isset($validation) && $validation->hasError('provenance_source')): ?>
            <div class="error-message"><?= $validation->getError('provenance_source') ?></div>
        <?php endif; ?>
    </div>

    <!-- DOCUMENTATION -->
    <div class="form-group">
        <label for="documentation">Does the Product Include Documentation?</label>
        <select id="documentation" name="documentation">
            <option value="">-- Select --</option>
            <option value="1" <?= $isSelectedHist('documentation','1')?>>Yes</option>
            <option value="0" <?= $isSelectedHist('documentation','0')?>>No</option>
        </select>
        <?php if(isset($validation) && $validation->hasError('documentation')): ?>
            <div class="error-message"><?= $validation->getError('documentation') ?></div>
        <?php endif; ?>
    </div>
    <div class="form-group">
        <div class="documentation-inputs" id="documentation-inputs" style="display: none;">
            <p>Please specify the type of documentation.</p>
            <label for = "documentation_type">Please specify the type of documentation.</label>
            <select id="documentation_type" name = "documentation_type">
                <?php foreach($documentationType as $value =>$label): ?>
                    <option
                        value = "<?= esc($value)?>"
                        <?= $isSelectedHist('documentation_type', $value)?>
                    >
                        <?= esc($label)?>
                    </option>
                <?php endforeach; ?>
            </select>

            <?php if(isset($validation) && ($validation->hasError('documentation_type'))): ?>
                <div class="error-message">
                    <?= $validation->getError('documentation_type') ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- CERTIFICATE OF AUTHENTICITY -->
    <div class="form-group">
        <label for="certificate_auth">Does the Product Include a Certificate of Authenticity?</label>
        <select id = "certificate_auth" name="certificate_auth">
            <option value="">-- Select --</option>
            <option value="1" <?= $isSelectedHist('certificate_auth','1')?>>Yes</option>
            <option value="0" <?= $isSelectedHist('certificate_auth','0')?>>No</option>
        </select>
        
        <?php if(isset($validation) && $validation->hasError('certificate_auth')): ?>
            <div class="error-message"><?= $validation->getError('certificate_auth') ?></div>
        <?php endif; ?>
    </div>

    <div class = "form-group">
        <div class="certificate-inputs" id="certificate-inputs" style="display: none;">
            <label for="certificate_type">Please specify the type of certificate of authenticity.</label>
            <select id="certificate_type" name = "certificate_type">
                <?php foreach($certificateTypes as $value =>$label): ?>
                    <option
                        value = "<?= esc($value)?>"
                        <?= $isSelectedHist('certificate_type', $value)?>
                    >
                        <?= esc($label)?>
                    </option>
                <?php endforeach; ?>
            </select>

            <?php if(isset($validation) && ($validation->hasError('certificate_type'))): ?>
                <div class="error-message">
                    <?= $validation->getError('certificate_type') ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>
