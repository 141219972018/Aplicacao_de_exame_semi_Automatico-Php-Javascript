<?php

    if(!empty($formErrors)){
        foreach($formErrors as $formError){
            echo "<span class='text-danger d-block'> <i class='fas fa-exclamation-circle'></i> ".$formError."</span>";
        }
    }

?>