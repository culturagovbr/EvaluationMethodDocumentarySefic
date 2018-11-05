<div class="alert success">
    <?php \MapasCulturais\i::_e("Inscrição enviada no dia");?>
    <?php echo isset($entity->sentTimestamp) ? $entity->sentTimestamp->format('d/m/Y à\s H:i:s') : ''; ?>
    <?php
        $app = \MapasCulturais\App::i();
        $reg = $app->repo('Registration')->find($entity->id);
        $slug = $reg->opportunity->evaluationMethodConfiguration->getEvaluationMethod()->getSlug()
    ?>
</div>

<h3 class="registration-header"><?php \MapasCulturais\i::_e("Formulário de Inscrição");?></h3>

<div class="registration-fieldset clearfix">
    <h4><?php \MapasCulturais\i::_e("Número da Inscrição");?></h4>
    <div class="registration-id alignleft">
        <?php echo $entity->number ?>
    </div>
    <div class="alignright">
        <?php if($opportunity->publishedRegistrations):?>
            <div align="center">
                <?php echo $slug==='sefic'?'Nota da Avaliação: '. $reg->consolidatedResult:''; ?>
                <span class="status status-{{getStatusSlug(<?php echo $entity->status ?>)}}">{{getStatusNameById(<?php echo $entity->status ?>)}}</span>
            </div>
        <?php endif; ?>
    </div>
</div>
