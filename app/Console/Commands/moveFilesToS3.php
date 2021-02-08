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
use Vinkla\Hashids\Facades\Hashids;

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
        //verificar link ta privado
        //$this->projects();
        //$this->products();
        //$this->ticketAttachments();
        //$this->changeUserPhoto();
    }

    //projects - photo, logo
    private function projects()
    {
        //photos
        $projectsPhoto = Project::select('id', 'photo')->whereNotNull('photo')
            ->where('photo', '!=', '')
            ->where('photo', 'like', '%digital%')
            ->limit(5)->get();

        try {

            foreach ($projectsPhoto as $project) {

                if (!@file_get_contents($project->photo))
                    continue;

                $hashid = Hashids::encode($project->id);
                $photoName = pathinfo($project->photo, PATHINFO_FILENAME);

                //https://cloudfox.nyc3.digitaloceanspaces.com/uploads/user/dX5pjw3RV32lQqy/public/projects/WXQemdmsy2h1oKg4LTvZC54moAHEa00Ix5pOX6Vi.png"

                $this->s3Drive->putFileAs(
                    'uploads/user/' . $hashid . '/public/projects',
                    $project->photo,
                    $photoName,
                    'public'
                );
                $urlPath = $this->s3Drive->url(
                    'uploads/user/' . $hashid . '/public/projects/' . $photoName
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
            ->where('logo', 'like', '%digital%')
            ->limit(5)->get();


        try {

            foreach ($projectsLogo as $project) {

                if (!@file_get_contents($project->logo))
                    continue;

                $hashid = Hashids::encode($project->id);
                $photoName = pathinfo($project->logo, PATHINFO_FILENAME);

                $this->s3Drive->putFileAs(
                    'uploads/projects/' . $hashid . '/public/projects',
                    $project->logo,
                    $photoName,
                    'public'
                );
                $urlPath = $this->s3Drive->url(
                    'uploads/projects/' . $hashid . '/public/projects/' . $photoName
                );
                $project->photo = $urlPath;
                $project->save();

                $this->info('A logo do projeto ' . $project->id . ' foi atualizado.');

            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
        }

    }

    private function userDocuments()
    {
        //photos
        $userDocuments = UserDocument::select('id', 'document_url')->whereNotNull('document_url')
            ->where('document_url', '!=', '')
            ->where('document_url', 'like', '%digital%')
            ->limit(115)->get();

        try {

            //"https://cloudfox.nyc3.digitaloceanspaces.com/uploads/user/wqP5LNZ8VgaRye0/private/documents/FP7IEKG1xZNVUfJQoIS5b56beCFUGhvfLftLqEeq.jpeg"
            foreach ($userDocuments as $document) {

                if (!@file_get_contents($document->document_url))
                    continue;

                dd(1, $document);

                $hashid = Hashids::encode($document->id);
                $photoName = pathinfo($document->document_url, PATHINFO_FILENAME);

                $this->s3Drive->putFileAs(
                    'uploads/user/' . $hashid . '/public/documents',
                    $document->document_url,
                    $photoName,
                    'public'
                );
                $urlPath = $this->s3Drive->url(
                    'uploads/user/' . $hashid . '/public/documents/' . $photoName
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

    //products - photo
    private function products()
    {
        //photos
        $productsPhoto = Product::select('id', 'photo')->whereNotNull('photo')
            ->where('photo', '!=', '')
            ->where('photo', 'like', '%digital%')
            ->limit(1)->get();

        try {

            foreach ($productsPhoto as $product) {

                $hashid = Hashids::encode($product->id);
                $photoName = pathinfo($product->photo, PATHINFO_FILENAME);

                $this->s3Drive->putFileAs(
                    'uploads/user/' . $hashid . '/public/products',
                    $product->photo,
                    $photoName,
                    'public'
                );
                $urlPath = $this->s3Drive->url(
                    'uploads/user/' . $hashid . '/public/products/' . $photoName
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

        $files = TicketAttachment::whereNotNull('file')
            ->where('file', '!=', '')
            ->where('file', 'like', '%digital%')
            ->limit(32)->get();

        try {

            foreach ($files as $file) {

                if (!@file_get_contents($file->file))
                    continue;

                $hashid = Hashids::encode($file->id);
                $fileName = pathinfo($file->file, PATHINFO_FILENAME);

                $this->s3Drive->putFileAs(
                    'uploads/ticket/' . $hashid . '/public/attachments',
                    $file->file,
                    $fileName,
                    'public'
                );

                $urlPath = $this->s3Drive->url(
                    'uploads/ticket/' . $hashid . '/public/attachments/' . $fileName
                );

                $file->file = $urlPath;
                $file->save();

                $this->info('O file ' . $file->id . ' foi atualizado.');

            }
            https://cloudfox.nyc3.digitaloceanspaces.com/uploads/ticket/YKV603kakGw8ymD/private/attachments/mM5vqDqKB3VoTqo8owGEvEemp1qdvP6JZMEdf3U1.jpeg
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
            ->where('photo', 'like', '%digital%')
            ->limit(1)->get();

        try {

            foreach ($users as $user) {

                $hashid = Hashids::encode($user->id);
                $photoName = pathinfo($user->photo, PATHINFO_FILENAME);
                $this->s3Drive->putFileAs(
                    'uploads/user/' . $hashid . '/public/profile',
                    $user->photo,
                    $photoName,
                    'public'
                );
                $urlPath = $this->s3Drive->url(
                    'uploads/user/' . $hashid . '/public/profile/' . $photoName
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
