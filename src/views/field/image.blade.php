<?php if ($showLabel && $showField): ?>
    <?php if ($options['wrapper'] !== false): ?>
    <div <?= $options['wrapperAttrs'] ?> >
    <?php endif; ?>
<?php endif; ?>
<?php if ($showLabel && $options['label'] !== false): ?>
<?= Form::label($name, $options['label'], $options['label_attr']) ?>
<?php endif; ?>
<?php if ($showField): ?>
    <div class="image-field">
        <div class="image-field-row">
            <div class="image-field-input">
                <?php if(!empty($options['value'])): ?>
                    <?= Form::hidden($name, $options['value'], ['id' => 'image-field-hidden-'.$name]) ?>
                <?php endif ?>
                <?= Form::file($name, $options['attr']) ?>
            </div>
            <?php if(!empty($options['value'])): ?>
                <div id="image-field-thumbnail-<?= $name ?>">
                    <div class="image-field-cell">
                        <button type="button" data-name="<?= $name ?>" data-required="<?= (int)$options['required'] ?>" class="btn btn-default image-field-clear"><span aria-hidden="true" class="glyphicon glyphicon-remove"></span> <?= $options['clear_text'] ?></button>
                    </div>
                    <div class="image-field-cell hidden-xs">
                        {!! renderImage($options['value'], ['class' => 'img-rounded'], 150) !!}
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>
    <?php if ($options['help_block']['text']): ?>
        <<?= $options['help_block']['tag'] ?> <?= $options['help_block']['helpBlockAttrs'] ?>>
            <?= $options['help_block']['text'] ?>
        </<?= $options['help_block']['tag'] ?>>
    <?php endif; ?>
<?php endif; ?>
<?php if ($showError && isset($errors)): ?>
    <?php foreach ($errors->get($nameKey) as $err): ?>
        <div <?= $options['errorAttrs'] ?>><?= $err ?></div>
    <?php endforeach; ?>
<?php endif; ?>
<?php if ($showLabel && $showField): ?>
    <?php if ($options['wrapper'] !== false): ?>
    </div>
    <?php endif; ?>
<?php endif; ?>