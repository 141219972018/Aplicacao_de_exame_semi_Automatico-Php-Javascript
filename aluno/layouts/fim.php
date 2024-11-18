<script>
    toastr.options = {
        "positionClass": "toast-bottom-right",
    }
    $(document).bind("contextmenu", function(e) {
        return false;
    });
</script>
<?php require_once('../includes/toastr.php') ?>
</body>

</html>