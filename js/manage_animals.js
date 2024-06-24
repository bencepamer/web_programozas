  // Funkció az állat szerkesztésére
  function editPet(animal_id) {
    // Adatok összegyűjtése a szerkesztéshez
    var animal_name = document.getElementById('animal-name-' + animal_id).innerText.trim();
    var animal_description = document.getElementById('animal-description-' + animal_id).innerText.trim();
    var animal_age = document.getElementById('animal-age-' + animal_id).innerText.trim();
    var animal_gender = document.getElementById('animal-gender-' + animal_id).innerText.trim();
  
    // Szerkesztő űrlap HTML létrehozása
    var editFormHTML = `
      <form id="editForm-${animal_id}" onsubmit="submitEditForm(event, ${animal_id})">
        <div class="mb-3">
          <label for="edit-name-${animal_id}" class="form-label">Név</label>
          <input type="text" class="form-control" id="edit-name-${animal_id}" value="${animal_name}" required>
        </div>
        <div class="mb-3">
          <label for="edit-description-${animal_id}" class="form-label">Leírás</label>
          <textarea class="form-control" id="edit-description-${animal_id}" rows="3" required>${animal_description}</textarea>
        </div>
        <div class="mb-3">
          <label for="edit-age-${animal_id}" class="form-label">Életkor</label>
          <input type="number" class="form-control" id="edit-age-${animal_id}" value="${animal_age}" required>
        </div>
        <div class="mb-3">
          <label for="edit-gender-${animal_id}" class="form-label">Nem</label>
          <select class="form-select" id="edit-gender-${animal_id}" required>
            <option value="male" ${animal_gender === 'hím' ? 'selected' : ''}>Hím</option>
            <option value="female" ${animal_gender === 'nőstény' ? 'selected' : ''}>Nőstény</option>
            <option value="unknown" ${animal_gender === 'ismeretlen' ? 'selected' : ''}>Ismeretlen</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary me-2">Mentés</button>
        <button type="button" class="btn btn-secondary" onclick="cancelEdit(${animal_id})">Mégse</button>
      </form>
    `;
  
    // A kártya tartalmának frissítése szerkesztő űrlappal
    var cardBody = document.getElementById(`animal-card-body-${animal_id}`);
    cardBody.innerHTML = editFormHTML;
  }
  
  // Funkció az állat szerkesztésének megszakítására
  function cancelEdit(animal_id) {
    // Az oldal újratöltése a szerkesztés megszakításához (opcionálisan visszaállítás is implementálható)
    window.location.reload();
  }
  
  // Űrlap elküldésének kezelése
  function submitEditForm(event, animal_id) {
    event.preventDefault();
  
    var formData = {
      action: 'edit_pet',
      animal_id: animal_id,
      animal_data: [
        $('#edit-name-' + animal_id).val().trim(),
        $('#edit-description-' + animal_id).val().trim(),
        $('#edit-age-' + animal_id).val().trim(),
        $('#edit-gender-' + animal_id).val().trim()
      ].join(',')
    };
  
    // AJAX kérés elküldése
    $.ajax({
      type: 'POST',
      url: window.location.href,
      data: formData,
      dataType: 'json',
      encode: true,
      success: function (data) {
        if (data.success) {
          // Sikeres válasz esetén frissítés a felhasználói felületen
          showToast('success', data.message);
  
          // Állat adatainak frissítése
          $('#animal-name-' + animal_id).text($('#edit-name-' + animal_id).val().trim());
          $('#animal-description-' + animal_id).text($('#edit-description-' + animal_id).val().trim());
          $('#animal-age-' + animal_id).text($('#edit-age-' + animal_id).val().trim());
          $('#animal-gender-' + animal_id).text($('#edit-gender-' + animal_id).val().trim());
  
          // Szerkesztő űrlap visszaállítása kártya nézetre
          var cardBody = $('#animal-card-body-' + animal_id);
          cardBody.html(`
            <h5 class="card-title" id="animal-name-${animal_id}">${$('#edit-name-' + animal_id).val().trim()}</h5>
            <p class="card-text" id="animal-description-${animal_id}">${$('#edit-description-' + animal_id).val().trim()}</p>
            <p class="card-text">Életkor: <span id="animal-age-${animal_id}">${$('#edit-age-' + animal_id).val().trim()}</span> év</p>
            <p class="card-text">Nem: <span id="animal-gender-${animal_id}">${$('#edit-gender-' + animal_id).val().trim()}</span></p>
            <div class="d-flex justify-content-between align-items-center">
              <button type="button" class="btn btn-edit me-2" onclick="editPet(${animal_id})">
                Szerkesztés
              </button>
              <button type="button" class="btn btn-delete" onclick="deletePet(${animal_id})">
                Törlés
              </button>
            </div>
          `);
        } else {
          // Hibás válasz esetén hibaüzenet megjelenítése
          showToast('error', data.message);
        }
      },
      error: function (xhr, status, error) {
        console.error('Hiba történt:', error);
        showToast('error', 'Hiba történt az állat szerkesztése során.');
      }
    });
  }
  
  // Állat törlésének funkciója
  function deletePet(animal_id) {
    // Megerősítés kérése a törléshez
    if (confirm('Biztosan törölni szeretnéd ezt az állatot?')) {
      var formData = new FormData();
      formData.append('action', 'delete_pet');
      formData.append('animal_id', animal_id);
  
      // Fetch API használata a törléshez
      fetch(window.location.href, {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Sikeres törlés esetén toast üzenet megjelenítése és kártya törlése a DOM-ból
          showToast('success', data.message);
          var deletedCard = document.getElementById('animal-card-' + animal_id);
          if (deletedCard) {
            deletedCard.remove();
          }
        } else {
          // Hibás válasz esetén toast üzenet megjelenítése
          showToast('error', data.message);
        }
      })
      .catch(error => {
        console.error('Hiba történt:', error);
        showToast('error', 'Hiba történt az állat törlése során.');
      });
    }
  }
  
  // Toast üzenet megjelenítése
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
  
    // Toast eltűntetése 3 másodperc után
    setTimeout(function() {
      var bsToast = new bootstrap.Toast(toast);
      bsToast.show();
      setTimeout(function() {
        toast.remove();
      }, 3000);
    }, 100);
  }