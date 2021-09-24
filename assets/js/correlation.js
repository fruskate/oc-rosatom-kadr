$(document).ready(function(){
    $('#dates').datepicker({
        onSelect: function (formattedDate, date) {
            $('#setDate').val(formattedDate);
        }
    })
});

