COLOR_CLASS = "red green blue yellow pink orange olive teal violet purple brown gray grey black"
COLOR_DEFAULT = 'gray';
COLOR = {
    red: "#B03060",
    orange: "#FE9A76",
    yellow: "#FFD700",
    olive: "#32CD32",
    green: "#016936",
    teal: "#008080",
    blue: "#0E6EB8",
    violet: "#EE82EE",
    purple: "#B413EC",
    pink: "#FF1493",
    brown: "#A52A2A",
    gray: "#A0A0A0",
    black: "#000000",
}
NO_CATEGORY_HTML = `<div id="no-category" class="inline field">
                        <div class="ui right pointing red basic label">
                            You haven\'t defined any category or tag, click "Add category" to add.</div>
                        </div>
                    </div>`;

function initDropdown() {
    $('.ui.dropdown').dropdown({
        onChange: function(value, text, object) {
            $(this).removeClass(COLOR_CLASS);
            $(this).addClass(value);
            $input = $(this).parent('.input').parent('.item').find('select[data-role=tagsinput]');
            $input.attr('color', value);
            initSelectTag($input, value, true);
        }
    });
}

function initSelectTag($intputTag, color, force) {
    if (force === true) {
        $intputTag.tagsinput('destroy');
    }
    $intputTag.tagsinput({
        tagClass: 'label label-info ui ' + color,

    });
}

function initSelectTags() {
    $inputTags = $('select[data-role=tagsinput]');
    for (var i = 0; i < $inputTags.length; i++) {
        $intputTag = $($inputTags[i]);
        initSelectTag($intputTag, $intputTag.attr('color'));
    }
}

function getTagStructure() {
    $.ajax(SERVICE_URL, {
            dataType: 'json'
        })
        .done(function(data) {
            $categories = $('div.ui.list');
            $categories.html('');
            for (var i = 0; i < data.data.length; i++) {
                $categories.append(htmlFromDoc(data.data[i]));
            }

            if (data.data.length == 0) {
                $categories.append(NO_CATEGORY_HTML);
            }

            initDropdown();
            initSelectTags();
            initDynamicListener();
        })
        .always(function() {
            $('#add-category').removeClass('loading');
        })
}

function htmlFromDoc(category) {
    if (!category) {
        category = {};
        category.category_color = COLOR_DEFAULT;
        category.category_name = "";
        category.tags = [];
    }

    tags_string = [];
    category.tags.forEach(function(item, i) {
        tags_string.push('<option value="' + item.tag_name + '">' + item.tag_id + '</option>');
    });

    html = `<div class="item tag-block">
                <div class="ui" style="float:right;"><i class="remove icon red remove-category"></i></div>
                <div class="category-id ui right labeled input" category-id="` + category.category_id + `">
                    <input class="category-name" placeholder="Category name" value="` + category.category_name + `" type="text">
                    <div class="ui dropdown label ` + category.category_color + `">
                        <div class="text">` + category.category_color + `</div>
                        <i class="dropdown icon"></i>
                        <div class="menu">
                            <div class="item">red</div>
                            <div class="item">orange</div>
                            <div class="item">yellow</div>
                            <div class="item">olive</div>
                            <div class="item">green</div>
                            <div class="item">teal</div>
                            <div class="item">blue</div>
                            <div class="item">violet</div>
                            <div class="item">purple</div>
                            <div class="item">pink</div>
                            <div class="item">brown</div>
                            <div class="item">gray</div>
                            <div class="item">black</div>
                        </div>
                    </div>
                </div>
                <div style="margin: 10px;"></div>
                <select multiple data-role="tagsinput" color="` + category.category_color + `">
                    ` + tags_string.join("\n") + `
                </select>
                <div style="margin: 20px;"></div>
            </div>`;
    return html;
}

function initListener() {
    $("#add-category").click(function() {
        $('#no-category').remove();
        $('div.ui.list').prepend(htmlFromDoc(false));
        initSelectTags();
        initDropdown();
        initDynamicListener();
    });

    $("#save-category").click(function() {
        save();
    });

    $("#cancel-category").click(function() {
        getTagStructure();
    });
}

function initDynamicListener() {
    $(".remove-category").click(function(e) {
        $(e.target.parentNode.parentNode).hide(400, function(e) {
            $(this).remove();
            $items = $('.ui.list .tag-block');
            if ($items.length == 0) {
                $categories = $('div.ui.list');
                $categories.append(NO_CATEGORY_HTML);
            }
        });
        $(e.target).remove();
    });
}

function dataToJson() {
    data = [];
    $items = $('.ui.list .tag-block');
    $items.each(function(i, e) {
        $e = $(e);
        categoryId = $e.find('.category-id').attr('category-id');
        categoryName = $e.find('.category-name').val();

        $select = $e.find('select');
        categoryColor = $select.attr('color');
        value = [];

        $select.val().forEach(function(tag_text) {
            $e = $select.find('option[value="' + tag_text + '"]');
            tag_value = $e.html();
            value.push({
                text: tag_text,
                value: tag_value,
            });
        });

        data.push({
            category_id: categoryId && categoryId != "undefined" ? categoryId : null,
            category_name: categoryName,
            category_color: categoryColor,
            data: value,
        });
    });

    return data;
}

function save() {
    data = dataToJson();

    $save = $('#save-category');
    $save.addClass('loading');
    $.ajax(SERVICE_URL_UPDATE, {
            dataType: 'json',
            type: 'post',
            data: {
                json_data: JSON.stringify(data),
            }
        })
        .done(function(data) {

        })
        .always(function() {
            $save.removeClass('loading');
            getTagStructure();
        })
}

getTagStructure();
initListener();