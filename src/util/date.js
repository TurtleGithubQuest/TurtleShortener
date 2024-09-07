getTimezone = function() {
    return Intl.DateTimeFormat().resolvedOptions().timeZone;
}
Number.prototype.formatUnixToDate = function(userFriendly) {
    const date = new Date(this);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hour = String(date.getHours()).padStart(2, '0');
    const minute = String(date.getMinutes()).padStart(2, '0');

    return userFriendly ?
        `${day}/${month}/${year} ${hour}:${minute}`:
        `${year}-${month}-${day}T${hour}:${minute}`;
};
updateInputElementDate = function(el, unix= null) {
    if (el === null) return;
    const unixTime = unix ? Number.parseInt(unix) : Date.now();
    el.value = unixTime.formatUnixToDate(false)
}
updateElementTextDate = function(el, unix= null) {
    if (el === null) return;
    const unixTime = unix ? Number.parseInt(unix) : Date.now();
    el.innerHTML = unixTime.formatUnixToDate(true)
}