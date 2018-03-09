function addFields(html, form) {
    $("#" + form).append(html);
    
}

$( "#save" ).click(function() {
    $( "#form" ).submit();
});