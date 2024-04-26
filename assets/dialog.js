(function ($) {
  // close dialogs on key "Escape"
  const $dialog = $('div.dialog.show')
  if ($dialog.length > 0) {
    $(document).on(
      'keydown',
      function (e) {
        if (e.key === "Escape") {
          $dialog.removeClass('show')
        }
      })
  }
})(jQuery);