    <div class="registration-fieldset">
        <div>
            <?php
            $tipologias = require __DIR__ . '/../../../' . 'tipologia-oportunidades.php';
            $tipologias = call_user_func_array("array_merge", $tipologias);
            $categories = explode(';',$entity->category); ?>
            <h4>Categorias</h4>
            <?php foreach($categories as $category){
                $segmento = $tipologias[$category];
                echo $segmento . '</br>';
            }?>
        </div>
    </div>