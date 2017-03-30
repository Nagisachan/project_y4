COLOR_CLASS = "red green blue yellow pink orange olive teal violet purple brown grey black"
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
    grey: "#A0A0A0",
    black: "#000000",
}

function initDropdown() {
    $('.ui.dropdown').dropdown({
        onChange: function(value, text, object) {
            $(this).removeClass(COLOR_CLASS);
            $(this).addClass(value);
            $input = $(this).parent('.input').parent('.item').find('input[data-role=tagsinput]');
            $input.attr('color', value);
            initInputTag($input, value, true);
        }
    });
}

function initInputTag($intputTag, color, force) {
    if (force === true) {
        $intputTag.tagsinput('destroy');
    }
    $intputTag.tagsinput({
        tagClass: 'label label-info ui ' + color
    });
}

function initInputTags() {
    $inputTags = $('input[data-role=tagsinput]');
    for (var i = 0; i < $inputTags.length; i++) {
        $intputTag = $($inputTags[i]);
        initInputTag($intputTag, $intputTag.attr('color'));
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

            initDropdown();
            initInputTags();
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
        tags_string.push(item.tag_name);
    });

    html = `<div class="item">
                <div class="ui right labeled input">
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
                            <div class="item">grey</div>
                            <div class="item">black</div>
                        </div>
                    </div>
                </div>
                <div style="margin: 10px;"></div>
                <input type="text" value="` + tags_string.join(",") + `" data-role="tagsinput" color="` + category.category_color + `"/>
                <div style="margin: 20px;"></div>
            </div>`;
    return html;
}

function initListener() {
    $("#add-category").click(function() {
        $('div.ui.list').prepend(htmlFromDoc(false));
        initInputTags();
        initDropdown();
    });
}

getTagStructure();
initListener();