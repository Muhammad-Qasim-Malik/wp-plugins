jQuery(document).ready(function ($) {
  const $bar = $("#mqab-bar");
  if ($bar.length === 0) return;

  if ($bar.hasClass("mqab-top")) {
    $("body").addClass("has-mqab-top");
  } else {
    $("body").addClass("has-mqab-bottom");
  }

  const $dismiss = $bar.find(".mqab-dismiss");
  if ($dismiss.length) {
    if (localStorage.getItem("mqab-dismissed") === "1") {
      $bar.hide();
      $("body").removeClass("has-mqab-top has-mqab-bottom");
      return;
    }

    $dismiss.on("click", function () {
      $bar.hide();
      $("body").removeClass("has-mqab-top has-mqab-bottom");
      localStorage.setItem("mqab-dismissed", "1");
    });
  }
});
