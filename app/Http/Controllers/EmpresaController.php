<?php

namespace App\Http\Controllers;
use App\Models\Empresa;
use App\Models\Fotografia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;

class EmpresaController extends Controller
{
    public function obtenerEmpresa()
    {
        return Empresa::all();
    }
    public function nuevaEmpresa(Request $res)
    {
        try{
            $razon_social = "";
            $rfc = "";
            $curp = "";
            $name = "";
            $path = "";
            $representante = "";
            $usuario = 0;
            //Validaciones
            if(!isset($res["razon_social"]) || !isset($res["rfc"]) || !isset($res["curp"])){
                return $this->crearRespuesta(2,"Los parametros razon_social, curp y rfc son obligatorios",301);
            }else{
                $curp = strtoupper($res["curp"]);
                $rfc = strtoupper($res["rfc"]);
                $razon_social = strtoupper($res["razon_social"]);
                $representante = $res["representante"];
                $usuario = $res["usuario"];
            }
            if(empty($curp) || empty($rfc) || empty($razon_social)){
                return $this->crearRespuesta(2,"Los parametros razon_social, curp y rfc no pueden ser vacios",301);
            }
            $validar_existensia = Empresa::where("rfc",$rfc)->get();
            if(count($validar_existensia)>0){
                return $this->crearRespuesta(2,"Esta empresa ya se encuentra registrada",301);
            }
            if(isset($res["fotografia"]) && empty($res["fotografia"])){
                $name = "foto_default";
                $path = "default/image-default.png";
            }else{
                $file = $res->file('fotografia');
                $validator = Validator::make(
                    array(
                        'file' => $file,
                    ),
                    array(
                        'file' => 'file|max:5000|mimes:png,jpg,gif',
                    )
                );
                //Validación del tipo de archivo adjuntado
                if ($validator->fails()) {
                    return $this->crearRespuesta(2,"La foto no cuenta con el formato requerido o es demasiado pesada",301);
                }else{
                    $file = $res->fotografia;
                    $name = "foto_personalizada";
                    $path = $res->fotografia->storeAs($razon_social,"logo.png","fotos");
                }
            }
            //Inserts
            $foto = new Fotografia;
            $foto->nombre_foto = $name;
            $foto->url_foto = $path;
            $foto->usuario_creacion = $usuario;
            $foto->activo = 1;
            $foto->save();
            $id_foto = $foto->id_foto;
            Empresa::create([
                "id_foto" => $id_foto,
                "razon_social" => $razon_social,
                "rfc" => $rfc,
                "curp" => $curp,
                "representante" => $representante,
                "usuario_creacion" => $usuario,
                "activo" => 1
            ]);
            return $this->crearRespuesta(1,"EMPRESA REGISTRADA CON EXITO",200);
        }catch(Throwable $e){
            return $this->crearRespuesta(2,"Ha ocurrido un error : " . $e->getMessage(),301);
        }
    }
}
