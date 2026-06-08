document
    .getElementById('register-form')
    .addEventListener('submit', async function (e) {

        e.preventDefault();

        const response = await fetch('/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                login: this.login.value,
                password: this.password.value
            })
        });

        const data = await response.json();

        if (data.success) {
            window.location.href = '/login';
        } else {
            document.getElementById('message').innerText =
                data.message;
        }
    });