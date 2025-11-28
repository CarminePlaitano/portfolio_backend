import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["email", "emailError", "submit"];

    connect() {
        this.checkEmail = this.debounce(this.checkEmail.bind(this), 500);
    }

    debounce(func, delay) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    }

    checkEmail() {
        const email = this.emailTarget.value;
        if (email.length < 3 || !email.includes('@')) {
            this.emailErrorTarget.style.display = 'none';
            this.emailTarget.classList.remove('is-invalid');
            this.submitTarget.disabled = false;
            return;
        }

        fetch('/api/admin/check-email', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ email: email })
        })
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    this.emailErrorTarget.textContent = 'This email is already registered.';
                    this.emailErrorTarget.style.display = 'block';
                    this.emailTarget.classList.add('is-invalid');
                    this.submitTarget.disabled = true;
                } else {
                    this.emailErrorTarget.style.display = 'none';
                    this.emailTarget.classList.remove('is-invalid');
                    this.submitTarget.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.emailErrorTarget.style.display = 'none';
                this.emailTarget.classList.remove('is-invalid');
                this.submitTarget.disabled = false;
            });
    }
}
