function Xeditable() {
    $('.editable').editable({
        emptytext: "Vide",
        params: function params(_params) {
            _params.model = $(this).attr('data-model');
            _params.hasone = $(this).attr('data-hasone');
            _params.foreignkey = $(this).attr('data-foreignkey');
            return _params;
        },
        showbuttons: 'bottom',
        onblur: 'ignore',
        url: '/ajax/quickupdate'
    });
}

if ($('.editable').length) {
    Xeditable();
}

$(document).on('click', '.showAllTrad', function () {
    $('.input-group').show();
    $('.showAllTrad').addClass('txt-color-red').addClass('forceTrad');
});

$(document).on('click', '.forceTrad', function () {
    block = $(this);

    var parent = block.parents('.input-group');
    var input = parent.find('.form-control');
    if (input.hasClass('summernote')) return false;
    var type = input.attr('data-type');

    var locale_id = parent.attr('data-locale_id');
    var field = input.attr('data-name');
    var content = input.val();
    block.find('i').addClass('fa-spin');
    if (content) {
        $.ajax({
            url: prefix_ajax + "ajax/forcetrad",
            dataType: 'json',
            data: {locale_id: locale_id, field: field, content: content},
            type: 'POST',
            success: function (data, statut) {
                $('.showAllTrad').find('i').removeClass('fa-spin');
                console.log(data);
                if (data.status) {
                    successS('Traduction complétée');
                    $.each(data.text, function (index, value) {
                        input = $(type + '[name="' + field + '[' + index + ']"]');
                        input.val(value);
                        input.addClass('border-bluemega')

                    });
                } else {
                    errorS('Erreur de traduction');
                }
            }
        });
    } else {
        errorS('Aucun texte à traduire')
    }
});