function removeAnimal(animal_id) {
    var formData = new FormData();
    formData.append('action', 'remove_animal');
    formData.append('animal_id', animal_id);

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('animal-card-' + animal_id).remove(); // Kártya eltávolítása DOM-ból
            showToast('success', data.message); // Sikeres üzenet megjelenítése
        } else {
            showToast('error', 'Hiba történt a törlés során.'); // Hibás üzenet megjelenítése
        }
    })
    .catch(error => {
        console.error('Hiba történt:', error);
        showToast('error', 'Hiba történt a törlés során.'); // Hiba esetén üzenet megjelenítése
    });
}


function submitAdoptionRequest(animal_id) {
    var formData = new FormData();
    formData.append('action', 'submit_adoption_request');
    formData.append('animal_id', animal_id);

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message); // Sikeres üzenet megjelenítése
        } else {
            showToast('error', data.message); // Hibaüzenet megjelenítése
        }
    })
    .catch(error => {
        console.error('Hiba történt:', error);
        showToast('error', 'Hiba történt az örökbefogadási kérelem során.'); // Hiba esetén üzenet megjelenítése
    });
}


function showToast(type, message) {
    var toastContainer = document.querySelector('.toast-container');
    var toast = document.createElement('div');
    toast.className = 'toast align-items-center text-white bg-' + (type === 'success' ? 'success' : 'danger') + ' show';
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;
    toastContainer.appendChild(toast);

    setTimeout(function() {
        var bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        setTimeout(function() {
            toast.remove();
        }, 3000);
    }, 100);
}