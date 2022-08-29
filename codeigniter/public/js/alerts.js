const POLL_EVERY = 3000;
const alertContainer = document.getElementById("alertContainer");
const alertTemplate = document.getElementById("alertTemplate");
var fetching = false;

const TYPE_WATCHLIST_AVAILABLE = 0;
const TYPE_PRODUCT_ORDER_COMPLETED = 1;

function createBoldElement(textContent) {
    const el = document.createElement("b");
    el.textContent = textContent;
    return el;
}

function removeChildren(element) {
    while (element.firstChild) {
        element.removeChild(element.firstChild);
    }
}

const SECOND = 1;
const MINUTE = 60;
const HOUR = 3600;
const DAY = 86_400;

function getTimeAgoString(time, from) {
    const diff = (from - time) / 1000;

    if (diff < SECOND) {
        return "just now";
    }
    else if (diff < MINUTE) {
        if (diff < (SECOND * 2)) {
            return "1 second ago";
        }
        
        const n = Math.floor(diff / SECOND);
        return n + " seconds ago";
    } else if (diff < HOUR) {
        if (diff < (MINUTE * 2)) {
            return "1 minute ago";
        }

        const n = Math.floor(diff / MINUTE);
        return n + " minutes ago";
    } else if (diff < DAY) {
        if (diff < (HOUR * 2)) {
            return "1 hour ago";
        }

        const n = Math.floor(diff / HOUR);
        return n + " hours ago";
    } else {
        if (diff < (DAY * 2)) {
            return "1 day ago";
        }

        const n = Math.floor(diff / DAY);
        return n + " days ago";
    }
}

function handle() {
    // polling alerts
    const template = alertTemplate.firstElementChild;
    const tracking = new Map();
    const seenAlerts = new Set();

    const alertInterval = setInterval(function(event) {
        if (fetching) return;
        fetching = true;

        const options = {
            method: 'GET',
            mode: 'same-origin',
            headers: {
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest"
            }
        };

        fetch('/alerts', options)
        .then(function(data) { 
            return data.json(); // expecting json...
        })
        .then(function(json) {
            fetching = false;
            const now = new Date().getTime();
            
            for (let i = 0; i < json.length; ++i) {
                const alert = json[i];

                if (seenAlerts.has(alert.id)) continue;

                /** @type {HTMLDivElement} */
                const newElement = template.cloneNode(true);
                linkElement(alert, newElement, now);
                tracking.set(alert.id, newElement);
                seenAlerts.add(alert.id);
                
                newElement.classList.remove("hide");
                alertContainer.appendChild(newElement);

                setTimeout(function() {
                    newElement.classList.add("show");
                }, 100);
            }
        });

    }, POLL_EVERY);

    const timestampUpdateInterval = setInterval(function(event) {
        const now = new Date().getTime();

        tracking.forEach(function (element, key) {
            const ts = parseInt(element.dataset.timeStamp);
            const timeEl = element.getElementsByClassName("notification-time")[0];
            timeEl.textContent = getTimeAgoString(ts, now);
        });

    }, 500);

    function closeAlert(event) {
        event.preventDefault();
        event.stopPropagation();

        const alertId = this.dataset.id;
        const element = tracking.get(alertId);
        element.classList.remove("show");
        tracking.delete(alertId);

        markSeen(alertId);

        setTimeout(function() {
            element.classList.add("hide");
            setTimeout(function() {
                element.parentElement.removeChild(element);
            }, 100);
        }, 100);
    }

    function markSeen(alertId, callbackAfterDispatch = null) {
        const options = {
            method: 'PUT',
            mode: 'same-origin',
            headers: {
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest"
            },
            body: JSON.stringify({
                "id": alertId
            })
        };

        fetch('/alerts/seen', options)
        .then(function(data) { 
            console.log("Mark alert seen status", { "alert" : alertId, "status": data.status });
        })
        .catch(function (err) { console.error(err) });

        if (callbackAfterDispatch) callbackAfterDispatch();
    }

    function clickHandler(event) {
        const { redirectTo, id } = this.dataset;

        if ((window.location.pathname + window.location.hash) === redirectTo) {
            closeAlert(event);
            return;
        }

        markSeen(id, function() {
            window.location.href = redirectTo;
        });
    }

    function linkElement(alert, /** @type {HTMLDivElement} */ parentElement, now) {
        // fill out template
        const titleEl = parentElement.getElementsByClassName("notification-title")[0];
        const timeEl = parentElement.getElementsByClassName("notification-time")[0];
        const bodyEl = parentElement.getElementsByClassName("notification-body")[0];
        const closeButton = parentElement.getElementsByClassName("notification-close")[0];
        
        // id: "1"
        // seen: "0"
        // subject_id: "1"
        // subject_name: "Hoofdkussen 'Slaap Lekker'"
        // timestamp: 1661342268
        // type: "0"
        // user_id: "1"
    
        const {
            id, type, subject_id, subject_name, timestamp
        } = alert;

        const typeInt = parseInt(type);
        removeChildren(bodyEl);
        parentElement.dataset.id = id;

        const occurrenceTime = (timestamp * 1000);
        parentElement.dataset.timeStamp = occurrenceTime;
    
        switch(typeInt) {
            case TYPE_WATCHLIST_AVAILABLE:
                titleEl.textContent = "Watchlist Alert";
                parentElement.dataset.redirectTo = "/product/" + subject_id;

                //body
                bodyEl.appendChild(document.createTextNode("Product "))
                bodyEl.appendChild(createBoldElement(subject_name));
                bodyEl.appendChild(document.createTextNode(" has become available again!"));
    
                break;
            case TYPE_PRODUCT_ORDER_COMPLETED:
                titleEl.textContent = "Order Completed";
                parentElement.dataset.redirectTo = "/product/" + subject_id + "#reviews";

                //body
                bodyEl.appendChild(document.createTextNode("Your order for "))
                bodyEl.appendChild(createBoldElement(subject_name));
                bodyEl.appendChild(document.createTextNode(" has been completed. You may now leave a review!"));
    
                break;
            default:
                throw new Error("Alert type not found");
        }

        closeButton.dataset.id = id;
        timeEl.textContent = getTimeAgoString(occurrenceTime, now);

        parentElement.addEventListener("click", clickHandler);
        closeButton.addEventListener("click", closeAlert);
    }
}

if (window.AppIsLoggedIn) {
    handle();
}