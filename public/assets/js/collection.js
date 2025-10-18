try{    
    document.addEventListener('DOMContentLoaded', function() {
        const entryTypeRadios = document.querySelectorAll('.entry-type-radio');
        const assignSection = document.getElementById('assign-to-collection-section');
        const createSection = document.getElementById('create-collection-section');
        const bundlePriceSection = document.getElementById('bundle-price-section');
        const bundleOptions = document.querySelectorAll('.bundle-option');

        // Handle entry type change
        entryTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'individual_item') {
                    assignSection.style.display = 'block';
                    createSection.style.display = 'none';
                    // Clear collection creation fields
                    document.getElementById('collection-name').value = '';
                    document.getElementById('collection-description').value = '';
                } else if (this.value === 'create_collection') {
                    assignSection.style.display = 'none';
                    createSection.style.display = 'block';
                    // Clear collection assignment
                    document.getElementById('collection-select').value = '';
                }
            });
        });

        // Handle bundle pricing visibility
        bundleOptions.forEach(option => {
            option.addEventListener('change', function() {
                if (this.value === '1') {
                    bundlePriceSection.style.display = 'block';
                } else {
                    bundlePriceSection.style.display = 'none';
                    document.getElementById('collection-price').value = '';
                }
            });
        });

        // Initialize on page load
        const checkedEntry = document.querySelector('.entry-type-radio:checked');
        if (checkedEntry && checkedEntry.value === 'individual_item') {
            assignSection.style.display = 'block';
        }

        const checkedBundle = document.querySelector('.bundle-option:checked');
        if (checkedBundle && checkedBundle.value === '1') {
            bundlePriceSection.style.display = 'block';
        }
    });

}catch(err){
    
}