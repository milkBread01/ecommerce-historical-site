try{
    const form   = document.querySelector('.npf-form');
    const target = document.getElementById('product-specific-fields');
    console.log('============ NPF js ============')

    async function renderProductSpecific() {
        const fd = new FormData(form);

        if (!fd.has(window.APP.csrf.name)) {
            fd.append(window.APP.csrf.name, window.APP.csrf.value);
        }

        const res = await fetch(window.APP.routes.productFields, {
            method: 'POST',
            body: fd,
            credentials: 'same-origin',
            headers: { 
                'X-Requested-With': 'XMLHttpRequest', 
                'Accept': 'text/html' 
            }
        });

        // if CSRF regenerates, refresh it for future calls
        const newToken = res.headers.get('X-CSRF-TOKEN');
        if (newToken) window.APP.csrf.value = newToken;

        const html = await res.text();
        target.innerHTML = html;
    }

    // hook up listeners
    document.getElementById('category_id')?.addEventListener('change', renderProductSpecific);

    // initial render if category already selected
    if (document.getElementById('category_id')?.value) renderProductSpecific();


}catch(err){

}