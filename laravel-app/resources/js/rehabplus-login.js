window.togglePassword = function () {
    const input = document.getElementById('passwordInput');
    const icon = document.getElementById('eyeIcon');

    if (!input || !icon) {
        return;
    }

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
};
