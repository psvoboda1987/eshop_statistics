window.onload = () => {

    initControls();

}

function initControls() {

    let controls = document.getElementsByClassName('control');
    if (controls.length === 0) return;

    for (let control of controls) {

        control.addEventListener('click', (e) => {

            sendRequest(control);

        })

    }

}

async function sendRequest(control) {

    let url = 'content/ajax/get_data.php';

    console.log(`${url}?method=${control.dataset.method}`);

    fetch(`${url}?method=${control.dataset.method}`)
        .then(response => {

            if (response.status != 200) console.log('response not OK');

            return response.json();
        })
        .then(result => {

            showResult(result, control);
        })
        .catch(err => console.log(err));

}

function showResult(result, control) {

    let resultElement = document.getElementById('result');
    if (!resultElement) return;

    if (typeof result == 'string' && result.includes('<br>')) {

        result = '<br>' + result;

    } else if (typeof result == 'number' && result % 1 != 0) {

        result = parseFloat(result).toFixed(2);
    }

    resultElement.innerHTML = `<span class="bold">${control.innerText}</span>: ${result}`;

}