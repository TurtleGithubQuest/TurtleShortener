export const copyValue = function(el, value=null) {
    const copy = value ? value :
        (el.getAttribute("copyValue") ? el.getAttribute("copyValue") : el.value);
    navigator.clipboard.writeText(copy).then(async function() {
        el.classList.add('success');
        setTimeout(function() {
            el.classList.remove('success');
        }, 500);
    })
};
export function createEl(tag, classNames) {
    const element = document.createElement(tag);
    if (classNames) {
        element.classList.add(...classNames.split(' '));
    }
    return element;
}
export function createAlert(level, text) {
    const label = createEl('label', 'alert');

    const input = createEl('input');
    input.type = 'checkbox';
    input.setAttribute('autocomplete', 'off');

    const notificationInfo = createEl('span', 'notification-info');
    notificationInfo.textContent = ' >';

    const content = createEl('span', `content ${level}`);
    content.innerHTML = text;

    label.appendChild(input);
    label.appendChild(notificationInfo);
    label.appendChild(content);

    return label;
}
export function createAlertAndAssign(level, text) {
    const alerts = document.getElementById('alerts');
    if (alerts)
        alerts.appendChild(createAlert(level, text));
}
export function deepMerge(obj1, obj2, arrayReplace = true, mergeObjectsInArray = false) {
    const output = { ...obj1 };
    for (const key in obj2) {
        const val = obj2[key];
        if (typeof val === 'object' && val !== null) {
            if (Array.isArray(val)) {
                if (arrayReplace || !Array.isArray(output[key])) {
                    output[key] = obj2[key];
                } else {
                    output[key] = output[key].map((item, index) => {
                        if (mergeObjectsInArray && typeof item === 'object' && typeof val[index] === 'object') {
                            return deepMerge(item, val[index], arrayReplace, mergeObjectsInArray);
                        }
                        return val[index] !== undefined ? val[index] : item;
                    });
                }
            } else {
                output[key] = deepMerge(obj1[key] || {}, obj2[key], arrayReplace, mergeObjectsInArray);
            }
        } else if (typeof val === 'function') {
            output[key] = val;
        } else {
            output[key] = obj2[key];
        }
    }
    return output;
}
export function deepClone(obj) {
    if (obj === null || typeof obj !== 'object') {
        return obj;
    }

    if (Array.isArray(obj)) {
        return obj.map(deepClone);
    }

    const clonedObj = {};
    for (const key in obj) {
        if (obj.hasOwnProperty(key)) {
            if (typeof obj[key] === 'function') {
                clonedObj[key] = obj[key];
            } else {
                clonedObj[key] = deepClone(obj[key]);
            }
        }
    }
    return clonedObj;
}
console.log()