jQuery(document).ready(function($) {
    // Toggle collapse content
    $('.exfolio_collapse_toggle').on('click', function() {
        var $collapseContent = $(this).closest('.exfolio-experience-item').find('.exfolio-collapse-content');
        var $iconDown = $(this).find('.collapse-icon-down');
        var $iconUp = $(this).find('.collapse-icon-up');

        // Toggle content visibility
        $collapseContent.slideToggle(300); // Adjust animation speed as needed

        // Toggle icons
        $iconDown.toggle();
        $iconUp.toggle();
    });
});
