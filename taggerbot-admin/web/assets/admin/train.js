SELECTED_TAG = null;

function init() {
    $('.train.dropdown').dropdown({
        on: 'hover',
        onChange: function(a, b, e) {
            $('#choose-tag-first').hide();
            tag = $(e).attr('tag');
            SELECTED_TAG = tag;
            getTagParagraph(tag);
        },
    });
}

function getTagParagraph(tagId) {
    $('#train-tag').addClass('loading');
    $.ajax(SERVICE_URL.replace('TAGID', tagId), {
            dataType: 'json',
        })
        .done(function(data) {
            $('#train-tag').removeClass('loading');
            $('tbody').html('');
            data.data.forEach(function(e) {
                $('tbody').append(
                    `<tr>
                        <td class="collapsing">
                            <div class="ui fitted slider checkbox" fid="` + e.file_id + `" pid="` + e.paragraph_id + `">
                                <input type="checkbox"> <label></label>
                            </div>
                        </td>
                        <td class="single line">` + e.file_name + `</td>
                        <td>` + e.content + `</td>
                        <td>` + e.tags + `</td>
                    </tr>`
                );
            });

            if (data.data.length == 0) {
                $('tbody').append(
                    `<tr>
                        <td class="collapsing">
                            <div class="ui fitted slider checkbox input disabled">
                                <input type="checkbox"> <label></label>
                            </div>
                        </td>
                        <td colspan="10" style="text-align: center">
                            <div class="ui pointing red basic label large">
                                No associated paragraph.
                            </div>
                        </td>
                    </tr>`
                );
            }

            initCheckbox();
        });
}

function selectAll() {
    $('.checkbox').checkbox('set checked');
    $('.select-all').addClass('disabled');
    $('.deselect-all').removeClass('disabled');
}

function deselectAll() {
    $('.checkbox').checkbox('set unchecked');
    $('.select-all').removeClass('disabled');
    $('.deselect-all').addClass('disabled');
}

function clearSelectAll() {
    $('.select-all').removeClass('disabled');
    $('.deselect-all').removeClass('disabled');
}

function initCheckbox() {
    $('.checkbox').checkbox({
        onChecked: function() {
            clearSelectAll();
        },
        onUnchecked: function() {
            clearSelectAll();
        }
    });
}

function train() {
    paragraphIds = [];
    $('#train-tag').addClass('loading');
    $('table.table .checkbox.checked').each(function(i, e) {
        $e = $(e);
        paragraphId = $e.attr('fid') + '-' + $(e).attr('pid');
        paragraphIds.push(paragraphId);
    });

    $.ajax(SERVICE_TRAIN_URL.replace('TAGID', SELECTED_TAG), {
            dataType: 'json',
            method: 'post',
            data: {
                paragraph_ids: paragraphIds,
            }
        })
        .done(function(data) {
            console.log(data);
        })
        .always(function() {
            $('#train-tag').removeClass('loading');
        })
}

init();