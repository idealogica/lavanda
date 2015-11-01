var rowSetsProtos = {};

$(function()
{
    // tooltips on action buttons
    $('[data-toggle="tooltip"]').tooltip({delay: { "show": 1000, "hide": 100 }})

    // handler for click event on "delete" buttons
    $('.action-delete').click(function ()
    {
        var url = $(this).attr('href');
        var token = $(this).attr('data-token');
        bootbox.confirm("Are you sure?", function(result)
        {
            if(result)
            {
                $.post(url, {
                    '_method' : 'delete',
                    '_token' : token},
                function (res)
                {
                    location.href = res;
                });
            }
        });
        return false;
    });

    // handler for refreshing page after sort select is changed by user
    $('#sort').change(function ()
    {
        $.cookie('sort', $(this).val(), {expires: 7, path: location.pathname});
        location.reload();
    });

    // removes all empty image fields from forms
    $('form').submit(function ()
    {
        $(this).find('input.lavanda-image-upload').each(function ()
        {
            var val = $(this).val();
            if(!val)
            {
                $(this).attr('disabled','disabled');
            }
        });
        return true;
    });

    // handles click on "add item" button of rowset from field
    $('.rowset-add').click(function ()
    {
        var name = $(this).attr('data-name');
        var rowset = $('#rowset-' + name);
        var rowsCount = rowset.attr('data-rows-count');
        var proto = rowSetsProtos[name]['proto'].
           replace(/#__IDX__/g, '#' + (rowsCount++ + 1)).
           replace(/__IDX__/g, rowsCount);
        createRowSetRow({
           'content' : proto,
           'name' : name,
           'key' : rowsCount});
        rowset.attr('data-rows-count', rowsCount);
    });

    // handles click on "add item" button of rowset from field
    $(document).on('click', '.rowset-remove', function ()
    {
        var name = $(this).attr('data-name');
        var idx = $(this).attr('data-idx');
        $('#sub-form-' + name + '-' + idx).remove();
    });

    // handles click "clear value" button of image form field
    $(document).on("click", ".image-field-clear", function ()
    {
        var name = $(this).attr('data-name').replace(/([\[\]])/g, "\\$1");
        var required = $(this).attr('data-required');
        if(required === '1')
        {
            $('#' + name).attr('required', 'required');
        }
        $('#image-field-hidden-' + name).val('');
        $('#image-field-thumbnail-' + name).hide();
    });

    // adds calendar widget to date form inputs
    $(document).on("focusin", "input[data-calendar]", function ()
    {
        var dateFormat = $(this).attr('data-format');
        $(this).datepicker({
            dateFormat: dateFormat,
            showAnim: "fadeIn"});
    });
});

/**
 * Creates new sub-form for rowset form field.
 *
 * @param {Object} parms
 */
function createRowSetRow(parms)
{
    var content = parms['content'];
    var name = parms['name'];
    var key = parms['key'];
    var rowset = $('#rowset-' + name);
    var removeText = rowset.attr('data-remove-text');
    var html = '<div class="sub-form" id="sub-form-' + name + '-' + key + '">';
    html += content;
    html += '<button class="btn btn-default rowset-remove" data-name="' + name + '" data-idx="' + key + '" type="button">';
    html += '<span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span> ' + removeText;
    html += '</button>';
    html += '</div>';
    rowset.children("button.rowset-add").before(html);
}