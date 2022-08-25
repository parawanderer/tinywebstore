// choose checkout type
const checkoutAccordion = document.getElementById('checkoutAccordion');

const buttonDeliver = document.getElementById("buttonDeliveryOptDeliver");
const buttonPickup = document.getElementById("buttonDeliveryOptPickup");

const checkoutOptions = document.getElementsByClassName("accordion-button");
var currentItem = buttonDeliver;

const collapse =  {
    'deliveryOptionsPanelDeliver': new bootstrap.Collapse(document.getElementById('deliveryOptionsPanelDeliver'), { toggle: false }),
    'deliveryOptionsPanelPickup': new bootstrap.Collapse(document.getElementById('deliveryOptionsPanelPickup'), { toggle: false }),
};

for(var i = 0; i < checkoutOptions.length; ++i) {
    const opt = checkoutOptions[i];

    opt.addEventListener("click", function(event) {
        if (currentItem === opt) return;

        currentItem.classList.add("collapsed");
        opt.classList.remove("collapsed");

        collapse[opt.dataset.opens].show();
        collapse[currentItem.dataset.opens].hide();

        currentItem = opt;
    });
}

const form1 = document.getElementById('checkoutFormDelivery');
const form2 = document.getElementById('checkoutFormPickup');
// validation (delivery option)
form1.addEventListener('submit', function (event) {

    if (!form1.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
    }

    form1.classList.add('was-validated');
});