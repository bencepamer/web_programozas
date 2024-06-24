<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>PET Adoption</title>
    
    <!-- Bootstrap CSS betöltése -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Saját CSS stílusok -->
    <style>
        /* Saját CSS stílusok */
        .footer-icon {
            max-width: 30px; /* Maximális szélesség ikonokhoz */
        }
    </style>
</head>
<body>
    <!-- Footer rész -->
    <footer class="bg-light text-white">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-4 text-center">
                    <!-- Instagram ikon és link -->
                    <p class="text-muted mb-4"><a href="https://www.instagram.com/vts_subotica_szabadka/"><img src="images/instagram.png" alt="Instagram" class="footer-icon"></a></p>
                    <!-- Facebook ikon és link -->
                    <p class="text-muted mb-4"><a href="https://www.facebook.com/vtsSu/"><img src="images/facebook.png" alt="Facebook" class="footer-icon"></a></p>
                    <!-- VTS ikon és link -->
                    <p class="text-muted mb-4"><a href="https://www.vts.su.ac.rs/"><img src="images/vts.png" alt="VTS" class="footer-icon"></a></p>
                </div>
            </div>
        </div>
        <!-- Alsó rész lábléc -->
        <div class="bg-light py-4">
            <div class="container text-center">
                <p class="text-muted mb-0">&copy; 2024 PET Adoption. Minden jog fenntartva.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript rész a footer pozícionálására -->
    <script>
        // Funkció a footer pozícionálására
        function setFooterPosition() {
            const footer = document.querySelector('footer');
            const windowHeight = window.innerHeight;
            const bodyHeight = document.body.scrollHeight;
            const footerHeight = footer.offsetHeight;

            // Ha az ablak magassága nagyobb, mint az oldal tartalma
            if (windowHeight > bodyHeight) {
                footer.style.position = 'fixed';
                footer.style.bottom = '0';
                footer.style.left = '0';
                footer.style.right = '0';
            } else {
                footer.style.position = 'static'; // Alapértelmezett pozíció
            }
        }

        // Oldal betöltésekor és ablakméret változáskor is frissítjük a pozíciót
        window.addEventListener('load', setFooterPosition);
        window.addEventListener('resize', setFooterPosition);
    </script>

</body>
</html>
