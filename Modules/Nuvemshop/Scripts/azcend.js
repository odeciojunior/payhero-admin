document.querySelectorAll('form[data-store="cart-form"]').forEach((form) => {
    form.addEventListener("submit", (event) => {
        event.preventDefault();

        const checkoutForm = document.createElement("form");
        checkoutForm.action = "https://checkout.azcend.com.br";
        checkoutForm.method = "POST";
        checkoutForm.style.display = "none";

        LS.cart.items.forEach((item, index) => {
            const productIdInput = document.createElement("input");
            productIdInput.type = "hidden";
            productIdInput.name = `product_id_${index + 1}`;
            productIdInput.value = item.item_id;
            checkoutForm.appendChild(productIdInput);

            const variantIdInput = document.createElement("input");
            variantIdInput.type = "hidden";
            variantIdInput.name = `variant_id_${index + 1}`;
            variantIdInput.value = item.variant_id;
            checkoutForm.appendChild(variantIdInput);

            const productAmountInput = document.createElement("input");
            productAmountInput.type = "hidden";
            productAmountInput.name = `product_amount_${index + 1}`;
            productAmountInput.value = item.quantity;
            checkoutForm.appendChild(productAmountInput);
        });

        document.body.appendChild(checkoutForm);
        checkoutForm.submit();
    });
});
