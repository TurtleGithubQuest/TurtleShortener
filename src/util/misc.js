export const copyValue = function(el, value) {
    const copy = value ? value :
        (el.getAttribute("copyValue") ? el.getAttribute("copyValue") : el.value);
    navigator.clipboard.writeText(copy).then(async function() {
        el.classList.add('success');
        setTimeout(function() {
            el.classList.remove('success');
        }, 500);
    })
};
export const createEl = (tag, classNames) => {
    const element = document.createElement(tag);
    if (classNames) {
        element.classList.add(...classNames.split(' '));
    }
    return element;
};