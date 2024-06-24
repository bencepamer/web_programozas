document.addEventListener('DOMContentLoaded', function() {
    var categorySelect = document.getElementById('category_id');
    var breedSelect = document.getElementById('breed_id');

    // Alapértelmezetten kutyák fajtáit mutatjuk
    var defaultCategoryId = 1; // Például a kutyák kategória azonosítója
    loadBreeds(defaultCategoryId);

    categorySelect.addEventListener('change', function() {
        var categoryId = this.value;
        if (categoryId) {
            loadBreeds(categoryId);
        } else {
            breedSelect.innerHTML = '<option value="">-- Válasszon kategóriát először --</option>';
        }
    });

    function loadBreeds(categoryId) {
        // AJAX kérés küldése
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'get_breeds.php?category_id=' + categoryId, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    // Fajták listájának frissítése
                    breedSelect.innerHTML = xhr.responseText;
                } else {
                    console.error('AJAX hiba: ' + xhr.status);
                }
            }
        };
        xhr.send();
    }
});