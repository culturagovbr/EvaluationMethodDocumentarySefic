    <div class="registration-fieldset">
        <div>
            <?php
            $tipologias = require __DIR__ . '/../../../' . 'tipologia-oportunidades.php';
            $categories = explode(';',$entity->category); ?>
            <h4>Categorias</h4>
            <?php foreach($tipologias as $category){
                $segmento = array_search($category, $tipologias);
                echo $segmento . '</br>';
            }?>
        </div>
    </div>