try {
    console.log('----------- Conditional Load -----------')
    const certSelect = document.getElementById('certificate_auth');
    const certificateInputs = document.getElementById('certificate-inputs');

    const documentationSelect = document.getElementById('documentation');
    const documentationInputs = document.getElementById('documentation-inputs');

    const saleSelect = document.getElementById('on_sale');
    const saleInputs = document.getElementById('on-sale-info');
    
    // Function to toggle certificate type visibility
    function toggleCertificateInputs() {
        const output = certSelect.options[certSelect.selectedIndex].value;
        console.log(`Selected Certificate State: ${output}`);
        
        if (output === '1') {
            certificateInputs.style.display = 'block';
        } else {
            certificateInputs.style.display = 'none';
        }
    }
    
    // Run on page load to set initial state
    toggleCertificateInputs();
    
    // Add event listener for changes
    certSelect.addEventListener('change', toggleCertificateInputs);

    // ---------------------------------------------------------

    function toggleDocumentationInputs() {
        const output = documentationSelect.options[documentationSelect.selectedIndex].value;
        console.log(`Selected Documentation State: ${output}`);
        
        if (output === '1') {
            documentationInputs.style.display = 'block';
        } else {
            documentationInputs.style.display = 'none';
        }
    }
    
    // Run on page load to set initial state
    toggleDocumentationInputs();
    
    // Add event listener for changes
    documentationSelect.addEventListener('change', toggleDocumentationInputs);

    // ---------------------------------------------------------

    function toggleSaleInputs() {
        const output = saleSelect.options[saleSelect.selectedIndex].value;
        console.log(`Selected Sale State: ${output}`);
        
        if (output === '1') {
            saleInputs.style.display = 'block';
        } else {
            saleInputs.style.display = 'none';
        }
    }
    
    // Run on page load to set initial state
    toggleSaleInputs();
    
    // Add event listener for changes
    saleSelect.addEventListener('change', toggleSaleInputs);

    // ---------------------------------------------------------


    const percentDiscount = document.querySelector('input[type=number][name="percentage_discount"]');
    const amountDiscount  = document.querySelector('input[type=number][name="amount_discount"]');

    // Mutually exclusive discount inputs (unchanged)
    if (percentDiscount && amountDiscount) {
        percentDiscount.addEventListener('input', function () {
            if (this.value !== '') amountDiscount.value = '';
        });

        amountDiscount.addEventListener('input', function () {
            if (this.value !== '') percentDiscount.value = '';
        });
    }
    
} catch(err) {
    console.error('Error initializing certificate toggle:', err);
}