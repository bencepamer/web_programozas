$(document).ready(function() {
    // Törlési gomb eseménykezelője
    $('.delete-message').click(function() {
        var messageId = $(this).data('message-id');

        // AJAX kérés küldése a törléshez
        $.ajax({
            type: 'POST',
            url: 'messages.php', // Az aktuális oldal URL-je, ahol ez a kód van
            data: { action: 'delete_message', message_id: messageId },
            dataType: 'json',
            success: function(response) {
                if (response.status == 'success') {
                    // Toast üzenet megjelenítése
                    showToast('success', response.message);
                    // Törölt üzenet eltávolítása az UI-ból
                    $('[data-message-id="' + messageId + '"]').closest('.list-group-item').remove();
                } else {
                    showToast('error', 'Hiba történt az üzenet törlése közben: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                showToast('error', 'Hiba történt az üzenet törlése közben: ' + error);
            }
        });
    });

    // Toast üzenet megjelenítése
    function showToast(type, message) {
        var toastClass = type === 'success' ? 'bg-success' : 'bg-danger';
        var toastHtml = '<div class="toast ' + toastClass + ' text-white" role="alert" aria-live="assertive" aria-atomic="true">' +
                            '<div class="toast-body">' + message + '</div>' +
                        '</div>';

        $('#toast-container').append(toastHtml); // Toast konténerhez hozzáadás

        // Toast eltűntetése animációval
        setTimeout(function() {
            $('.toast').fadeOut(function() {
                $(this).remove();
            });
        }, 3000); // 3 másodperc után tűnik el
    }
});