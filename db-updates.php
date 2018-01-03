<?php
namespace MapasCulturais;

$app = App::i();
$em = $app->em;
$conn = $em->getConnection();

return array(

    'Changing column type for categories' => function() use ($conn){
        $conn->executeQuery("ALTER TABLE registration ALTER COLUMN category TYPE text");
    }

);

