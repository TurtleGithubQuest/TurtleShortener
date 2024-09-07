document.querySelectorAll("form[response-type]").forEach((form) => {
    const response_type = form.attributes.getNamedItem('response-type').value;
    if (response_type === null)
        return;
    form.target = "none";
    console.log("Response type: ",response_type);
    let fn;

    switch(response_type) {
        case "console":
            fn = responseConsole;
            break;
        default:
            fn = null;
            break;
    }
    if (fn) {
        form.addEventListener('submit', fn);
    }

});

function responseConsole(e) {
    e.preventDefault(); // Prevent default form submission

    const form = e.target;
    const formData = new FormData(form);
    const action = e.submitter.getAttribute('formaction');

    fetch(action, {
        method: form.method,
        body: formData,
    })
    .then(response => response.text())
    .then(data => {
        data.split('<br>').forEach(line => console.log(line));
    })
    .catch(error => {
        console.error('Error:', error);
    });
}