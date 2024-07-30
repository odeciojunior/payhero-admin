@php
    $userModel = new \Modules\Core\Entities\User();
    $account_type = $userModel->present()->getAccountType(auth()->user()->id, auth()->user()->account_owner_id);

    $user = auth()->user();
    $account_is_approved = $user->account_is_approved;
    if($user->is_cloudfox && $user->logged_id){
        $query = $userModel::select('account_is_approved')->where('id',$user->logged_id)->get();
        $account_is_approved = $query[0]->account_is_approved ?? false;
    }
@endphp

@if (!$account_is_approved && $account_type === 'admin' && !empty($version) && $version=='mobile')
    @include('utils.new-register-link')
@endif

<!-- EMPRESAS  -->
<div class="<?php

//echo (!empty($version) && $version=='mobile') ? 'p-10 pb-0' : 'pr-30';
echo 'pr-30';

?>" id="company-select<?php

    //echo (!empty($version) && $version=='mobile') ? '-'.$version : '';

    ?>" style="display:none"><!--d-sm-flex -->
    <div class="d-lg-flex align-items-center justify-content-end">
        <div>
            <select class="sirius-select company-navbar" title="Escolha uma opção"> </select>
        </div>
    </div>
</div>



