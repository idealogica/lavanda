<?php if ($showLabel && $options['label'] !== false): ?>
<?= Form::label($name, $options['label'], $options['label_attr']) ?>
<?php endif; ?>
<?php if ($showField): ?>
    <?php if ($options['wrapper'] !== false): ?>
    <div class="form-container">
    <?php endif; ?>
<?php endif; ?>
<?php if ($showField): ?>
    {!! $options['children']['entity']->render() !!}
<?php endif; ?>
<?php if($showField): ?>
    <?php if ($options['wrapper'] !== false): ?>
    </div>
    <?php endif; ?>
<?php endif; ?>