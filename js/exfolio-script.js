// ExFolio Plugin Script
jQuery(document).ready(function($) {
    $('.exfolio-toggle-collapse').on('click', function() {
        $(this).next('.exfolio-collapse-content').slideToggle();
    });
});
