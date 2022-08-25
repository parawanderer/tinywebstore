const searchBarInput = document.getElementById("searchBarInput");
const searchResultsDropDown = document.getElementById("searchResultsDropDown");
const searchContainer = document.getElementById("searchContainer");
const searchOverlay = document.getElementById("searchOverlay");


searchBarInput.addEventListener("focus", function(event) {
    searchOverlay.classList.add("show");
});


searchBarInput.addEventListener("keyup", function(event) {
    if (event.key === "Escape") {
        searchOverlay.classList.remove("show");
        searchResultsDropDown.classList.remove("show");
    }
});

searchBarInput.addEventListener("click", function(event) {
    searchOverlay.classList.add("show");
});

searchOverlay.addEventListener("mousedown", function(event) {
    searchOverlay.classList.remove("show");
    searchResultsDropDown.classList.remove("show");
});


var lastSearch = null;
var typingTimeout = null;
var isSearching = false;

function removeChildren(element) {
    while (element.firstChild) {
        element.removeChild(element.firstChild);
    }
}

function insertChildren(element, childList) {
    for (let i = childList.length - 1; i >= 0; --i) {
        element.prepend(childList[i]);
    }
}

function handleSearch(searchTerm) {
    if (isSearching || lastSearch === searchTerm) return;
    lastSearch = searchTerm;
    isSearching = true;

    const targetUrl = new URL(window.location.origin);
    targetUrl.pathname = "/quicksearch";
    targetUrl.searchParams.set("q", searchTerm);

    const options = {
        method: 'GET',
        mode: 'same-origin'
    };

    fetch(targetUrl.toString(), options)
    .then(function(data) { 
        return data.text(); // expecting html...
    })
    .then(function(html) {
        const parser = new DOMParser();
        const virtualDoc = parser.parseFromString(html, 'text/html')
        const resultContainer = virtualDoc.getElementById("resultContainer");
        const elements = resultContainer.children;

        removeChildren(searchResultsDropDown);
        insertChildren(searchResultsDropDown, elements);

        searchResultsDropDown.classList.add("show");
        isSearching = false;
    });
}

function doSearch(searchTerm) {
    // search if possible
    clearTimeout(typingTimeout);
    typingTimeout = setTimeout(function() { handleSearch(searchTerm); }, 200);
};

searchBarInput.addEventListener("input", function(event) {
    searchOverlay.classList.add("show");

    const val = searchBarInput.value?.trim();

    if (val) {
        doSearch(val);
    }
});
