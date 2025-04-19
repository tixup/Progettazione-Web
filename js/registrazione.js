document.addEventListener('DOMContentLoaded', () => {

    const inputs = ['username','email','password','conferma_password'];

    // assegna la classe .error agli input che hanno $err (e il bordo rosso).
    inputs.forEach(field => {
      const inp = document.getElementById(field);
      const errEl = document.getElementById(`${field}-error`);
      if (errEl && errEl.textContent.trim() !== '') {
        inp.classList.add('error');
      }
      // All’input di ogni campo rimuove .error e pulisce il testo del <small>.
      inp.addEventListener('input', () => {
        inp.classList.remove('error');
        errEl.textContent = '';
      });
    });
  });
  