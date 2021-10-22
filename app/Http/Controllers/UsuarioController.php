<?php

namespace App\Http\Controllers;
use App\Models\Usuario;
use App\Models\Empresa;
use App\Models\Fotografia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;

class UsuarioController extends Controller
{
    public function obtenerUsuarios()
    {
        return Usuario::all();
    }

    public function nuevoUsuario(Request $res){
        $path = "";
        $name = "";
        $contra = "";
        $usuario = "";
        $nombre = "";
        $tb_usuario = 0;
        //Validaciones
        if(!isset($res["nombre"]) || !isset($res["usuario"]) || !isset($res["password"]) || !isset($res["id_perfil"]) || !isset($res["id_empresa"])){
            return $this->crearRespuesta(2,"Los parametros 'nombre', 'usuario', 'password', 'id_perfil' y 'id_empresa' son obligatorios",301);
        }else{
            $nombre = strtoupper($res["nombre"]);
            $usuario = $res["usuario"];
            $contra = $this->encode_json($res["password"]);
            $id_perfil = $res["id_perfil"];
            $id_empresa = $res["id_empresa"];
            $tb_usuario = $res["usuario_creacion"];
        }
        if(empty($nombre) || empty($usuario) || empty($contra) || empty($id_perfil) || empty($id_empresa)){
            return $this->crearRespuesta(2,"Los parametros 'nombre', 'usuario', 'contra', 'id_perfil' y 'id_empresa' no pueden ser vacios",301);
        }
        $validar_user = Usuario::where("usuario",$usuario)->first();
        if($validar_user){
            return $this->crearRespuesta(2,"Este usuario ya se encuentra en uso, utilice otro",301);
        }
        $validar_empresa = Empresa::where("id_empresa",$id_empresa)->first();
        if(!$validar_empresa){
            return $this->crearRespuesta(2,"La empresa no existe",301);
        }
        if(isset($res["avatar"]) && empty($res["avatar"])){
            $name = "foto_default";
            $path = "default/usuario_default.png";
        }else{
            $file = $res->file('avatar');
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
                $file = $res->avatar;
                $name = "foto_personalizada";
                $path = $res->avatar->storeAs($validar_empresa->razon_social."/".$nombre,"avatar.".$file->extension(),"fotos");
            }
        }
        try{
            //Inserts
            $foto = new Fotografia;
            $foto->nombre_foto = $name;
            $foto->url_foto = $path;
            $foto->usuario_creacion = $tb_usuario;
            $foto->activo = 1;
            $foto->save();
            $id_foto = $foto->id_foto;
            Usuario::create([
                "id_empresa" => $id_empresa,
                "nombre" => $nombre,
                "usuario" => $usuario,
                "contra" => $contra,
                "id_foto" => $id_foto,
                "id_perfil" => $id_perfil,
                "usuario_creacion" => $tb_usuario,
                "activo" => 1
            ]);
            return $this->crearRespuesta(1,"USUARIO INSERTADO CON EXITO",200);
        }catch(Throwable $e){
            return $this->crearRespuesta(2,"Ha ocurrido un error : " . $e->getMessage(),301);
        }
    }

    public function iniciarSesion(Request $res)
    {
        $validar = Usuario::where("usuario",$res["usuario"])
        ->first();
        if($validar){
            if($this->decode_json($validar->contra) == $res["password"]){
                if($validar->activo == 1){
                    $getFoto = Fotografia::where("id_foto",$validar->id_foto)->first();
                    $getEmpresa = Empresa::where("id_empresa",$validar->id_empresa)->first();
                    $getFotoEmpresa = Fotografia::where("id_foto",$getEmpresa->id_foto)->first();
                    $respuesta = [
                        "id_usuario" => $validar->id_usuario,
                        "id_empresa" => $validar->id_empresa,
                        "id_perfil" => $validar->id_perfil,
                        "nombre" => $validar->nombre,
                        "url_foto" => Storage::disk('fotos')->url($getFoto->url_foto),
                        "url_empresa" => Storage::disk('fotos')->url($getFotoEmpresa->url_foto)
                    ];
                    return $this->crearRespuesta(1,$respuesta,200);
                }else{
                    return $this->crearRepuesta(2,"El usuario se encuentra inactivo, cosulte con su administrador",301);
                }
            }else{
                return $this->crearRespuesta(2,"La contraseña no coincide con nuestros datos, intente de nuevo",301);
            }
        }else{
            return $this->crearRespuesta(2,"El usuario no existe",301);
        }
    }
}