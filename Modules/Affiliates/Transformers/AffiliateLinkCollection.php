<?php

namespace Modules\Affiliates\Transformers;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AffiliateLinkCollection extends ResourceCollection
{
    protected $preserveAllQueryParameters = true;
    public $collects = AffiliateLinkResource::class;
}
