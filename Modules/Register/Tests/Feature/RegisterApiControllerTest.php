<?php

namespace Modules\Register\Tests\Feature;

use Modules\Core\Entities\Company;
use Modules\Core\Entities\User;
use Modules\Register\Http\Controllers\RegisterApiController;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class RegisterApiControllerTest extends TestCase
{

    /**
     * @var string
     */
    protected $url = 'http://dev.admin.com/api/register/';

    // Route::get('/verify-cpf', 'RegisterApiController@verifyCpf');
    public function testShouldNBeValidWhenCpfIsValidAndNew()
    {
        $response = $this->get($this->url . 'verify-cpf?document=954.467.690-23');
        $response->assertStatus(200);
    }

    public function testShouldNotBeValidWhenCpfIsInvalid()
    {
        $response = $this->get($this->url . 'verify-cpf?document=044.249.593-51');
        $response->assertStatus(403);
    }

    public function testShouldNotBeValidWhenCpfInUse()
    {

        $userModel = new User();
        $userPresenter = $userModel->present();

        $user = $userModel->where(
            [
                ['address_document_status', $userPresenter->getAddressDocumentStatus('approved')],
                ['personal_document_status', $userPresenter->getPersonalDocumentStatus('approved')],
            ]
        )->first();

        $response = $this->get($this->url . 'verify-cpf?document=' . $user->document);
        $response->assertStatus(403);
    }

    // Route::get('/verify-cnpj', 'RegisterApiController@verifyCnpj');
    public function testShouldNotBeValidWhenCnpjIsInvalid()
    {
        $response = $this->get($this->url . 'verify-cnpj?company_document=67.413.127/0001-0311111111111111111');
        $response->assertStatus(403);
    }

    public function testShouldNotBeValidWhenCnpjInUse()
    {

        $companyModel = new Company();
        $companyPresenter = $companyModel->present();

        $company = $companyModel->where(
            [
                ['bank_document_status', $companyPresenter->getBankDocumentStatus('approved')],
                ['address_document_status', $companyPresenter->getAddressDocumentStatus('approved')],
                ['contract_document_status', $companyPresenter->getContractDocumentStatus('approved')],
            ]
        )->first();

        $response = $this->get($this->url . 'verify-cnpj?company_document=' . $company->document);
        $response->assertStatus(403);
    }

    public function testShouldNBeValidWhenCnpjIsValidAndNew()
    {
        $response = $this->get($this->url . 'verify-cnpj?company_document=79.470.211/0001-48');
        $response->assertStatus(200);
    }

    //  Route::get('/verify-email', 'RegisterApiController@verifyEmail');
    public function testShouldNBeValidWhenEmailIsValidAndNew()
    {
        $response = $this->get($this->url . 'verify-email?email=aushduhasda@asdasd.com');
        $response->assertStatus(200);
    }

    public function testShouldNotBeValidWhenEmailIsInvalid()
    {
        $response = $this->get($this->url . 'verify-email?email=aaaaaaaaaaaaa');
        $response->assertStatus(403);
    }

    public function testShouldNotBeValidWhenEmailInUse()
    {

        $user = User::whereNotNull('email')->first();

        $response = $this->get($this->url . 'verify-email?email=' . $user->email);
        $response->assertStatus(403);
    }

    //  Route::get('/upload-documents', 'RegisterApiController@uploadDocuments');
//
//    public function testShouldNotBeValidWhenDocumentIsInvalid()
//    {
//
//        $file=UploadedFile::fake()->image('file.png', 600, 600);
//        $this->post($this->url . 'upload-documents',["fileToUploud" => $file, 'document_type' => 'user']);
//
//        Storage::disk("local")->assertExists($path_file);
//
//    }

}
