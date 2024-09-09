

jQuery(document).ready(function($) {
    $('.exfolio-experience-title, .exfolio_collapse_toggle').on('click', function() {
        var $experienceItem = $(this).closest('.exfolio-experience-item');
        var $collapseContent = $experienceItem.find('.exfolio-collapse-content');
        var $iconDown = $experienceItem.find('.collapse-icon-down');
        var $iconUp = $experienceItem.find('.collapse-icon-up');

        // Toggle content visibility
        $collapseContent.slideToggle(300); 

        // Toggle icons
        $iconDown.toggle();
        $iconUp.toggle();
    });
});
