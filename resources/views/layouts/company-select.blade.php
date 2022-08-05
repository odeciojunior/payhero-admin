<!-- EMPRESAS  -->
<div class="<?php

echo (!empty($version) && $version=='mobile') ? 'p-10 pb-0' : 'pr-20';

?>" id="company-select<?php

    echo (!empty($version) && $version=='mobile') ? '-'.$version : '';

    ?>" style="display:none"><!--d-sm-flex -->
    <div class="d-lg-flex align-items-center justify-content-end">
        <div>
            <select class="sirius-select company-navbar"> </select>
        </div>
    </div>
</div>



