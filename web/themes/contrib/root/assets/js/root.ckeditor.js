(function($, Drupal) {
  var isMaximized = false;
  Drupal.behaviors.ckeditorSelect = {
    attach: function(context) {
      // Only when direction is rtl.
      if ($("html").attr("dir") == "rtl") {
        CKEDITOR.on("instanceReady", function(event) {
          var instance = event.editor;
          instance.on("maximize", function(e) {
            isMaximized = e.data == 1 ? true : false;
          });
          $(".cke_combo_button").on("click", function() {
            // Don't run this code if the ckeditor is not maximized.
            if (!isMaximized) return;

            setTimeout(() => {
              var comboPanel = $('.cke_combopanel[role="presentation"]');
              var buttonLeft = $(this).offset().left;
              comboPanel.css("left", -$(".cke_inner").width() + buttonLeft);
            });
          });
        });
      }

      // CKEDITOR Autogrow.
      if (typeof CKEDITOR !== "undefined") {
        CKEDITOR.config.autoGrow_maxHeight = 0.8 * window.innerHeight;
      }
    }
  };
})(jQuery, Drupal);
