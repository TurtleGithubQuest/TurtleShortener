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
console.log()