<?php
    if(isset($_SESSION['feedbackSuccess'])){
        echo "<script>toastr.success('".$_SESSION['feedbackSuccess']."')</script>";
        unset($_SESSION['feedbackSuccess']);
    }

    if(isset($_SESSION['feedbackFailed'])){
        echo "<script>toastr.error('".$_SESSION['feedbackFailed']."')</script>";
        unset($_SESSION['feedbackFailed']);
    }
?>