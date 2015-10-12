<?php if ($showLabel && $options['label'] !== false): ?>
<?= Form::label($name, $options['label'], $options['label_attr']) ?>
<?php endif; ?>
<?php if ($showField): ?>
    <?php if ($options['wrapper'] !== false): ?>
    <div id="rowset-<?= $name ?>" data-rows-count="<?= $options['rows_count'] ?>" data-remove-text="<?= $options['remove_text'] ?>" class="form-container">
    <?php endif; ?>
<?php endif; ?>
<?php if ($showField): ?>
    <script type="text/javascript">
        $(function () {
            <?php foreach ((array)$options['children'] as $key => $child):
                $array = ['content' => $child->render(), 'name' => $name, 'key' => $key];
                ?>
                createRowSetRow(<?= json_encode($array) ?>);
            <?php endforeach; ?>
        });
    </script>
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
<button class="btn btn-default rowset-add" data-name="<?= $name ?>" type="button">
    <span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> <?= $options['add_text'] ?>
</button>
<?php if($showField): ?>
    <?php if ($options['wrapper'] !== false): ?>
    </div>
    <?php endif; ?>
<?php endif; ?>
<script type="text/javascript">
    $(function () {
        rowSetsProtos['<?= $name ?>'] = <?= json_encode(['proto' => $options['prototype']->render()]) ?>;
    });
</script>