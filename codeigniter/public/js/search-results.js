const extendedSearchForm = document.getElementById("extendedSearchForm");

function disableEmptyFormValues(form) {
    const inputElements = form.getElementsByTagName("input");
    for (let i = 0; i < inputElements.length; ++i) {
        const el = inputElements[i];
        const val = el.value?.trim();
        if (!val) {
            el.disabled = true; // do not include this in sent form
        }
    }
}

//dont send empty values
extendedSearchForm.addEventListener("submit", function(event) {
    disableEmptyFormValues(extendedSearchForm);
});

// un-disable fields when page loads
const inputElements = extendedSearchForm.getElementsByTagName("input");

for (let i = 0; i < inputElements.length; ++i) {
    const el = inputElements[i];
    el.disabled = false;
}

const sortOrderSelect = document.getElementById("sortOrderSelect");
sortOrderSelect.addEventListener("change", function(event) {
    disableEmptyFormValues(extendedSearchForm);
    extendedSearchForm.submit(); // prompt re-render without clicking "filter" for this
});


// prevent max price being below min price
const minPriceInput = document.getElementById("minPriceInput");
const maxPriceInput = document.getElementById("maxPriceInput");

minPriceInput.addEventListener("input", function(event) {
    const val = minPriceInput.value?.trim();
    const otherVal = maxPriceInput.value?.trim();
    if (val && otherVal && parseInt(val) >= parseInt(otherVal)) {
        minPriceInput.value = parseInt(otherVal) - 1;
    }   
});

maxPriceInput.addEventListener("input", function(event) {
    const val = minPriceInput.value?.trim();
    const otherVal = maxPriceInput.value?.trim();

    if (val && otherVal && parseInt(val) >= parseInt(otherVal)) {
        maxPriceInput.value = parseInt(val) + 1;
    }   
});