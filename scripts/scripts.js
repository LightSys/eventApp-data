function addFields(html, form) {
    $("#" + form).append(html);
    
}

$( "#save" ).click(function() {
    console.log("save");
    $( "#form" ).submit();
});