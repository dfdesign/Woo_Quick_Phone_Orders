var buttonOptions = {
    defaultColor: jQuery('#button_color').val(),
    change: function(event, ui){
        var c = Color(ui.color._hsv);
        jQuery('#button_color').val(c.toString());
    }
};
var boxOptions = {
    defaultColor: jQuery('#box_color').val(),
    change: function(event, ui){
        var c = Color(ui.color._hsv);
        jQuery('#box_color').val(c.toString());
    }
};
jQuery(document).ready(function($){
    jQuery('.button_color').wpColorPicker(buttonOptions);
    jQuery('.box_color').wpColorPicker(boxOptions);
});
