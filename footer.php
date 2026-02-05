<!-- <script src="https://kit.fontawesome.com/c71e41ff4a.js" crossorigin="anonymous"></script> -->
<script src="./js/fontawesomekit.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" type="text/javascript" charset="utf8"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script>
    // تمرير مصفوفة الترجمة كاملة إلى JavaScript ككائن عالمي
    window.translations = <?php echo json_encode($translate); ?>;
    window.lang = '<?php echo $lang; ?>';
</script>
<script src="./js/script.js?v=<?php echo $nadim->next_version; ?>"></script>

<br>
<br>
<br>
<br>
<footer style="text-align:center; padding:4px; color:#555; font-size:13px;">
    Copyright © 2023 - <?php echo date('Y'); ?> Nadim Al-Zoubi® |
    <a style="color:#00c800 !important;" href="<?php echo $nadim->website_link; ?>" target="_blank">
        <?php echo $nadim->website; ?>
    </a> | All rights reserved | v<?php echo $nadim->next_version; ?>
</footer>

</main>
</div>

</body>

</html>