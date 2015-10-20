<?php if ($showLabel && $showField): ?>
    <?php if ($options['wrapper'] !== false): ?>
    <div <?= $options['wrapperAttrs'] ?> >
    <?php endif; ?>
<?php endif; ?>
    <?php if ($showLabel && $options['label'] !== false): ?>
    <?= Form::label($name, $options['label'], $options['label_attr']) ?>
    <?php endif; ?>
    <?php if ($showField): ?>
        <?php foreach ((array)$options['children'] as $child): ?>
            <div class="lookup-entity-child">
                <?= $child->render(['selected' => $options['selected']], true, true, false) ?>
            </div>
        <?php endforeach; ?>
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