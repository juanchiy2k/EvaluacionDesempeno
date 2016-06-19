/*$.extend({ alert: function (message, title) {
  $("<div></div>").dialog( {
    buttons: { "Ok": function () { $(this).dialog("close"); } },
    close: function (event, ui) { $(this).remove(); },
    resizable: false,
    title: title,
    modal: true
   
    
  }).text(message);
}
});
$.alert('message', 'title');*/

/*
window.alert = function(message){
    $(document.createElement('div'))
        .attr({title: 'Alert', 'class': 'alert'})
        .html(message)
        .dialog({
            buttons: {OK: function(){$(this).dialog('close');}},
            close: function(){$(this).remove();},
            draggable: true,
            modal: true,
            resizable: false,
            width: 'auto'
        });
};*/

