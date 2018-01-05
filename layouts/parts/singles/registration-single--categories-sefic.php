    <div class="registration-fieldset">
        <div>
            <?php $categories = explode(';',$entity->category); ?>
            <h4>Categorias</h4>
            <?php foreach($categories as $category){
                echo $category . '</br>';
            }?>
        </div>
    </div>