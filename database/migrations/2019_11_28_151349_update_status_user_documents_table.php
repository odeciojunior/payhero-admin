<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserDocument;

class UpdateStatusUserDocumentsTable extends Migration
{
    /**
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function up()
    {
        $userDocumentModel = new UserDocument();
        $userModel         = new User();
        $documents         = $userDocumentModel->with('user')->where('status', '=', null)->get();
        foreach ($documents as $document) {
            if ($document->document_type_enum == $userModel->present()->getDocumentType('personal_document')) {
                $document->update(['status' => $document->user->personal_document_status]);
            } else {
                $document->update(['status' => $document->user->address_document_status]);
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
