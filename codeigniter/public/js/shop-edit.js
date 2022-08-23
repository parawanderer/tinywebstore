const form = document.getElementById('shopEditForm');

const bgColorSelect = document.getElementById("backgroundColor");
const textColorSelect = document.getElementById("textColor");

class RGBColor {
    constructor(hexColor) {
        this.r = parseInt(hexColor.substring(1, 3), 16);
        this.g = parseInt(hexColor.substring(3, 5), 16);
        this.b = parseInt(hexColor.substring(5, 7), 16);
    }
}

// https://stackoverflow.com/questions/9733288/how-to-programmatically-calculate-the-contrast-ratio-between-two-colors/9733420#9733420
function luminance(r, g, b) {
    var a = [r, g, b].map(function(v) {
      v /= 255;
      return v <= 0.03928 ?
        v / 12.92 :
        Math.pow((v + 0.055) / 1.055, 2.4);
    });
    return a[0] * 0.2126 + a[1] * 0.7152 + a[2] * 0.0722;
}

// https://dev.to/alvaromontoro/building-your-own-color-contrast-checker-4j7o
function validColorContrast(el1, el2) {
    const value = el1.value?.trim();
    const value2 = el2.value?.trim();

    const color1 = new RGBColor(value);
    const lum1 = luminance(color1.r, color1.g, color1.b);

    const color2 = new RGBColor(value2);
    const lum2 = luminance(color2.r, color2.g, color2.b);

    const ratio = lum1 > lum2 
        ? ((lum2 + 0.05) / (lum1 + 0.05))
        : ((lum1 + 0.05) / (lum2 + 0.05));

    return ratio < 1/4.5;
}

function handleCompareValidity(one, other) {
    if (validColorContrast(one, other)) {
        bgColorSelect.setCustomValidity('');
        textColorSelect.setCustomValidity('');
    } else {
        const ERROR = 'Contrast ratio between colors too low!';
        bgColorSelect.setCustomValidity(ERROR);
        textColorSelect.setCustomValidity(ERROR);
    }

    bgColorSelect.reportValidity();
    textColorSelect.reportValidity();
}


bgColorSelect.addEventListener('input', function (event) { 
    handleCompareValidity(event.target, textColorSelect);
});

textColorSelect.addEventListener('input', function (event) { 
    handleCompareValidity(event.target, bgColorSelect);
});


form.addEventListener('submit', function (event) {
    if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
    }

    form.classList.add('was-validated');

});

const descriptionInput = document.getElementById("description");

const htmlEditor = pell.init({
    // <HTMLElement>, required
    element: document.getElementById('pellEditor'),
    onChange: function(html) { 
        descriptionInput.value = html;
     },
    defaultParagraphSeparator: 'p',
    styleWithCSS: false,
    actions: [
      'bold',
      'underline',
      'italic',
      'strikethrough',
      'heading1',
      'heading2',
      'paragraph',
      'olist',
      'ulist',
      'code',
      'line',
      'link'
    ]
});

htmlEditor.content.innerHTML = descriptionInput.value;
