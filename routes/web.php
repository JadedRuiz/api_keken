<?php
//RUTAS DEL PROYECTO

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->get("obtenerPerfiles","Controller@obtenerPerfiles");
    //CRUD DE USUARIO
    $router->group(['prefix' => 'usuario'], function () use ($router) {
        $router->get("obtenerUsuarios","UsuarioController@obtenerUsuarios");
        $router->post("nuevoUsuario","UsuarioController@nuevoUsuario");
        $router->post("login","UsuarioController@iniciarSesion");
    });
    //CRUD DE EMPRESA
    $router->group(['prefix' => 'empresa'], function () use ($router) {
        $router->get("obtenerEmpresas","EmpresaController@obtenerEmpresa");
        $router->post("nuevaEmpresa","EmpresaController@nuevaEmpresa");
    });
    //CRUD RELACION
    $router->group(['prefix' => 'relacion'], function () use ($router) {
        $router->post("buscador","RelacionLaboralController@buscador");
        $router->post("obtenerRelaciones","RelacionLaboralController@obtenerRelaciones");
        $router->post("altaRelacion","RelacionLaboralController@altaRelacion");
        $router->post("modificarRelacion","RelacionLaboralController@modificarRelacion");
        $router->get("obtenerRelacionPorId/{id_relacion}","RelacionLaboralController@obtenerRelacionPorId");
    });
    //CRUD DOCUMENTOS
    $router->group(['prefix' => 'documento'], function () use ($router) {
        $router->get("obtenerDocumentosRelacionConfigurados/{id_empresa}","DocumentoController@obtenerDocumentosRelacionConfigurados");
        $router->get("obtenerDocumentoRelacion/{id_relacion}/{id_documento}","DocumentoController@obtenerDocumentoRelacion");
    });
    //CRUD DEMANDA LABORAL
    $router->group(['prefix' => 'demanda'], function () use ($router) {
        $router->post("buscador","DemandaLaboralController@buscador");
        $router->post("obtenerRelaciones","DemandaLaboralController@obtenerRelaciones");
        $router->post("altaRelacion","DemandaLaboralController@altaRelacion");
        $router->post("modificarRelacion","DemandaLaboralController@modificarRelacion");
        $router->get("obtenerRelacionPorId/{id_relacion}","DemandaLaboralController@obtenerRelacionPorId");
    });
    //CRUD MOVIMIENTO AUDIENCIA
    $router->group(['prefix' => 'audiencia'], function () use ($router) {
        $router->post("buscador","MovAudienciaController@buscador");
        $router->post("obtenerRelaciones","MovAudienciaController@obtenerRelaciones");
        $router->post("altaRelacion","MovAudienciaController@altaRelacion");
        $router->post("modificarRelacion","MovAudienciaController@modificarRelacion");
        $router->get("obtenerRelacionPorId/{id_relacion}","MovAudienciaController@obtenerRelacionPorId");
    });
});
