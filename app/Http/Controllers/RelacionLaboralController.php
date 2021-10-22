<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RelacionLaboral;
use App\Models\Fotografia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RelacionLaboralController extends Controller
{
    public function buscador(Request $request)
    {
        $id_sucursal = $request["id_sucursal"];
        $palabra = "%".strtoupper($request["palabra"])."%";
        $busqueda = RelacionLaboral::select(DB::raw('CONCAT(apellido_p, " ", apellido_m, " ", nombres) as nombre'),"correo","telefono","id_RelacionLaboral","numero_nomina","rl.url_foto")
        ->join("rl_catfotografias as rl","rl.id_foto","=","relacion_laboral.id_fotografia")
        ->where("id_sucursal",$id_sucursal)
        ->where(function ($query) use ($palabra){
            $query->orWhere(DB::raw('CONCAT(apellido_p, " ", apellido_m, " ", nombres)'),"like",$palabra)
            ->orWhere("numero_nomina","like",$palabra)
            ->orWhere("correo","like",$palabra);
        })
        ->get();
        if(count($busqueda)>0){
            foreach($busqueda as $relacion){
                $relacion->url_foto = Storage::disk('relaciones')->url($relacion->url_foto);
            }
            return $this->crearRespuesta(1,[
                "busqueda" => $busqueda,
                "total" => count($busqueda)
            ],200);
        }else{
            return $this->crearRespuesta(2,"No se han econtrado coincidencias",301);
        }
    }

    public function obtenerRelaciones(Request $request)
    {
        $id_sucursal = $request["id_sucursal"];
        $relaciones = DB::table('relacion_laboral as rll')
        ->select(DB::raw('CONCAT(rll.apellido_p, " ", rll.apellido_m, " ", rll.nombres) as nombre'), "rl.url_foto","rll.correo","rll.telefono","rll.id_RelacionLaboral","rll.numero_nomina")
        ->join("rl_catfotografias as rl","rl.id_foto","=","rll.id_fotografia")
        ->where("rll.id_sucursal",$id_sucursal)
        ->get();
        if(count($relaciones)>0){
            foreach($relaciones as $relacion){
                $relacion->url_foto = Storage::disk('relaciones')->url($relacion->url_foto);
            }
            return $this->crearRespuesta(1,[
                "relaciones" => $relaciones,
                "total" => count($relaciones)
            ],200);
        }else{
            return $this->crearRespuesta(2,"No se cuenta con relaciones",301);
        }
    }

    public function obtenerRelacionPorId($id_relacion)
    {
        $relacion = RelacionLaboral::select("rl.url_foto as user_img","numero_nomina","apellido_p","apellido_m","nombres","fecha_nacimiento","edad","curp","rfc","correo","telefono","direccion","puesto","departamento","sueldo_mensual","sueldo_neto","fecha_ingreso","id_RelacionLaboral","id_RelacionLaboral as documentos","rl.id_foto")
        ->join("rl_catfotografias as rl","rl.id_foto","=","relacion_laboral.id_fotografia")
        ->where("id_RelacionLaboral",$id_relacion)
        ->first();
        if($relacion){
            $documentos_subidos = DB::table('rl_ligarelaciondocumento')
            ->select("id_documentoDigital","documento","extension")
            ->where("id_RelacionLaboral",$id_relacion)
            ->get();
            $relacion->documentos = $documentos_subidos;
            $relacion->fecha_nacimiento = date('Y-m-d',strtotime($relacion->fecha_nacimiento));
            $relacion->fecha_ingreso = date('Y-m-d',strtotime($relacion->fecha_ingreso));
            $relacion->user_img = Storage::disk('relaciones')->url($relacion->user_img);
            return $this->crearRespuesta(1,$relacion,200);
        }
    }

    public function altaRelacion(Request $res)
    {
        if(isset($res["nombres"]) && strlen($res["nombres"]) == 0){
            return $this->crearRespuesta(2,"El 'nombre' no puede ser vacio",200);
        }
        if(isset($res["apellido_p"]) && strlen($res["apellido_p"]) == 0){
            return $this->crearRespuesta(2,"El 'apellido_p' no puede ser vacio",200);
        }
        if(isset($res["apellido_m"]) && strlen($res["apellido_m"]) == 0){
            return $this->crearRespuesta(2,"El 'apellido_m' no puede ser vacio",200);
        }
        if(isset($res["curp"]) && strlen($res["curp"]) == 0){
            return $this->crearRespuesta(2,"El 'curp' no puede ser vacio",200);
        }
        if(isset($res["rfc"]) && strlen($res["rfc"]) == 0){
            return $this->crearRespuesta(2,"El 'rfc' no puede ser vacio",200);
        }
        try{
            $name = "foto_default";
            $path = "default/usuario_default.png";
            if(strlen($res["user_img"])>0 && strlen($res["extension_img"])>0){
                $file = base64_decode($res["user_img"]);
                $path = "EMPRESA/".$res["numero_nomina"]."/foto_user.".$res["extension_img"];
                $name = "foto_personalizada";
                Storage::disk('relaciones')->put($path, $file);
            }
            $foto = new Fotografia;
            $foto->nombre_foto = $name;
            $foto->url_foto = $path;
            $foto->usuario_creacion = $res["usuario_c"];
            $foto->activo = 1;
            $foto->save();
            $id_foto = $foto->id_foto;
            $relacion = new RelacionLaboral();
            $relacion->id_sucursal = $res["id_sucursal"];
            $relacion->id_fotografia = $id_foto;
            $relacion->nombres = strtoupper($res["nombres"]);
            $relacion->apellido_p = strtoupper($res["apellido_p"]);
            $relacion->apellido_m = strtoupper($res["apellido_m"]);
            $relacion->curp = strtoupper($res["curp"]);
            $relacion->rfc = strtoupper($res["rfc"]);
            $relacion->numero_nomina = $res["numero_nomina"];
            $relacion->fecha_nacimiento = date('Y-m-d h:i:s', strtotime($res["fecha_nacimiento"]));
            $relacion->edad = $res["edad"];
            $relacion->correo = $res["correo"];
            $relacion->telefono =  $res["telefono"];
            $relacion->direccion = $res["direccion"];
            $relacion->puesto = strtoupper($res["puesto"]);
            $relacion->departamento = strtoupper($res["departamento"]);
            $relacion->fecha_ingreso = date('Y-m-d h:i:s', strtotime($res["fecha_ingreso"]));
            $relacion->sueldo_mensual = $res["sueldo_mensual"];
            $relacion->sueldo_neto = $res["sueldo_neto"];
            $relacion->usuario_c = $res["usuario_c"];
            $relacion->activo = $res["activo"];
            $relacion->save();
            $documentos = $res["documentos"];
            foreach($documentos as $documento){
                $this->subirDocumento(1,[
                    "id_RelacionLaboral" => $relacion->id_RelacionLaboral,
                    "id_documentoDigital" => $documento["id_documentoDigital"],
                    "documento" => $documento["documento"],
                    "extension" => $documento["extension"]                    
                ]);
            }
            return $this->crearRespuesta(1,"Relacion insertada con éxito",200);
        }catch(Throwable $e){
            return $this->crearRespuesta(2,"Ha ocurrido un error : " . $e->getMessage(),301);
        }
    }

    public function modificarRelacion(Request $res)
    {
        if(isset($res["nombres"]) && strlen($res["nombres"]) == 0){
            return $this->crearRespuesta(2,"El 'nombre' no puede ser vacio",200);
        }
        if(isset($res["apellido_p"]) && strlen($res["apellido_p"]) == 0){
            return $this->crearRespuesta(2,"El 'apellido_p' no puede ser vacio",200);
        }
        if(isset($res["apellido_m"]) && strlen($res["apellido_m"]) == 0){
            return $this->crearRespuesta(2,"El 'apellido_m' no puede ser vacio",200);
        }
        if(isset($res["curp"]) && strlen($res["curp"]) == 0){
            return $this->crearRespuesta(2,"El 'curp' no puede ser vacio",200);
        }
        if(isset($res["rfc"]) && strlen($res["rfc"]) == 0){
            return $this->crearRespuesta(2,"El 'rfc' no puede ser vacio",200);
        }
        
        try{
            $fecha = $this->getHoraFechaActual();
            if(strlen($res["user_img"])>0 && strlen($res["extension_img"])>0){
                $foto = Fotografia::find($res["id_fotografia"]);
                $file = base64_decode($res["user_img"]);
                $path = "EMPRESA/".$res["numero_nomina"]."/foto_user.".$res["extension_img"];
                $name = "foto_personalizada";
                Storage::disk('relaciones')->put($path, $file);
                $foto->nombre_foto = $name;
                $foto->url_foto = $path;
                $foto->usuario_mod = $res["usuario_c"];
                $foto->save();
            }
            $relacion = RelacionLaboral::find($res["id_RelacionLaboral"]);
            $relacion->nombres = strtoupper($res["nombres"]);
            $relacion->apellido_p = strtoupper($res["apellido_p"]);
            $relacion->apellido_m = strtoupper($res["apellido_m"]);
            $relacion->curp = strtoupper($res["curp"]);
            $relacion->rfc = strtoupper($res["rfc"]);
            $relacion->numero_nomina = $res["numero_nomina"];
            $relacion->fecha_nacimiento = date('Y-m-d h:i:s', strtotime($res["fecha_nacimiento"]));
            $relacion->edad = $res["edad"];
            $relacion->correo = $res["correo"];
            $relacion->telefono =  $res["telefono"];
            $relacion->direccion = $res["direccion"];
            $relacion->puesto = strtoupper($res["puesto"]);
            $relacion->departamento = strtoupper($res["departamento"]);
            $relacion->fecha_ingreso = date('Y-m-d h:i:s', strtotime($res["fecha_ingreso"]));
            $relacion->sueldo_mensual = $res["sueldo_mensual"];
            $relacion->sueldo_neto = $res["sueldo_neto"];
            $relacion->usuario_m = $res["usuario_c"];
            $relacion->save();
            $documentos = $res["documentos"];
            foreach($documentos as $documento){
                $this->subirDocumento(1,[
                    "id_RelacionLaboral" => $relacion->id_RelacionLaboral,
                    "id_documentoDigital" => $documento["id_documentoDigital"],
                    "documento" => $documento["documento"],
                    "extension" => $documento["extension"]                    
                ]);
            }
            return $this->crearRespuesta(1,"Relacion modificada",200);
        }catch(Throwable $e){
            return $this->crearRespuesta(2,"Ha ocurrido un error : " . $e->getMessage(),301);
        }
    }
}
