// Toast üzenetek kezelése Bootstrap modal-ként
var toastElList = [].slice.call(document.querySelectorAll('.toast'));
var toastList = toastElList.map(function (toastEl) {
    return new bootstrap.Toast(toastEl);
});

// Automatikus megjelenítés
if (toastList.length > 0) {
    toastList.forEach(function (toast) {
        toast.show();
    });
}