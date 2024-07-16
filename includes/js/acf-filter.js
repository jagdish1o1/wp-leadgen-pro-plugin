jQuery(document).ready(function ($) {


    if (typeof acf !== 'undefined') {

        let countryFieldId = 'field_667e94c14d805';
        let stateFieldId = 'field_667e8a1fe4615';
        let cityFieldId = 'field_667e8a7ce4617';

        acf.addAction('load', function () {
            var post_id = acf.get('post_id');
            var data = {
                action: 'data_fetch',
                'post_id': post_id
            };
            $.post(ajaxurl, data).done(function (result) {
                var fields = [
                    { id: countryFieldId, key: 'country' },
                    { id: stateFieldId, key: 'state' },
                    { id: cityFieldId, key: 'cities' }
                ];

                // console.log(result);
                fields.forEach(function (field) {
                    if (result[field.key]) {
                        var acfField = acf.getField(field.id);
                        if (Array.isArray(result[field.key])) {
                            result[field.key].forEach(function (item) {
                                acfField.select2.addOption({
                                    id: item,
                                    tag: true,
                                    text: item,
                                    selected: true
                                });
                            });
                        } else {
                            acfField.select2.addOption({
                                id: result[field.key],
                                text: result[field.key],
                                selected: true
                            });
                        }
                        acfField.$el.trigger('change');
                    }
                });
            });
        });

        acf.add_filter('select2_ajax_data', (data) => {

            if (data.field_key === stateFieldId) { // state field id 
                country = acf.getField(countryFieldId).val();
                data.selected_country = country.split(';')[1];
            }

            if (data.field_key === cityFieldId) { // cities field id 
                country = acf.getField(countryFieldId).val();
                data.selected_country = country.split(';')[1];

                state = acf.getField(stateFieldId).val();
                data.selected_state = state.split(';')[1];
            }

            return data;
        });
    } else {
        console.log('acf js error');
    }
});