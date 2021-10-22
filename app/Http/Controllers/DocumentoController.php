<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DocumentoController extends Controller
{
    function obtenerDocumentosRelacionConfigurados($id_empresa){
        $getDocumentos = DB::table('config_documentos as cd')
        ->select("cd.obligatorio","rcdd.documentoDigital as nombre","rcdd.descripcion","cd.id_documentoDigital")
        ->join("rl_catdocumentosdigitales as rcdd","rcdd.id_documentoDigital","=","cd.id_documentoDigital")
        ->where("id_empresa",$id_empresa)
        ->where("tipo_config","relacion")
        ->orderBy("orden","ASC")
        ->get();
        if(count($getDocumentos)>0){
            return $this->crearRespuesta(1,$getDocumentos,200);
        }
        return $this->crearRespuesta(2,"No se tiene configurado documentos, contacte con el administrador",200);
    }

    function obtenerDocumentoRelacion($id_relacion, $id_documento){
        $getDocumento = DB::table('rl_ligarelaciondocumento as rl')
        ->select("rcdd.documentoDigital","documento","extension")
        ->join("rl_catdocumentosdigitales as rcdd","rcdd.id_documentoDigital","=","rl.id_documentoDigital")
        ->where("rl.id_RelacionLaboral",$id_relacion)
        ->where("rl.id_documentoDigital",$id_documento)
        ->get();
        return $this->crearRespuesta(1,$getDocumento[0],200);
    }
}
