<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\CompanyDocument;

class UpdateStatusCompanyDocumentsTable extends Migration
{
    /**
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function up()
    {
        $companyDocumentModel = new CompanyDocument();
        $companyModel         = new Company();

        $documents = $companyDocumentModel->with('company')->where('status', '=', null)->whereHas('company')->get();
        foreach ($documents as $document) {
            if ($document->document_type_enum == $companyModel->present()->getDocumentType('bank_document_status')) {
                $document->update(['status' => $document->company->bank_document_status]);
            } else if ($document->document_type_enum == $companyModel->present()
                                                                     ->getDocumentType('address_document_status')) {
                $document->update(['status' => $document->company->address_document_status]);
            } else {
                $document->update(['status' => $document->company->contract_document_status]);
            }
        }
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        //
    }
}
