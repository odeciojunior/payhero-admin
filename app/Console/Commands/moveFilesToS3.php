<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\TicketAttachment;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserDocument;
use Modules\Core\Services\AmazonFileService;
use Modules\Core\Services\DigitalOceanFileService;

class moveFilesToS3 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'move:digital-ocean-to-s3';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move all files to s3';


    protected $s3Drive;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->s3Drive = Storage::disk('s3_documents');
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        /******* NÃO PRECISA CORRIGIR NO CÓDIGO *******/

        // $this->changeUserPhoto();
        // $this->digitalProducts();
        // $this->userDocuments();
        $this->ticketAttachments();

        /******* PRECISA CORRIGIR NO CÓDIGO *******/

        //$this->withdrawals();
        //$this->products();
        //$this->projects(); logo e photo


    }

    //projects - photo, logo
    private function projects()
    {
        //photos
        $projectsPhoto = Project::select('id', 'photo')->whereNotNull('photo')
            ->where('photo', '!=', '')
            ->where('photo', 'like', '%digitaloceanspaces%')
            ->get();

        try {

            foreach ($projectsPhoto as $project) {

                if (!@file_get_contents($project->photo))
                    continue;

                $photoName = pathinfo($project->photo, PATHINFO_FILENAME);

                //https://cloudfox.nyc3.digitaloceanspaces.com/uploads/user/dX5pjw3RV32lQqy/public/projects/WXQemdmsy2h1oKg4LTvZC54moAHEa00Ix5pOX6Vi.png"

                //MUDAR NO PROJECTAPICONTROLER
                $this->s3Drive->putFileAs(
                    'uploads/public/projects/photos',
                    $project->photo,
                    $photoName,
                    'public'
                );
                $urlPath = $this->s3Drive->url(
                    'uploads/public/projects/photos/' . $photoName
                );
                $project->photo = $urlPath;
                $project->save();

                $this->info('A foto do produto ' . $project->id . ' foi atualizado.');

            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
        }

        $projectsLogo = Project::select('id', 'logo')->whereNotNull('logo')
            ->where('logo', '!=', '')
            ->where('logo', 'like', '%digitaloceanspaces%')
            ->get();

        try {

            foreach ($projectsLogo as $project) {

                if (!@file_get_contents($project->logo))
                    continue;

                $photoName = pathinfo($project->logo, PATHINFO_FILENAME);

                $this->s3Drive->putFileAs(
                    'uploads/public/projects/logos',
                    $project->logo,
                    $photoName,
                    'public'
                );
                $urlPath = $this->s3Drive->url(
                    'uploads/public/projects/logos/' . $photoName
                );

                $project->logo = $urlPath;
                $project->save();

                $this->info('A logo do projeto ' . $project->id . ' foi atualizado.');

            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
        }

    }

    private function withdrawals()
    {
        //photos
        $userDocuments = UserDocument::select('id', 'document_url')->whereNotNull('document_url')
            ->where('document_url', '!=', '')
            ->where('document_url', 'like', '%digitaloceanspaces%')
            ->get();

        $digitalOceanFileService = app(DigitalOceanFileService::class);
        $amazonFileService = app(AmazonFileService::class);
        $amazonFileService->setDisk('s3_documents');

        try {

            //"https://cloudfox.nyc3.digitaloceanspaces.com/uploads/user/wqP5LNZ8VgaRye0/private/documents/FP7IEKG1xZNVUfJQoIS5b56beCFUGhvfLftLqEeq.jpeg"
            foreach ($userDocuments as $document) {

                $temporaryUrl = $digitalOceanFileService->getTemporaryUrlFile($document->document_url, 180);

                if (!@file_get_contents($temporaryUrl))
                    continue;

                $photoName = pathinfo($temporaryUrl, PATHINFO_FILENAME);
                $photoExtension = (explode("?", (pathinfo($temporaryUrl, PATHINFO_EXTENSION))))[0];
                $fullname = $photoName . '.' . $photoExtension;

                $this->s3Drive->putFileAs(
                    'uploads/private/users/documents',
                    $temporaryUrl,
                    $fullname,
                    'private'
                );

                $urlPath = $this->s3Drive->url(
                    'uploads/private/users/documents/' . $fullname
                );


                $document->document_url = $urlPath;
                $document->save();

                $this->info('O documento ' . $document->id . ' foi atualizado.');

            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
        }

    }

    //userdocuments
    private function userDocuments()
    {
        //photos
        $userDocuments = UserDocument::select('id', 'document_url')->whereNotNull('document_url')
            ->where('document_url', '!=', '')
            ->where('document_url', 'like', '%digitaloceanspaces%')
            ->get();

        $digitalOceanFileService = app(DigitalOceanFileService::class);
        $amazonFileService = app(AmazonFileService::class);
        $amazonFileService->setDisk('s3_documents');

        try {

            //"https://cloudfox.nyc3.digitaloceanspaces.com/uploads/user/wqP5LNZ8VgaRye0/private/documents/FP7IEKG1xZNVUfJQoIS5b56beCFUGhvfLftLqEeq.jpeg"
            foreach ($userDocuments as $document) {

                $temporaryUrl = $digitalOceanFileService->getTemporaryUrlFile($document->document_url, 180);

                if (!@file_get_contents($temporaryUrl))
                    continue;

                $photoName = pathinfo($temporaryUrl, PATHINFO_FILENAME);
                $photoExtension = (explode("?", (pathinfo($temporaryUrl, PATHINFO_EXTENSION))))[0];
                $fullname = $photoName . '.' . $photoExtension;

                $this->s3Drive->putFileAs(
                    'uploads/private/users/documents',
                    $temporaryUrl,
                    $fullname,
                    'private'
                );

                $urlPath = $this->s3Drive->url(
                    'uploads/private/users/documents/' . $fullname
                );


                $document->document_url = $urlPath;
                $document->save();

                $this->info('O documento ' . $document->id . ' foi atualizado.');

            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
        }

    }

    //products - digital_product_url --- não precisa modificar no código
    private function digitalProducts()
    {
        //photos
//        $productsDigital = Product::select('id', 'digital_product_url')->whereNotNull('digital_product_url')
//            ->where('digital_product_url', '!=', '')
//            ->where('digital_product_url', 'like', '%digitaloceanspaces%')
//            ->limit(11)->get();
//
//        $digitalOceanFileService = app(DigitalOceanFileService::class);
//        $amazonFileService = app(AmazonFileService::class);
//        $amazonFileService->setDisk('s3_digital_product');
//
//        try {
//
//            foreach ($productsDigital as $product) {
//
//                $temporaryUrl = $digitalOceanFileService->getTemporaryUrlFile($product->digital_product_url, 180);
//
//                if (!@file_get_contents($temporaryUrl))
//                    continue;
//
//                $photoName = pathinfo($temporaryUrl, PATHINFO_FILENAME);
//                $photoExtension  = (explode("?", (pathinfo($temporaryUrl, PATHINFO_EXTENSION))))[0];
//                $fullname = $photoName . '.'.$photoExtension;
//
//                $this->s3Drive->putFileAs(
//                    'products',
//                    $temporaryUrl,
//                    $fullname,
//                    'private'
//                );
//
//                $urlPath = $this->s3Drive->url(
//                    'products/' . $fullname
//                );
//
//                $product->digital_product_url = $urlPath;
//                $product->save();
//
//                $this->info('O documento ' . $product->id . ' foi atualizado.');
//
//            }
//
//            DB::commit();
//
//        } catch (\Exception $e) {
//            DB::rollBack();
//            dd($e->getMessage());
//        }

    }

    //products - photo
    private function products()
    {
        //photos
        $productsPhoto = Product::select('id', 'photo')->whereNotNull('photo')
            ->where('photo', '!=', '')
            ->where('photo', 'like', '%digitaloceanspaces%')
            ->get();

        try {

            foreach ($productsPhoto as $product) {

                if (!@file_get_contents($product->photo))
                    continue;

                $photoName = pathinfo($product->photo, PATHINFO_FILENAME);

                $this->s3Drive->putFileAs(
                    'uploads/public/products',
                    $product->photo,
                    $photoName,
                    'public'
                );
                $urlPath = $this->s3Drive->url(
                    'uploads/public/products/' . $photoName
                );

                $product->photo = $urlPath;
                $product->save();

                $this->info('A foto do produto ' . $product->id . ' foi atualizado.');

            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
        }

    }

    //ticket_attachments - file
    private function ticketAttachments()
    {

        $tichetAttachments = TicketAttachment::whereNotNull('file')
            ->where('file', '!=', '')
            ->where('file', 'like', '%digitaloceanspaces%')
            ->get();

        $digitalOceanFileService = app(DigitalOceanFileService::class);
        $amazonFileService = app(AmazonFileService::class);
        $amazonFileService->setDisk('s3_documents');

        try {

            foreach ($tichetAttachments as $file) {

                $temporaryUrl = $file->file;

                //verifico se é publico
                if (!@file_get_contents($temporaryUrl)) {

                    //verifico se é privado
                    $temporaryUrl = $digitalOceanFileService->getTemporaryUrlFile($file->file, 180);

                    if (!@file_get_contents($temporaryUrl))
                        continue;
                }

                $fileName = pathinfo($temporaryUrl, PATHINFO_FILENAME);
                $fileExtension = (explode("?", (pathinfo($temporaryUrl, PATHINFO_EXTENSION))))[0];
                $fullname = $fileName . '.' . $fileExtension;

                $this->s3Drive->putFileAs(
                    'uploads/private/tickets/attachments',
                    $temporaryUrl,
                    $fullname,
                    'private'
                );

                $urlPath = $this->s3Drive->url(
                    'uploads/private/tickets/attachments/' . $fullname
                );

                $file->file = $urlPath;
                $file->save();

                $this->info('O file ' . $file->id . ' foi atualizado.');

            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
        }

    }

    //user - photo
    private function changeUserPhoto()
    {

        $users = User::select('id', 'photo')->whereNotNull('photo')
            ->where('photo', '!=', '')
            ->where('photo', 'like', '%digitaloceanspaces%')
            ->get();

        try {

            foreach ($users as $user) {

                if (!@file_get_contents($user->photo))
                    continue;

                $photoName = pathinfo($user->photo, PATHINFO_FILENAME);
                $this->s3Drive->putFileAs(
                    'uploads/public/users/profile',
                    $user->photo,
                    $photoName,
                    'public'
                );
                $urlPath = $this->s3Drive->url(
                    'uploads/public/users/profile/' . $photoName
                );

                $user->photo = $urlPath;
                $user->save();

                $this->info('A foto do usuário ' . $user->id . ' foi atualizada.');

            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
        }

    }

}
