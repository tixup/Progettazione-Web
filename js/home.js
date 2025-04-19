document.addEventListener('DOMContentLoaded', () => {
    const filterForm = document.getElementById('filtri-form');
    const dataInput = document.getElementById('data');
    const saleGrid = document.querySelector('.saleGrid');
    
    // Imposta la data minima a oggi
    const today = new Date().toISOString().split('T')[0];
    dataInput.min = today;
    dataInput.value = today;

    // Mostra tutte le sale al caricamento iniziale, senza pulsante prenota
    function showAllRooms() {
        // endpoint per tutte le sale
        fetch('../php/get_all_rooms.php')  
            .then(response => response.json())
            .then(rooms => {
                // Passa false per non mostrare i bottoni 'prenota'
                updateRoomList(rooms, false); 
            })
            .catch(error => {
                console.error("Errore:", error);
                saleGrid.innerHTML = `<div class="error">Errore nel caricamento delle sale</div>`;
            });
    }

    // Funzione principale per filtrare le sale
    filterForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        await applyFilters();
    });

    // Funzione riutilizzabile per applicare i filtri
    async function applyFilters() {
        const orario = document.getElementById('orario').value;
        
        // Se l'orario non è selezionato, mostra tutte le sale
        if (!orario) {
            showAllRooms();
            return;
        }

        // Validazione data
        if (!dataInput.value) {
            alert('Seleziona una data');
            return;
        }
        
        const selectedDate = new Date(dataInput.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (selectedDate < today) {
            alert('Seleziona una data odierna o futura');
            return;
        }

        try {
            // endpoint filtraggio sale
            const response = await fetch('../php/filtra_sale.php', { 
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    data: dataInput.value,
                    orario: orario,
                    attrezzature: document.getElementById('attrezzature').value,
                    disponibili: document.getElementById('disponibili').checked
                })
            });
            
            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.errore || 'Errore nella risposta del server');
            }
            
            const filteredRooms = await response.json();
            // Passa true per mostrare i bottoni 'prenota'
            updateRoomList(filteredRooms, true); 
            
        } catch (error) {
            console.error("Errore:", error);
            showErrorPopup(error.message, 5000);
        }
    }

        // Funzione per gestire la prenotazione
        async function handlePrenotazione(salaId, salaNome, data, orario) {
        const btn = document.querySelector(`.prenota-btn[data-id="${salaId}"]`);
            
        try {
            // Disabilita il bottone durante la richiesta
            if (btn) {
                btn.disabled = true;
                btn.textContent = 'Prenotazione in corso...';
            }
            
            // richiesta endpoint prenotazione
            const response = await fetch('../php/prenota.php', { 
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    sala_id: salaId, 
                    data, 
                    orario 
                })
            });
            
            // Gestione della risposta
            const result = await response.json();
            
            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Errore nella prenotazione');
            }

            // Mostra il popup di successo per 3 secondi
            showSuccessPopup(
                `Prenotazione confermata per ${salaNome}<br>Data: ${data}<br>Orario: ${orario}`,
                3000
            );
            
            // Riapplica i filtri dopo la prenotazione
            setTimeout(() => applyFilters(), 3000);
            
        } catch (error) {
            console.error("Errore:", error);
            showErrorPopup(error.message, 5000);
            
            // Riabilita il bottone in caso di errore
            if (btn) {
                btn.disabled = false;
                btn.textContent = 'Prenota';
            }
        }
    }

    // Funzioni per i popup (modificate per accettare durata personalizzata)
    function showSuccessPopup(message, duration = 3000) {
        const popup = document.createElement('div');
        popup.className = 'success-popup';
        popup.innerHTML = message;
        document.body.appendChild(popup);
        
        setTimeout(() => {
            if (popup.parentNode) {
                popup.remove();
            }
        }, duration);
        
        return popup;
    }

    function showErrorPopup(message, duration = 5000) {
        const popup = document.createElement('div');
        popup.className = 'error-popup';
        popup.innerHTML = message;
        document.body.appendChild(popup);
        
        setTimeout(() => {
            if (popup.parentNode) {
                popup.remove();
            }
        }, duration);
    }

    // Funzione per aggiornare la lista delle sale (modificata)
    function updateRoomList(rooms, showButtons = false) {
        saleGrid.innerHTML = '';
        
        if (rooms.length === 0) {
            saleGrid.innerHTML = '<div class="no-rooms">Nessuna sala trovata</div>';
            return;
        }
        
        rooms.forEach(room => {
            const roomCard = document.createElement('div');
            roomCard.className = 'sala-card';
            
            const isFull = room.posti_disponibili <= 0;
            if (isFull) {
                roomCard.classList.add('sala-piena');
            }
            
            roomCard.innerHTML = `
                <div>
                    <img src="../img/sale/${room.immagine}" alt="${room.nome}" class="sala-img">
                    <h3>${room.nome}</h3>
                    <p>Posti totali: ${room.posti_totali}</p>
                    ${showButtons ? `<p>Posti disponibili: ${room.posti_disponibili || room.posti_totali}</p>` : ''}
                    <p>Attrezzature: ${room.attrezzature}</p>
                    ${showButtons && !isFull ? 
                        `<button class="btn prenota-btn" 
                            data-id="${room.id}" 
                            data-nome="${room.nome}"
                            data-data="${dataInput.value}"
                            data-orario="${document.getElementById('orario').value}">
                            Prenota
                        </button>` : 
                        showButtons ? '<p class="full-message">Sala piena</p>' : ''}
                </div>
            `;
            saleGrid.appendChild(roomCard);
        });
        
        // Aggiungi event listener ai bottoni
        if (showButtons) {
            document.querySelectorAll('.prenota-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    handlePrenotazione(
                        btn.dataset.id,
                        btn.dataset.nome,
                        btn.dataset.data,
                        btn.dataset.orario
                    );
                });
            });
        }
    }

    // Mostra tutte le sale al caricamento iniziale
    showAllRooms();
});