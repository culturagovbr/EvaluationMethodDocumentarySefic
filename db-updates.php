<?php
namespace MapasCulturais;

$app = App::i();
$em = $app->em;
$conn = $em->getConnection();

return array(

    'Changing column type for categories' => function() use ($conn){
        $conn->executeQuery("ALTER TABLE registration ALTER COLUMN category TYPE text");
    },

    'Fix registration categories that were saved incorrectly' => function() use ($conn, $app){
        $segmentos = require __DIR__ . '/' . 'tipologia-oportunidades.php';
        $segmentos = call_user_func_array("array_merge", $segmentos);
        $segmento_repetido = "Ações de capacitação e treinamento de pessoal";
        $oportunidade_id = 775;

        $oportunidades = $conn->fetchAll("
            SELECT
                id,
                opportunity_id,
                category
            FROM
                registration
            WHERE
                status = 1
                AND opportunity_id = $oportunidade_id
                AND category not like '%$segmento_repetido%';
        ");

        foreach($oportunidades as $oportunidade){
            $categorias = array();

            foreach(explode(';', $oportunidade['category']) as $categoria){
                $categorias[] = array_search($categoria, $segmentos);
            }

            $oportunidade['category'] = implode(';', $categorias);

            $sql[] = "
                UPDATE
                    registration
                SET
                    category = '{$oportunidade['category']}'
                WHERE
                    id = {$oportunidade['id']}
            ";
        }

        $oportunidades = $conn->fetchAll("
            SELECT
                id
            FROM
                registration
            WHERE
                opportunity_id = $oportunidade_id
                AND (category like '%$segmento_repetido%' OR status = 0)
        ");

        foreach($oportunidades as $oportunidade){
            $sql[] = "
                UPDATE
                    registration
                SET
                    status = 0,
                    category = ''
                WHERE
                    id = {$oportunidade['id']}
            ";
        }

        try{
            $conn->beginTransaction();

            foreach($sql as $query){
                $conn->executeQuery($query);
            }

            $conn->commit();
        }catch (Exception $e){
            $conn->rollback();
            $app->log->debug($e->getMessage());
        }
    }

);

