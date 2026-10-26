<?php
// Avalik jalus. Eeldab muutujat $base.
?>
    <footer class="bg-white border-top mt-5">
        <div class="container py-5">
            <div class="row gy-4">
                <div class="col-lg-3">
                    <p class="fw-bold fs-5 mb-0">Loomade Varjupaik</p>
                    <p class="mt-3">Aitame koduta ja hätta sattunud loomi ning leiame neile armastavad kodud.</p>
                </div>
                <div class="col-lg-3">
                    <h2 class="h5 fw-bold mb-3">Kiirlingid</h2>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="<?= e($base) ?>index.php" class="text-decoration-none text-dark">Avaleht</a></li>
                        <li class="mb-2"><a href="<?= e($base) ?>all_animals.php" class="text-decoration-none text-dark">Loomad</a></li>
                        <li class="mb-2"><a href="#" class="text-decoration-none text-dark">Kuidas aidata</a></li>
                        <li class="mb-2"><a href="#" class="text-decoration-none text-dark">Meist</a></li>
                        <li><a href="<?= e($base) ?>contact.php" class="text-decoration-none text-dark">Kontakt</a></li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h2 class="h5 fw-bold mb-3">Kontakt</h2>
                    <p><i class="fa-solid fa-location-dot me-2"></i>Varjupaiga tee 1, Tallinn 12345</p>
                    <p><i class="fa-solid fa-phone me-2"></i>+372 5555 1234</p>
                    <p><i class="fa-regular fa-envelope me-2"></i>info@varjupaik.ee</p>
                    <p><i class="fa-regular fa-clock me-2"></i>E–P 10:00–18:00</p>
                </div>
                <div class="col-lg-3">
                    <h2 class="h5 fw-bold mb-3">Toeta meid</h2>
                    <p>Sinu annetus aitab meil pakkuda loomadele paremat elu.</p>
                    <a href="#" class="btn btn-success px-4">Anneta</a>
                </div>
            </div>
            <hr class="my-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <small>&copy; <?= date('Y') ?> Loomade Varjupaik. Kõik õigused kaitstud.</small>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>
