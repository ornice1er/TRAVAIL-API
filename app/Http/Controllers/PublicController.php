<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Route,Response,Storage,Mail;
use App\Models\Maps;
use App\Models\Media;
use App\Models\Poster;
use App\Models\Prestations;
use App\Models\LiensUtile;
use App\Models\Galeries;
use App\Models\StructuresSousTutelles;
use App\Models\TypesStructure;
use App\Models\Structures;
use App\Models\Organigrammes;
use App\Models\Mots;
use App\Models\Category;
use App\Models\Doc;
use App\Utilities\Common;
use Redirect,Http;


class PublicController extends Controller
{
    public function index($category=null)
    {
        $route_name=Route::currentRouteName();
   
        
        switch ($route_name) {
            case 'contact':
            return $this->getContactPage();
            break;
            case 'accueil':
            return $this->getHomePage();
            break;
            case 'dgt':
                return $this->getHomeDGTPage();
                break;
                case 'dgrce':
                    return $this->getHomeDGRCEPage();
                    break;
                    case 'dgfp':
                        return $this->getHomeDGFPPage();
                        break;
            case 'anciens':
            return $this->getAnciensPage();
            break;
            case 'st':
            return $this->getStPage();
            break;
            case 'aof':
                return $this->getAofPage($category);
                break;
            case 'directions':
            return $this->getDirectionsPage();
            break;
            
            case 'organigramme':
            return $this->getorgPage();
            break;
            case 'vision':
            return $this->getVisionPage();
            break;
            case 'sgm':
            return $this->getSGMPage();
            break;
            case 'dd':
            return $this->getDDPage();
            break;
            case 'aof.igsep':
            return $this->getAofIgsepPage();
            break;
            
            case 'ministre':
            return $this->getMinistrePage($category);
            break;
            case 'communiques':
            return $this->getCommuniquesPage();
            break;
            case 'recrutements':
            return $this->getRecrutementPage();
            break;
            case 'stages':
            return $this->getStagePage();
            break;
            case 'formations':
            return $this->getFormationPage();
            break;
            case 'offres':
            return $this->getOffrePage();
            break;
            case 'actualites':
            return $this->getActualitesPage($category);
            break;
            case 'document':
            return $this->getDocumentPage($category);
            break;
            case 'sanctions':
            return $this->getSanctionPage();
            break;
            case 'reformes':
            return $this->getReformesPage();
            break;
            case 'eservices':
                return $this->getPrestationPage();
                break;
                case 'igsep':
                    return $this->getIgsepPage();
                    break;
                case 'structure':
                    return $this->getPresentationPage($category);
                    break;
            default:
                return abort(404);
                break;
        }
    }

    public function getAofPage($id)
    {
        if ($id) {
             $aof=Media::with('aof')->where('type','aof')->where('is_published',true)->where('is_archived',false)->where('id',$id)->first();
             $title="Attributions, Organisations et Fonctionnement";
             $share_path="aof";
             $share_title="Découvrez notre aof";
                         return Common::success("Données de site récupérées", compact(['aof','title','share_path','share_title']));

        }else {
            $type=TypesStructure::where('is_parent',true)->first();
            $structure=Structures::where('type_structure_id',$type->id)->first();
            $aof=Media::with('aof')->where('type','aof')->where('is_published',true)->where('is_archived',false)->where('has_principal_access',true)->where('structure_id',$structure->id)->orderBy('id','desc')->get()->last();
     
             $title="Attributions, Organisations et Fonctionnement";
             $share_path="aof";
             $share_title="Découvrez notre aof";
                 return Common::success("Données de site récupérées", compact(['aof','title','share_path','share_title']));

        }
      }

    public function getContactPage()
    {
        $maps=Maps::all();
        $title="CONTACTS";
        $share_path="contact";
        $share_title="Contactez nous";


         return Common::success("Données de site récupérées", compact(['maps','share_path','share_title','title']));
    }

    public function getPrestationPage()
    {
        $title="E-SERVICES";
        $share_path="prestations";
        $share_title="Nos prestations";
        $type=TypesStructure::where('is_parent',true)->first();
        $structures=Structures::with('mediaPrestations.prestation')->where('type_structure_id',"!=",$type->id)
                                ->whereHas('mediaPrestations',function($query){
                                     $query->where('type','prestation')->where('is_published',true)->where('is_archived',false)->where('has_principal_access',true);
                                })
                                ->get();   
                        //        dd($structures);
                 return Common::success("Données de site récupérées", compact(['structures','share_path','share_title','title']));

    }

    public function getDocumentPage($typ)
    {
        $title="DOCUMENTATION";
        $share_path="document/".$typ;
        $share_title="Découvrez ici nos textes de ".$typ;
        $datas=array();
        $doc_ccom=array();
        if ($typ == "commentaire" || $typ == "officiels") {
            $doc_ccom=Media::with('doc')
            ->where('type','doc')
            ->where('is_published',true)
            ->where('is_archived',false)
            ->where('has_principal_access',true)
            ->orderBy('id','desc')
            ->whereHas("doc",function($query){
                return $query->where('type','commentaire');
            })->get();
        }else {
            # code...
        
        $type=TypesStructure::where('is_parent',true)->first();

        $structures=Structures::with('medias')->where('type_structure_id',"!=",$type->id)->get();
                                $i=0;
                                foreach ($structures as $st) {
                                    $datas[$i]['st']=$st;
                                   $medias=$st->medias;
                                   $j=0;
                                   foreach ($medias as $value) {
                                      $doc= Doc::where("media_id",$value->id)->first();
                                       if ($doc && $doc->type == $typ) {
                                        $datas[$i]['medias'][$j]=$doc;
                                        $j++;
                                       }
                                   }
                                   $i++;
                                    
                                }
                        //        dd($datas);
                               
       // dd($structures);
      //  $prestations=Media::with('prestation')->where('type','prestation')->where('is_published',true)->where('is_archived',false)->where('has_principal_access',true)->orderBy('id','desc')->take(4)->get();
    }       
        $sub_title=$typ;
                         return Common::success("Données de site récupérées", compact(['share_path','title','share_title','datas','sub_title','doc_ccom']));

    }

    public function getAnciensPage()
    {
        $galeries=Galeries::all();
        $title="ANCIENS MINISTRES";
        $share_path="anciens";
        $share_title="Découvrez ici nos anciens ministres ";
                                 return Common::success("Données de site récupérées",compact(['galeries','title','share_path','share_title']));

    }
    public function getorgPage()
    {
       // $org=Organigrammes::all()->last();
       
       $type=TypesStructure::where('is_parent',true)->first();
       $structure=Structures::where('type_structure_id',$type->id)->first();
       $org=Media::with('org')->where('type','organigramme')->where('is_published',true)->where('is_archived',false)->where('has_principal_access',true)->where('structure_id',$structure->id)->orderBy('id','desc')->get()->last();

        $title="ORGANIGRAMME";
        $share_path="organigramme";
        $share_title="organigramme du ministère";
        return Common::success("Données de site récupérées",compact(['org','title','share_path','share_title']));

    }
    public function getStPage()
    {
        $sts=StructuresSousTutelles::all();
        $title="STRUCTURES SOUS TUTELLE";
        $share_path="directions";
        $share_title="Découvrez ici nos directions ";
                return Common::success("Données de site récupérées",compact(['sts','title','share_path','share_title']));

    }
    public function getDirectionsPage()
    {
        $directions=TypesStructure::with('structures')->whereNotIn('id',[1,5,6])->get();
        $title="DIRECTIONS";
        $share_path="directions";
        $share_title="Découvrez ici nos directions ";
                        return Common::success("Données de site récupérées",compact(['directions','title','share_path','share_title']));

    }

     public function getHomePage()
    {
        $actualites=Media::with('actualite')->where('type','actualite')->where('is_published',true)->where('is_archived',false)->where('has_principal_access',true)->orderBy('id','desc')->take(4)->get();
        $concours = Media::with('communique')
            ->where('type', 'communique')
            ->where('is_published', true)
            ->where('is_archived', false)
            ->where('has_principal_access', true)
            ->whereHas('communique', function ($query) {
                $query->where('category', 'Concours');
            })
            ->orderBy('id', 'desc')
            ->take(3)
            ->get();

        $activites = Media::with('communique')
            ->where('type', 'communique')
            ->where('is_published', true)
            ->where('is_archived', false)
            ->where('has_principal_access', true)
            ->whereHas('communique', function ($query) {
                $query->where('category', 'Activité');
            })
            ->orderBy('id', 'desc')
            ->take(3)
            ->get();

        $communiques = $concours->merge($activites)->sortByDesc('id')->values();

        $prestations=Media::with('prestation')->where('type','prestation')->where('is_published',true)->whereHas('prestation',function($q){
                $q->where('status',"active");
        })->where('has_principal_access',true)->orderBy('id','desc')->take(4)->get();
        $posters=Poster::all();
        $doc=Media::with('doc')
        ->where('type','doc')
        ->where('is_published',true)
        ->where('is_archived',false)
        ->where('has_principal_access',true)
        ->orderBy('id','desc')
        ->whereHas("doc",function($query){
            return $query->where('type','commentaire');
        })->first();
        $links=LiensUtile::all();
       
        $faqs=$this->getFaq();
                            return Common::success("Données de site récupérées",compact(['actualites','communiques','posters','prestations','links','doc','faqs']));

    }
    public function getHomeDGTPage()
    {
        $structure=Structures::where('acronym',"DGT")->first();

        $actualites=Media::with('actualite')->where('type','actualite')->where('is_published',true)->where('is_archived',false)->where('has_principal_access',true)->where('structure_id',$structure->id)->orderBy('id','desc')->take(4)->get();
        $communiques=Media::with('communique')->where('type','communique')->where('is_published',true)->where('is_archived',false)->where('has_principal_access',true)->orderBy('id','desc')->take(4)->get();
        $prestations=Media::with('prestation')->where('type','prestation')->where('is_published',true)->where('is_archived',false)->where('has_principal_access',true)->orderBy('id','desc')->take(4)->get();
        $aof=Media::with('aof')->where('type','aof')->where('is_published',true)->where('is_archived',false)->where("structure_id",$structure?->id)->latest()->first();
      
        $links=LiensUtile::all();
        $d_name="DGT";
                                return Common::success("Données de site récupérées",compact(['actualites','communiques','prestations','links','d_name','aof']));

    }
    public function getHomeDGRCEPage()
    {
        $structure=Structures::where('acronym',"DGRCE")->first();

        $actualites=Media::with('actualite')->where('type','actualite')->where('is_published',true)->where('is_archived',false)->where('has_principal_access',true)->orderBy('id','desc')->where('structure_id',$structure->id)->take(4)->get();
        $communiques=Media::with('communique')->where('type','communique')->where('is_published',true)->where('is_archived',false)->where('has_principal_access',true)->orderBy('id','desc')->take(4)->get();
        $prestations=Media::with('prestation')->where('type','prestation')->where('is_published',true)->where('is_archived',false)->where('has_principal_access',true)->orderBy('id','desc')->take(4)->get();
        $aof=Media::with('aof')->where('type','aof')->where('is_published',true)->where('is_archived',false)->where("structure_id",$structure?->id)->latest()->first();
        $links=LiensUtile::all();
        $d_name="DGRCE";
                                        return Common::success("Données de site récupérées",compact(['actualites','communiques','prestations','links','d_name','aof']));

    }
    public function getHomeDGFPPage()
    {
        $structure=Structures::where('acronym',"DGFP")->first();
        $actualites=Media::with('actualite')->where('type','actualite')->where('is_published',true)->where('is_archived',false)->where('has_principal_access',true)->where('structure_id',$structure->id)->orderBy('id','desc')->take(4)->get();
        $communiques=Media::with('communique')->where('type','communique')->where('is_published',true)->where('is_archived',false)->where('has_principal_access',true)->orderBy('id','desc')->take(4)->get();
        $prestations=Media::with('prestation')->where('type','prestation')->where('is_published',true)->where('is_archived',false)->where('has_principal_access',true)->orderBy('id','desc')->take(4)->get();
        $aof=Media::with('aof')->where('type','aof')->where('is_published',true)->where('is_archived',false)->where("structure_id",$structure?->id)->latest()->first();
        //$posters=Posters::all();
        $links=LiensUtile::all();
        $d_name="DGFP";
                                            return Common::success("Données de site récupérées",compact(['actualites','communiques','prestations','links','d_name','aof']));

    }
     public function getCommuniquesPage()
    {
        $communiques=Media::with('communique')->where('type','communique')->where('is_published',true)->where('is_archived',false)->where('has_principal_access',true)->orderBy('id','desc')->get();
        $title="COMMUNIQUES";
        $share_path="communiques";
        $share_title="Découvrez ici nos communiqués ";

                                                return Common::success("Données de site récupérées",compact(['communiques','title','share_path','share_title']));

    }
     public function getActualitesPage($category=null)
    {
        
        if ($category) {
            $category=Category::whereName($category)->first();
            $actualites=Media::with('actualite')->where('type','actualite')->where('is_published',true)->where('is_archived',false)->where('has_principal_access',true)->orderBy('id','desc')->whereHas("actualite",function($query)use($category){
                $query->where("category_id",$category->id);
            })->get();
            $sub_title=$category->name;

        } else {
            $sub_title="";

           $actualites=Media::with('actualite')->where('type','actualite')->where('is_published',true)->where('is_archived',false)->where('has_principal_access',true)->orderBy('id','desc')->get();

        }
        
        $title="ACTUALITES";

        $share_path="actualites/".$category;
        $share_title="Découvrez ici nos textes de ".$category;
      
                                                return Common::success("Données de site récupérées",compact(['actualites','title','share_path','share_title','sub_title']));

    }
     public function getRecrutementPage()
    {
        $recrutements=Media::with('recrutement')->where('type','recrutement')->where('is_published',true)->where('is_archived',false)->where('has_principal_access',true)->orderBy('id','desc')->paginate(10);
        $title="RECRUTEMENT";
        $share_path="recrutements";
        $share_title="Découvrez ici nos recrutements ";

                                                return Common::success("Données de site récupérées",compact(['recrutements','title','share_path','share_title']));

    }
    public function getStagePage()

    {
        $stages=Media::with('stage')->where('type','stage')->where('is_published',true)->where('is_archived',false)->where('has_principal_access',true)->orderBy('id','desc')->paginate(10);
        $title="STAGES";
        $share_path="stages";
        $share_title="Découvrez ici nos ofrres de stage ";
                                                    return Common::success("Données de site récupérées",compact(['stages','title','share_path','share_title']));

    }
    public function getFormationPage()

    {
        $formations=Media::with('formation')->where('type','formation')->where('is_published',true)->where('is_archived',false)->where('has_principal_access',true)->orderBy('id','desc')->get();
        $title="FORMATIONS";
        $share_path="formations";
        $share_title="Découvrez ici nos formations ";

                                                    return Common::success("Données de site récupérées",compact(['formations','title','share_path','share_title']));

    }
    public function getOffrePage()

    {
        $offres=Media::with('offre')->where('type','offre')->where('is_published',true)->where('is_archived',false)->where('has_principal_access',true)->orderBy('id','desc')->get();
        $title="Appels d'offre";
        $share_path="offres";
        $share_title="Découvrez ici nos appels d'offre. ";

                                                    return Common::success("Données de site récupérées",compact(['offres','title','share_path','share_title']));

    }


    public function getVisionPage()
    {
        $type=TypesStructure::where('is_parent',true)->first();

        $structure=Structures::where('type_structure_id',$type->id)->first();

       // ->where('structure_id',$structure->id)
        // $aof=Media::with('aof')->where('type','aof')->where('is_published',true)->where('is_archived',false)->where('has_principal_access',true)->orderBy('id','desc')->get()->last();
        $aof=Media::with('aof')->where('structure_id',$structure->id)->where('type','aof')->where('is_published',true)->where('is_archived',false)->where('has_principal_access',true)->orderBy('id','desc')->first();


        $title="VISION / MISSIONS  ";
        $share_path="vision";
        $share_title="Découvrez notre vision et nos missions";
                                                    return Common::success("Données de site récupérées",compact(['structure','title','share_path','share_title','aof']));

    }
    public function getSGMPage()
    {

        $structure=Structures::where('acronym',"SGM")->first();

       // ->where('structure_id',$structure->id)
        // $aof=Media::with('aof')->where('type','aof')->where('is_published',true)->where('is_archived',false)->where('has_principal_access',true)->orderBy('id','desc')->get()->last();
        $aof=Media::with('aof')->where('structure_id',$structure?->id)->where('type','aof')->where('is_published',true)->where('is_archived',false)->where('has_principal_access',true)->orderBy('id','desc')->first();


        $title= $structure?->name;
        $share_path="sgm";
        $share_title="Attribution, Organisation et Fonctionnement";
                                                    return Common::success("Données de site récupérées",compact(['structure','title','share_path','share_title','aof']));

    }
    public function getPresentationPage($category)
    {

        if ($category=="dd") {
            $direction=TypesStructure::where('title',"Directions Départementales")->first();
            // ->where('structure_id',$structure->id)
             // $aof=Media::with('aof')->where('type','aof')->where('is_published',true)->where('is_archived',false)->where('has_principal_access',true)->orderBy('id','desc')->get()->last();
            // $aof=Media::with('aof')->where('structure_id',$structure?->id)->where('type','aof')->where('is_published',true)->where('is_archived',false)->where('has_principal_access',true)->orderBy('id','desc')->first();
     
     
             $title="Directions Départementales";
             $share_path=$category;
             $share_title="Attribution, Organisation et Fonctionnement";
                                                                 return Common::success("Données de site récupérées",compact(['direction','title','share_path','share_title']));

        } else {
            $structure=Structures::where('acronym',$category)->first();
            // ->where('structure_id',$structure->id)
             // $aof=Media::with('aof')->where('type','aof')->where('is_published',true)->where('is_archived',false)->where('has_principal_access',true)->orderBy('id','desc')->get()->last();
             $aof=Media::with('aof')->where('structure_id',$structure?->id)->where('type','aof')->where('is_published',true)->where('is_archived',false)->where('has_principal_access',true)->orderBy('id','desc')->first();
     
     
            //  $title="AOF / ".strtoupper($category);
             $title=$structure?->name;
             $share_path=$category;
             $share_title="Attribution, Organisation et Fonctionnement";
                                                                              return Common::success("Données de site récupérées",compact(['structure','title','share_path','share_title','aof']));

        }
        
       
    }
    public function getDDPage()
    {

        $type=TypesStructure::where('title','Directions Départementales')->first();
        $structures=Structures::where('type_structure_id',$type->id)->get();
       // ->where('structure_id',$structure->id)
        // $aof=Media::with('aof')->where('type','aof')->where('is_published',true)->where('is_archived',false)->where('has_principal_access',true)->orderBy('id','desc')->get()->last();
        if ($structures->count()!=0) {
            $aof=Media::with('aof')->where('structure_id',$structures[0]?->id)->where('type','aof')->where('is_published',true)->where('is_archived',false)->where('has_principal_access',true)->orderBy('id','desc')->first();


        $title="Directions Départementales";
        $share_path="dd";
        $share_title="Attribution, Organisation et Fonctionnement";
            return Common::success("Données de site récupérées",compact(['structures','title','share_path','share_title','aof']));

        }       
    }
    public function getAofIgsepPage()
    {

        $structure=Structures::where('acronym',"IGSEP")->first();

       // ->where('structure_id',$structure->id)
        // $aof=Media::with('aof')->where('type','aof')->where('is_published',true)->where('is_archived',false)->where('has_principal_access',true)->orderBy('id','desc')->get()->last();
        $aof=Media::with('aof')->where('structure_id',$structure?->id)->where('type','aof')->where('is_published',true)->where('is_archived',false)->where('has_principal_access',true)->orderBy('id','desc')->first();


        $title=$structure?->name;
        $share_path="igsep";
        $share_title="Attribution, Organisation et Fonctionnement";
                return Common::success("Données de site récupérées",compact(['structure','title','share_path','share_title','aof']));

    }

    public function getMinistrePage($id)
    {
        $type=TypesStructure::where('is_parent',true)->first();
        $structure=Structures::where('type_structure_id',$type->id)->first();
        $title="LE MINISTRE";
        $share_path="ministre";
        $share_title="Découvre la biographie de notre Ministre";
        $mots=Mots::where('structure_id',$structure->id)->get();
        $id==null?$mot=$structure->mots->last():$mot=Mots::find($id);
                return Common::success("Données de site récupérées",compact(['structure','title','share_path','share_title','mots','mot']));

    }
   

    public function download($directory,$doc_name,$filename)
    {
        $file= public_path(). "/storage/".$directory."/".$doc_name;

        $file_exist=Storage::disk("public")->exists($directory."/".$doc_name);

        $headers = array(
              'Content-Type: application/pdf',
            );

            if ($file_exist) {
                return Response::download($file, $filename.'.pdf', $headers);
            }else {
                return back();
            }
    }


    public function showFile($directory,$doc_name,$filename)
    {
        
        $file= public_path(). "/storage/".$directory."/".$doc_name;
        $file_exist=Storage::disk("public")->exists($directory."/".$doc_name);



        if ($file_exist) {
            return response()->file($file);
        }else {
            return back();
        }

    }

    public function getIgsepPage()
    {
        $igsep="";
        $share_path="";
        $share_title="";
        $structure=Structures::where('acronym',"IGSEP")->first();
        $org=Media::with('org')->where('type','organigramme')->where('is_published',true)->where('is_archived',false)->where('has_principal_access',true)->where('structure_id',$structure->id)->orderBy('id','desc')->get()->last();

                    return Common::success("Données de site récupérées",compact(['igsep','share_path',"share_title",'structure','org']));

    }


    public function sendMail(Request $request)
    {
        $name=$request->identity;
        $email=$request->email;
        $subject=$request->subject;
       // dd($request->message);
        Mail::send("email.usager", [
            "textmail"=>$request->message
        ], function($message)use ($name,$email,$subject){
            $message->from(env('MAIL_FROM_ADDRESS'), env("MAIL_FROM_NAME"))
                ->subject($subject);
            $message->to(env('MAIL_FROM_ADDRESS'), env("MAIL_FROM_NAME"))
            ;
        });

        return back()->with("success","Votre mail a été envoyé avec succès");
    }


    public function setCookie()
    {
        setcookie('accept_cookie',true, time() +365*24*3600, '/',null, false, true);
		if(isset($_SERVER['HTTP_REFERER']) AND !empty($_SERVER['HTTP_REFERER'])){
		//	header('Location:'.$_SERVER['HTTP_REFERER']);
         return Redirect::to($_SERVER['HTTP_REFERER']);
		}else{
			//header('Location:https://wwww.travail.gouv.bj');
            return Redirect::to('https://wwww.travail.gouv.bj');
		}

        
    }

    public function getReformesPage()
    {
       
        $reformes=[];
        $share_path="";
        $share_title="";
        $title="Suivi des réformes";

        $response = Http::get(env('URI').'/api/reformes/public/suivi-result');
        if ($response->status()>="200" &&  $response->status()<"300") {
            $reformes=json_decode($response->body())->data;


        }
       // dd($response->status(), $response->body());
       
                            return Common::success("Données de site récupérées",compact(['reformes','share_path',"share_title",'title']));

    }


    public function openPDF($dir,$name)
    {

        try {
            $pathToFile = storage_path("/app/public/".$dir."/".$name);

            return response()->file($pathToFile);
        } catch (\Throwable $th) {
            return abort(404);
            
        }
      


    }


    
    public function getFaq()
    {
        $faqs=[];
      
        try {
            
            $response = Http::withoutVerifying()
            ->withOptions(["ssl_verify"=>false])
            ->get('https://api.mataccueil.gouv.bj/api/faq');
            $body=$response->json() ;
            $faqs= $body;
            
        } catch (\Throwable $th) {
            info("error faq". $th->getMessage());
        }finally{
            return $faqs;
        }
    }
}
