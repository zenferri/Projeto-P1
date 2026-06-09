<?php $layoutArea = $layoutArea ?? "landpage"; // Se o layoutArea não for definido, define como landpage. ?>

<?php if ($layoutArea === "app") { ?>
    <footer class="app-footer py-3 mt-5">
        <div class="container text-center">
            <p>&copy; <?php echo date("Y"); ?> Singularys — Tecnologia para quem pensa grande.</p>
        </div>
    </footer>
<?php } else { // se não for app, então vem para cá, em landpage!?>
    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> Singularys - Tecnologia para quem pensa grande.</p>
    </footer>
<?php } ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
<?php if ($layoutArea !== "app") { ?>
    <script src="./js/script_reveal.js?v=23112025"></script>
<?php } ?>
</body>
</html>

<!-- Aqui fechamos o body e o html -->