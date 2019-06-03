<?php

namespace Modules\Projects\Http\Controllers;

use App\Entities\Company;
use App\Entities\Project;
use Illuminate\Http\Request;
use App\Entities\Shipping;
use App\Entities\UserProject;
use Illuminate\Http\Response;
use App\Entities\ExtraMaterial;
use Illuminate\Routing\Controller;
use Vinkla\Hashids\Facades\Hashids;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Modules\Core\Helpers\CaminhoArquivosHelper;

class ProjectsController extends Controller{

    public function index() {

        $projects = array();

        if(\Auth::user()->hasRole('administrador geral')){

            $projects = Project::all();
        }
        else{
            $userProjects = UserProject::where('user', \Auth::user()->id)->get()->toArray();

            if($userProjects != null){

                foreach($userProjects as $userProject){

                    $project = Project::find($userProject['project']);

                    if($project){
                        $p['id'] = Hashids::encode($userProject['project']);
                        $p['photo'] = $project['photo'];
                        $p['name'] = $project['name'];
                        $p['description'] = $project['description'];
                        $projects[] = $p;
                    }
                }
            }
        }

        return view('projects::index',[
            'projects' => $projects
        ]); 
    }

    public function create() {

        $companies = array();

        $companies = Company::where('user', \Auth::user()->id)->get()->toArray();

        return view('projects::create',[
            'companies' => $companies
        ]);

    }

    public function store(Request $request){

        $dataRequest = $request->all();

        $project = Project::create($dataRequest);

        $projectPhoto = $request->file('project_photo');

        if ($projectPhoto != null) {
            $photoName = 'project_' . $project->id . '_.' . $projectPhoto->getClientOriginalExtension();

            Storage::delete('public/upload/project/'.$photoName);

            $projectPhoto->move(CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO, $photoName);

            $img = Image::make(CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO . $photoName);

            $img->crop($dataRequest['foto_w'], $dataRequest['foto_h'], $dataRequest['foto_x1'], $dataRequest['foto_y1']);

            $img->resize(200, 200);

            Storage::delete('public/upload/project/'.$photoName);

            $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO . $photoName);

            $project->update([
                'foto' => $photoName
            ]);
        }

        UserProject::create([
            'user'                 => \Auth::user()->id,
            'project'              => $project->id,
            'company'              => $dataRequest['company'],
            'type'                 => 'producer',
            'shipment_responsible' => true,
            'access_permission'    => true,
            'edit_permission'      => true,
            'status'               => 'ativo'
        ]);

        return redirect()->route('projects');
    }

    public function update(Request $request){

        $dataRequest = $request->all();

        $project = Project::where('id',Hashids::decode($dataRequest['projeto']))->first();

        $project->update($dataRequest);

        $imagem = $request->file('project_photo');

        if ($imagem != null) {
            $nomeFoto = 'project_' . $project->id . '_.' . $imagem->getClientOriginalExtension();

            Storage::delete('public/upload/project/'.$nomeFoto);

            $imagem->move(CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO, $nomeFoto);

            $img = Image::make(CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO . $nomeFoto); 

            $img->crop($dataRequest['foto_w'], $dataRequest['foto_h'], $dataRequest['foto_x1'], $dataRequest['foto_y1']);

            $img->resize(200, 200);

            Storage::delete('public/upload/project/'.$nomeFoto);

            $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO . $nomeFoto);

            $project->update([
                'photo' => $nomeFoto
            ]);
        }

        $userProject = UserProject::where([
            ['user', \Auth::user()->id],
            ['project', $project['id']]
        ])->first();

        if($userProject->company != $dataRequest['company']){
            $userProject->company = $dataRequest['company'];
            $userProject->update();
        }

        return response()->json('sucesso');
    }

    public function delete(Request $request){

        $dataRequest = $request->all();

        $project = Project::where('id',Hashids::decode($dataRequest['projeto']))->first();

        $plans = Plan::where('project', $project->id)->pluck('id')->toArray();

        $productsPlans = ProductPlan::whereIn('plan',$plans)->pluck('product')->toArray();

        // $project->delete();

        return response()->json('sucesso');

    }

    public function project($id){

        $project = Project::where('id',Hashids::decode($id))->first();

        $photo = '/'.CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO.$project->photo."?dummy=".uniqid();

        $projectId = Hashids::encode($project->id);

        return view('projects::project',[
            'projeto'    => $project,
            'foto'       => $photo,
            'projeto_id' => $projectId
        ]);
    }

    public function edit($id){

        $project = Project::where('id',Hashids::decode($id))->first();

        $materiaisExtras = ExtraMaterial::where('project',$project->id)->get()->toArray();

        $companies = Company::where('user', \Auth::user()->id)->get()->toArray();

        $shippings = Shipping::where('project',$project->id)->get()->toArray();

        $producer = UserProject::where([
            ['user', \Auth::user()->id],
            ['project', $project['id']]
        ])->first();

        $view = view('projects::edit',[
            'project'          => $project,
            'companies'        => $companies,
            'extra_materials'  => $materiaisExtras,
            'emp'              => $producer->company,
            'shippings'        => $shippings,
        ]);

        return response()->json($view->render());
    }

    public function getDadosProject($id){

        $project = Project::find(Hashids::decode($id)[0]); 
        $idProject = Hashids::encode($project->id);

        $userProject = UserProject::where([
            ['project',$project['id']],
            ['tipo','produtor']
        ])->first();

        $usuario = User::find($userProject['user']);
        $planos = Plano::where('project',$project['id'])->get()->toArray();

        foreach($planos as &$plano){
            $plano['lucro'] = number_format($plano['preco'] * $project['porcentagem_afiliados'] / 100, 2);
        }
        
        $view = view('projects::detalhes',[
            'id_project' => $idProject,
            'project'    => $project,
            'planos'     => $planos,
            'produtor'   => $usuario['name'],
            'empresa'    => $produtor->empresa,
        ]);

        return response()->json($view->render());
    }

    public function addMaterialExtra(Request $request){

        $dataRequest = $request->all();

        $dataRequest['descricao'] = $dataRequest['descricao_material_extra'];

        if($dataRequest['tipo'] == 'video'){
            $dataRequest['material'] = $dataRequest['material_extra_video'];
            MaterialExtra::create($dataRequest);
        }

        else if($dataRequest['tipo'] == 'imagem'){

            $materialExtra = MaterialExtra::create($dataRequest);

            $imagem = $request->file('material_extra_imagem');

            if ($imagem != null) {
                $nomeFoto = 'foto_' . $materialExtra->id . '_.' . $imagem->getClientOriginalExtension();
    
                Storage::delete('public/upload/materialextra/fotos/'.$nomeFoto);
    
                $imagem->move(CaminhoArquivosHelper::CAMINHO_MATERIAL_EXTRA_PROJEct_FOTO, $nomeFoto);
    
                $img = Image::make(CaminhoArquivosHelper::CAMINHO_MATERIAL_EXTRA_PROJEct_FOTO . $nomeFoto);

                Storage::delete('public/upload/materialextra/fotos/'.$nomeFoto);

                $img->save(CaminhoArquivosHelper::CAMINHO_MATERIAL_EXTRA_PROJEct_FOTO . $nomeFoto);

                $materialExtra->update([
                    'material' => $nomeFoto
                ]);
            }

        }
        else if($dataRequest['tipo'] == 'pdf'){

            $materialExtra = MaterialExtra::create($dataRequest);

            $arquivo = $request->file('material_extra_pdf');

            if ($arquivo != null) {
                $nome_pdf = 'pdf_' . $materialExtra->id . '_.' . $arquivo->getClientOriginalExtension();

                Storage::delete('public/upload/materialextra/pdfs/'.$nome_pdf);

                $arquivo->move(CaminhoArquivosHelper::CAMINHO_MATERIAL_EXTRA_PROJEct_FOTO, $nome_pdf);

                $img = Image::make(CaminhoArquivosHelper::CAMINHO_MATERIAL_EXTRA_PROJEct_FOTO . $nome_pdf);

                Storage::delete('public/upload/materialextra/pdfs/'.$nome_pdf);

                $img->save(CaminhoArquivosHelper::CAMINHO_MATERIAL_EXTRA_PROJEct_FOTO . $nome_pdf);

                $materialExtra->update([
                    'material' => $nome_pdf
                ]);
            }

        }

        return response()->json('sucesso');
    }

    public function deletarMaterialExtra(Request $request){

        $dataRequest = $request->all();

        MaterialExtra::find($dataRequest['id_material_extra'])->delete();

        return response()->json('sucesso');
    }

}
