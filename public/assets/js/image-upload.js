try{
    console.log("Image Upload JS Loaded");
    
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('fileInput');
        const previewContainer = document.getElementById('preview-container');
        const uploadTrigger = document.getElementById('upload-trigger');

        let sortableInstance = null;
        
        // Store files that will be uploaded (don't clear the input!)
        let selectedFiles = [];

        // Function to log current image order state
        function logImageOrder(action = 'Current State') {
            console.log(`\n===== ${action} =====`);
            const cards = previewContainer.querySelectorAll('.image-card');
            
            if (cards.length === 0) {
                console.log('No images in container');
                return;
            }
            
            cards.forEach((card, index) => {
                const titleInput = card.querySelector('input[name^="title"]');
                const orderInput = card.querySelector('input[name^="image_order"]');
                const existingIdInput = card.querySelector('input[name^="existing_image_id"]');
                
                const title = titleInput ? titleInput.value : 'No title';
                const order = orderInput ? orderInput.value : 'No order';
                const existingId = existingIdInput ? existingIdInput.value : 'NEW';
                const isExisting = existingIdInput && existingIdInput.value;
                
                console.log(`  [${index}] Order: ${order} | ${isExisting ? 'EXISTING' : 'NEW'} | ID: ${existingId} | Title: "${title}"`);
            });
            console.log('==================\n');
        }

        // Function to update order badges and hidden inputs
        function updateImageOrder() {
            const cards = previewContainer.querySelectorAll('.image-card');
            
            console.log('\n🔄 Updating image order...');
            
            cards.forEach((card, newIndex) => {
                const badge = card.querySelector('.order-badge');
                const oldOrder = badge.textContent;
                badge.textContent = `Order ${newIndex + 1}`;
                
                const orderInput = card.querySelector('input[name^="image_order"]');
                const oldValue = orderInput.value;
                orderInput.value = newIndex;
                
                const titleInput = card.querySelector('input[name^="title"]');
                const title = titleInput ? titleInput.value : 'No title';
                
                if (oldValue != newIndex) {
                    console.log(`  ➜ "${title}": position ${oldValue} → ${newIndex}`);
                }
            });
            
            logImageOrder('After Order Update');
        }

        // Initialize Sortable on existing images if any
        function initSortable() {
            if (sortableInstance) {
                sortableInstance.destroy();
            }
            
            if (previewContainer.children.length > 0) {
                sortableInstance = new Sortable(previewContainer, {
                    animation: 150,
                    handle: '.drag-handle',
                    ghostClass: 'sortable-ghost',
                    dragClass: 'sortable-drag',
                    onStart: function(evt) {
                        const titleInput = evt.item.querySelector('input[name^="title"]');
                        const title = titleInput ? titleInput.value : 'No title';
                        console.log(`\n🎯 Started dragging: "${title}" from position ${evt.oldIndex}`);
                    },
                    onEnd: function(evt) {
                        const titleInput = evt.item.querySelector('input[name^="title"]');
                        const title = titleInput ? titleInput.value : 'No title';
                        console.log(`📍 Dropped: "${title}" at position ${evt.newIndex}`);
                        
                        updateImageOrder();
                    }
                });
                
                console.log(`✅ Sortable initialized with ${previewContainer.children.length} images`);
            }
        }

        // Wire up remove buttons for existing images
        function wireRemoveButtons() {
            previewContainer.querySelectorAll('.remove-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const card = this.closest('.image-card');
                    const titleInput = card.querySelector('input[name^="title"]');
                    const title = titleInput ? titleInput.value : 'No title';
                    const existingIdInput = card.querySelector('input[name^="existing_image_id"]');
                    const existingId = existingIdInput ? existingIdInput.value : 'NEW';
                    
                    card.classList.toggle('to-be-removed');
                    const flag = card.querySelector('input[name^="remove"]');
                    const newValue = flag.value === '0' ? '1' : '0';
                    flag.value = newValue;
                    
                    if (newValue === '1') {
                        console.log(`\n🗑️ Marked for removal: "${title}" (ID: ${existingId})`);
                    } else {
                        console.log(`\n↩️ Unmarked for removal: "${title}" (ID: ${existingId})`);
                    }
                });
            });
        }

        // Initialize on page load if there are existing images
        console.log('\n🚀 Initializing image upload interface...');
        wireRemoveButtons();
        initSortable();
        
        // Log initial state
        logImageOrder('Initial Load');

        uploadTrigger.addEventListener('click', () => {
            console.log('\n📂 Opening file picker...');
            fileInput.click();
        });

        // Handle new file selection
        fileInput.addEventListener('change', () => {
            console.log('\n📥 File input change event fired');
            console.log(`   Files in input: ${fileInput.files.length}`);
            
            const existingCards = previewContainer.querySelectorAll('.image-card');
            const startIndex = existingCards.length;
            const fileCount = fileInput.files.length;

            console.log(`   Selected ${fileCount} new file(s)`);
            console.log(`   Starting index: ${startIndex}`);
            
            if (fileCount === 0) {
                console.log('   No files selected');
                return;
            }

            // Store the files in our array (they'll stay in the FileList for form submission)
            selectedFiles = Array.from(fileInput.files);
            console.log(`   Stored ${selectedFiles.length} files for upload`);
            
            // Verify files are actually there
            for (let i = 0; i < fileInput.files.length; i++) {
                console.log(`   File ${i}: ${fileInput.files[i].name} (${fileInput.files[i].size} bytes)`);
            }

            selectedFiles.forEach((file, idx) => {
                const actualIndex = startIndex + idx;
                const reader = new FileReader();
                
                console.log(`   [${actualIndex}] Processing: "${file.name}" (${(file.size / 1024).toFixed(2)} KB)`);
                
                reader.onload = e => {
                    const rawName = file.name || "";
                    const baseName = rawName.replace(/\.[^.]+$/,"");
                    const prettyName = baseName.replace(/[_-]+/g, " ").replace(/\s+/g, " ").trim();

                    console.log(`   ✓ Loaded: "${prettyName}" at position ${actualIndex}`);
                    
                    const card = document.createElement('div');
                    card.className = 'image-card';
                    card.dataset.index = actualIndex;
                    card.dataset.isNew = 'true';

                    card.innerHTML = `
                        <div class="order-badge">Order ${actualIndex + 1}</div>
                        <div class="drag-handle">⋮⋮</div>
                        <img src="${e.target.result}" class="thumb" />
                        <div class="meta-fields">
                            <label>
                                Title
                                <input 
                                    type="text" 
                                    name="title[${actualIndex}]" 
                                    value="${prettyName}"
                                />
                            </label>

                            <label>
                                Description
                                <textarea name="description[${actualIndex}]" rows="3"></textarea>
                            </label>
                        </div>

                        <button 
                            type="button" 
                            class="remove-btn"
                        >Remove</button>

                        <input 
                            type="hidden" 
                            name="remove[${actualIndex}]" 
                            value="0"
                        />
                        <input 
                            type="hidden" 
                            name="image_order[${actualIndex}]" 
                            value="${actualIndex}"
                        />
                        <!-- Mark as new image (no existing_image_id) -->
                    `;

                    card.querySelector('.remove-btn').addEventListener('click', () => {
                        const titleInput = card.querySelector('input[name^="title"]');
                        const title = titleInput ? titleInput.value : 'No title';
                        
                        card.classList.toggle('to-be-removed');
                        const flag = card.querySelector('input[name^="remove"]');
                        const newValue = flag.value === '0' ? '1' : '0';
                        flag.value = newValue;
                        
                        if (newValue === '1') {
                            console.log(`\n🗑️ Marked NEW image for removal: "${title}" (position ${actualIndex})`);
                        } else {
                            console.log(`\n↩️ Unmarked NEW image: "${title}" (position ${actualIndex})`);
                        }
                    });

                    previewContainer.appendChild(card);

                    // Re-initialize Sortable after adding all new images
                    const currentCards = previewContainer.querySelectorAll('.image-card');
                    if (currentCards.length === startIndex + fileCount) {
                        console.log(`\n✅ All ${fileCount} new images added`);
                        updateImageOrder();
                        initSortable();
                    }
                };
                
                reader.onerror = function(error) {
                    console.error(`❌ Failed to read file "${file.name}":`, error);
                };
                
                reader.readAsDataURL(file);
            });
            
            // CRITICAL: DO NOT clear the file input!
            // The files need to remain in the input for form submission
            // fileInput.value = ''; // ❌ REMOVED THIS LINE
            console.log('   File input retained for form submission');
        });
        
        // Log when form is submitted
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                console.log('\n📤 Form submitting...');
                logImageOrder('Final State Before Submit');
                
                // Verify files are still in the input
                console.log(`\nFile input contains: ${fileInput.files.length} file(s)`);
                for (let i = 0; i < fileInput.files.length; i++) {
                    console.log(`  File ${i}: ${fileInput.files[i].name}`);
                }
                
                // Show summary
                const allCards = previewContainer.querySelectorAll('.image-card');
                const existingCount = previewContainer.querySelectorAll('input[name^="existing_image_id"]').length;
                const newCount = allCards.length - existingCount;
                const markedForRemoval = Array.from(previewContainer.querySelectorAll('input[name^="remove"]'))
                    .filter(input => input.value === '1').length;
                
                console.log('\n📊 Submission Summary:');
                console.log(`   Total images: ${allCards.length}`);
                console.log(`   Existing images: ${existingCount}`);
                console.log(`   New uploads: ${newCount}`);
                console.log(`   Files in input: ${fileInput.files.length}`);
                console.log(`   Marked for removal: ${markedForRemoval}`);
                console.log('==================\n');
            });
        }
    });
}catch(err){
    console.error('❌ Image upload error:', err);
    console.error('Stack trace:', err.stack);
}