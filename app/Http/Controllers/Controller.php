<?php

namespace App\Http\Controllers;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Controller extends BaseController
{
    public function crearRespuesta($tipo,$obj,$http_response)
    {
        if($tipo == 1){
            return response()->json(['ok' => true, 'data' => $obj], $http_response);
        }
        if($tipo == 2){
            return response()->json(['ok' => false, 'message' => $obj], $http_response);
        }
    }
    public function getHoraFechaActual(){
        $mytime = Carbon::now();
        return $mytime;
    }
    public function obtenerPerfiles()
    {
        $perfiles = DB::table('rl_catperfiles')
        ->where("activo",1)
        ->get();
        return $this->crearRespuesta(1,$perfiles,200);
    }
    public function subirDocumento($tipo,$arreglo)
    {
        if($tipo == 1){         //INSERTAR/ACTUALIZAR LIGA_RELACION_DOCUMENTO
                $validar = DB::table('rl_ligarelaciondocumento')
                ->where("id_RelacionLaboral",$arreglo["id_RelacionLaboral"])
                ->where("id_documentoDigital",$arreglo["id_documentoDigital"])
                ->first();
                $fecha = $this->getHoraFechaActual();
                $usuario = 1;
                if($validar){
                        //ACTUALIZAR DOCUMENTO
                        try{
                                DB::update('update rl_ligarelaciondocumento set documento = ?, extension = ?, fecha_m = ? where id_relacionDocumento = ?', [$arreglo["documento"],$arreglo["extension"],$fecha,$validar->id_relacionDocumento]);
                                return ["ok"=>true,"message" => "Documento actualizado"];
                        }catch(Throwable $e){
                                return ["ok"=>false, "message" => "Ha ocurrido un error : " . $e->getMessage()];
                        }
                }else{
                        //INSERTAR DOCUMENTO
                        try{
                                DB::insert('insert into rl_ligarelaciondocumento (id_RelacionLaboral, id_documentoDigital, documento, extension, fecha_c, usuario_c, activo) values (?,?,?,?,?,?,?)', [$arreglo["id_RelacionLaboral"],$arreglo["id_documentoDigital"], $arreglo["documento"], $arreglo["extension"], $fecha, $usuario, 1]);
                                return ["ok"=>true,"message" => "Documento insertado"];
                        }catch(Throwable $e){
                                return ["ok"=>false, "message" => "Ha ocurrido un error : " . $e->getMessage()];
                        }
                }
        }
    }
    public function decode_json($code){
        $ultimoCharacter = substr($code,-1);
        $restante = substr($code,0,-1);
        $digitos = substr($restante,-2);
        $longitud = $this->convert1toInvers($digitos[0]).$this->convert1toInvers($digitos[1]);
        $iteraciones = substr($code,0,1);
        $ite=  $this->convertAto1($iteraciones);
        $descrypt = substr($code,0,$longitud+1);
        $descrypt = substr($descrypt,1);
        $cola = substr($code,0,-3);
        $cola = substr($cola,$longitud+1);
        for($i=0; $i<$ite; $i++){
                $descrypt= base64_decode($descrypt);
        }
        $resu = $descrypt.$cola.$ultimoCharacter;
        return base64_decode($resu);
    }
    public function encode_json($code){
        $rand = rand(3,9);
        $base_64 = base64_encode($code);
        $cabecera = substr($base_64, 0, 3);
        $cola = substr($base_64,3);
        for($r =0; $r<$rand; $r++){
                $cabecera = base64_encode($cabecera);
        }
        $longitud = strlen($cabecera);
        $cabecera_cola = substr($cola, 0, -1);
        $cola_cola = substr($cola, -1);
        $longitud_1er = substr($longitud,0,1);
        $longitud_2do = substr($longitud, -1);
        $letras = $this->convert1toInvers($longitud_1er) . $this->convert1toInvers($longitud_2do);
        $letra_rand = $this->convertAto1($rand);
        return $letra_rand.$cabecera.$cabecera_cola.$letras.$cola_cola;
    }
    public function convertAto1($num){
        if(is_numeric($num)){
            switch($num) {
                case 3: return "e";
                        break;
                case 4: return "A";
                        break;
                case 5: return "r";
                        break;
                case 6: return "M";
                        break;
                case 7: return "z";
                        break;
                case 8: return "L";
                        break;
                case 9: return "S";
                        break; 
            }
        }else{
            switch($num) {
                case "e": return 3;
                        break;
                case "A": return 4;
                        break;
                case "r": return 5;
                        break;
                case "M": return 6;
                        break;
                case "z": return 7;
                        break;
                case "L": return 8;
                        break;
                case "S": return 9;
                        break;
                        
            }
        }
    
    }
    public function convert1toInvers($num){
        if(is_numeric($num)){
            switch($num) {
                case 0: return "z";
                        break;
                case 1: return "Y";
                        break;
                case 2: return "x";
                        break;
                case 3: return "W";
                        break;
                case 4: return "v";
                        break;
                case 5: return "U";
                        break;
                case 6: return "t";
                        break;
                case 7: return "S";
                        break;
                case 8: return "r";
                        break;
                case 9: return "Q";
                        break;
                        
            }
        }else{
            switch($num) {
                case "z": return 0;
                        break;
                case "Y": return 1;
                        break;
                case "x": return 2;
                        break;
                case "W": return 3;
                        break;
                case "v": return 4;
                        break;
                case "U": return 5;
                        break;
                case "t": return 6;
                        break;
                case "S": return 7;
                        break;
                case "r": return 8;
                        break;
                case "Q": return 9;
                        break;
            }
        }
    
    }
}
