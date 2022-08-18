

@if (!auth()->user()->account_is_approved && (!empty($version) && $version=='mobile'))
    @include('utils.new-register-link')
@endif

<!-- EMPRESAS  -->
<div class="<?php

echo (!empty($version) && $version=='mobile') ? 'p-10 pb-0' : 'pr-30';

?>" id="company-select<?php

    echo (!empty($version) && $version=='mobile') ? '-'.$version : '';

    ?>" style="display:none"><!--d-sm-flex -->
    <div class="d-lg-flex align-items-center justify-content-end">
        <div>
            <select class="sirius-select company-navbar"> </select>
        </div>
    </div>
</div>



