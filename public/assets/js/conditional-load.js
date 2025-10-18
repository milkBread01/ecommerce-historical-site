try{
    
    const documentationRadios = document.querySelectorAll('input[type=radio][name=documentation]');

    const authenticationRadios = document.querySelectorAll('input[type=radio][name=certificate_auth]');

    const saleRadios = document.querySelectorAll('input[type=radio][name=on_sale]');

    const percentDiscount = document.querySelector('input[type=number][name=percentage_discount]');

    const amountDiscount = document.querySelector('input[type=number][name=amount_discount]');
    
    const assignDocVisibility = document.getElementById('documentation-inputs');
    const assignAuthVisibility = document.getElementById('certificate-inputs');
    const assignSaleInfoVisibility = document.getElementById('on-sale-info');

    percentDiscount.addEventListener("input", function() {
        if (this.value !== '') {
            amountDiscount.value = '';
        }
    });

    amountDiscount.addEventListener("input", function() {
        if (this.value !== '') {
            percentDiscount.value = '';
        }
    });

    documentationRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if(this.value === '1'){
                console.log('yes doc');
                assignDocVisibility.style.display='block';
            }else if(this.value === '0'){
                console.log('no doc');
                assignDocVisibility.style.display='none';
            }
        })
    })

    authenticationRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if(this.value === '1'){
                console.log('yes Auth');
                assignAuthVisibility.style.display='block';
            }else if(this.value === '0'){
                console.log('no Auth');
                assignAuthVisibility.style.display='none';
            }
        })
    })

    saleRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if(this.value === '1'){
                console.log('yes sale');
                assignSaleInfoVisibility.style.display='block';
            }else if(this.value === '0'){
                console.log('no sale');
                assignSaleInfoVisibility.style.display='none';
            }
        })
    })

    


}catch(err) {

}