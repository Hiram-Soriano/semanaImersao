<?php
  // Realizar o incluir da conexão
  include ( '../../conexao/conexao.php' );

  // Verificar se um conexão
  if ( $conexao ) {

      // Obter o request vindo do datatable
      $requestData = $_REQUEST ;

      // Obter as colunas vindas do pedido
      $colunas = $requestData [ 'colunas' ];

      // Preparar o comando sql para obter os dados do tipo_usuario
      $sql = "SELECT idtipo_usuario, nome, tipo FROM TIPOS_USUARIOS WHERE 1 = 1" ;

      // Obter o total de registros cadastrados
      $resultado = mysqli_query ( $conexão , $sql );
      $QtdeLinhas = mysqli_num_rows ( $resultado );

      // Verificando se há filtro determinado
      $filtro = $requestData [ 'pesquisa' ] [ 'valor' ];
      if (!empty ( $filtro )) {
          // Montar uma expressão lógica que irá compor os filtros
          // Aqui você pode determinar quais colunas farão parte do filtro
          $sql .= "AND (idtipo_usuario COMO '$ filtro%'" ;
          $sql .= "OU nome como '$ filtro%'" ;
          $sql .= "OU tipo LIKE '$ filtro%')" ;
      }
      // Obter o total dos dados filtrados
      $resultado = mysqli_query ( $conexão , $sql );
      $TotalFiltrados = mysqli_num_rows ( $resultado );

      // Obter valores para ORDER BY      
      $colunaOrdem = $requestData [ 'order' ] [ 0 ] [ 'column' ]; // Obtém uma posição da coluna na ordenação
      $ordem = $colunas [ $colunaOrdem ] [ 'dados' ]; // Obtém o nome da coluna para ordenação
      $direcao = $requestData [ 'order' ] [ 0 ] [ 'dir' ]; // Obtém uma direção de ordenação

      // Obter valores para o LIMIT
      $inicio = $requestData [ 'start' ]; // Obtém o ínicio do limite
      $tamanho = $requestData [ 'length' ]; // Obtém o tamanho do limite

      // Realizar ORDER BY com LIMIT
      $sql . = "PEDIDO POR $ ordem $ direcao LIMIT $ inicio, $ tamanho" ;
      $resultado = mysqli_query ( $conexão , $sql );
      $dados = array();
      while ( $row = mysqli_fetch_assoc ( $resultado )) {
          $dados [] = array_map ( 'utf8_encode' , $row );
      }

      // Monta o objeto json para retornar ao DataTable
      $json_data = array (
          "draw" => intval ( $requestData [ 'draw' ]),
          "recordsTotal" => intval ( $qtdeLinhas ),
          "recordsFiltered" => intval ( $totalFiltrados ),
          "data" => $dados
      );

      // Fecha a conexão com o banco
      mysqli_close ( $conexão );

  } else {
      // Monta um objeto json zerado para retornar ao DataTable
      $json_data = array (
          "empate" => 0 ,
          "recordsTotal" => 0 ,
          "recordsFiltered" => 0 ,
          "data" => array ()
      );
  }

  // Retorna o objeto json para o DataTable
  echo  json_encode ( $json_data );
  ?>