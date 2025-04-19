
document.addEventListener('DOMContentLoaded', () => {
    // Nascondi il messaggio di successo dopo 5 secondi
    const successMsg = document.querySelector('.success-message');
    if (successMsg) {
        setTimeout(() => {
            successMsg.style.display = 'none';
        }, 5000);
    };

    // Nascondi il messaggio di errore dopo 5 secondi
    const errorMsg = document.querySelector('.error-login');
    if (errorMsg) {
        setTimeout(() => {
            errorMsg.style.display = 'none';
        }, 5000);
    };
});