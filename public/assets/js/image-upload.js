try{
    console.log("Image Upload JS ")
    document.addEventListener('DOMContentLoaded', function() {
        /* id of input for file input */
        const fileInput = document.getElementById('fileInput');
        /* div where html will be injected into */
        const previewContainer = document.getElementById('preview-container');
        /* div pressed to initiate file upload menu */
        const uploadTrigger = document.getElementById('upload-trigger');

        // map file upload click to div click
        uploadTrigger.addEventListener('click', () => {
            fileInput.click();
        });

        // Handle file selection
        fileInput.addEventListener('change', () => {
            // Clear out any existing cards
            previewContainer.innerHTML = '';
            const imageOrder = {};

            Array.from(fileInput.files).forEach((file, idx) => {
                const reader = new FileReader();
                reader.onload = e => {
                    const rawName = file.name || "";

                    const baseName = rawName.replace(/\.[^.]+$/,"");

                    const prettyName = baseName.replace(/[_-]+/g, " ").replace(/\s+/g, " ").trim();

                    console.log(`Pretty Name ${prettyName}`)
                    
                    // Build the card
                    const card = document.createElement('div');
                    card.className = 'image-card';
                    card.dataset.index = idx;

                    card.innerHTML = `
                        <div class="order-badge">Order ${idx + 1}</div>
                        <img src="${e.target.result}" class="thumb" />
                        <div class="meta-fields">
                            <label>
                                Title
                                <input 
                                    type="text" 
                                    name="title[${idx}]" 
                                    value="${prettyName}"
                                     
                                />
                            </label>

                            <label>
                                Description
                                <textarea name="description[${idx}]" rows="3"></textarea>
                            </label>
                        </div>

                        <button 
                            type="button" 
                            class="remove-btn"
                        >Remove</button>

                        <!-- hidden flag for deletion; default "0" -->
                        <input 
                            type="hidden" 
                            name="remove[${idx}]" 
                            value="0"
                        />
                        <input 
                            type="hidden" 
                            name="image_order[${idx}]" 
                            value="${idx}"
                        />
                    `;

                    imageOrder[file] = idx;

                    // wire up the Remove button
                    card.querySelector('.remove-btn').addEventListener('click', () => {
                        card.classList.toggle('to-be-removed');
                        const flag = card.querySelector('input[type=hidden]');
                        flag.value = flag.value === '0' ? '1' : '0';
                    });

                    previewContainer.appendChild(card);
                };
                reader.readAsDataURL(file);
            });
        });
    });
}catch(err){

}