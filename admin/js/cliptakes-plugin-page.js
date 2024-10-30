(function ($) {
  let ctadminSendFeedbackTries = 0;
  let ctadminDeactivationReasonId = false;
  const ctadminDeactivationReasons = [
    {
      id: 1,
      text: cliptakes_i18n.technical_difficulties,
      hasInput: true,
      inputPlaceholder: cliptakes_i18n.what_went_wrong,
    },
    {
      id: 2,
      text: cliptakes_i18n.not_what_expected,
      hasInput: true,
      inputPlaceholder: cliptakes_i18n.what_did_you_expect,
    },
    {
      id: 3,
      text: cliptakes_i18n.better_plugin,
      hasInput: true,
      inputPlaceholder: cliptakes_i18n.what_plugin_name,
    },
    {
      id: 4,
      text: cliptakes_i18n.too_expensive,
      hasInput: true,
      inputPlaceholder: cliptakes_i18n.what_price_expected,
    },
    {
      id: 99,
      text: cliptakes_i18n.other,
      hasInput: true,
      inputPlaceholder: cliptakes_i18n.what_can_we_improve,
    },
  ];

  const ctadminDeactivationFeedbackDialogueHtml = `
<div id="ctadmin-deactivation-feedback">
  <div class="ctadmin-dialogue">
    <div class="ctadmin-dialogue-header">
      <h4>${cliptakes_i18n.quick_feedback}</h4>
    </div>
    <div class="ctadmin-dialogue-body">
      <h3>
        <strong>${cliptakes_i18n.why_deactivating}</strong>
      </h3>
      <ul id="ctadmin-deactivation-reasons-list">
      </ul>
      <h4>${cliptakes_i18n.want_a_solution}</h4>
      <input id="ctadmin-deactivation-email-input" type="text" maxlength="256" placeholder="${cliptakes_i18n.your_email}">
    </div>
    <div class="ctadmin-dialogue-footer">
      <label class="anonymous-feedback-label" style="display: none"
        ><input type="checkbox" class="anonymous-feedback-checkbox" />${cliptakes_i18n.anonymous}</label
      >
      <a href="#" class="button button-secondary ctadmin-btn-deactivate-plugin">
      ${cliptakes_i18n.skip} &amp; ${cliptakes_i18n.deactivate}
      </a>
      <a href="#" class="button button-secondary ctadmin-btn-close-dialogue">${cliptakes_i18n.cancel}</a>
    </div>
  </div>
</div>`;

  function ctadminGetDeactivationReasonHtml(reason) {
    let liClassList = `ctadmin-deactivation-reason${
      reason.hasInput ? " has-input" : ""
    }`;
    let liData = reason.hasInput
      ? ` data-input-placeholder="${reason.inputPlaceholder}"`
      : "";
    let html = `
<li class="${liClassList}"${liData}>
  <label>
    <span><input type="radio" name="selected-reason" value="${reason.id}" /></span>
    <span>${reason.text}</span>
  </label>
</li>`;
    return html;
  }

  function ctadminShowFeedbackDialogue() {
    $("#ctadmin-deactivation-feedback").addClass("active");
    $("body").addClass("ctadmin-dialogue-active");
  }
  function ctadminResetFeedbackDialogue() {
    ctadminDeactivationReasonId = false;
    $("#ctadmin-deactivation-feedback")
      .find('input[type="radio"]')
      .prop("checked", false);
    $("#ctadmin-deactivation-feedback")
      .find(".ctadmin-deactivation-reason-input")
      .remove();
  }
  function ctadminCloseFeedbackDialogue() {
    $("body").removeClass("ctadmin-dialogue-active");
    $("#ctadmin-deactivation-feedback").removeClass("active");
  }

  function ctadminEnableDeactivationButton() {
    $(
      "#ctadmin-deactivation-feedback .ctadmin-btn-deactivate-plugin"
    ).removeClass("disabled");
  }
  function ctadminDisableDeactivationButton() {
    $("#ctadmin-deactivation-feedback .ctadmin-btn-deactivate-plugin").addClass(
      "disabled"
    );
  }

  function ctadminSendFeedback() {
    let textInput = $("#ctadmin-deactivation-feedback")
      .find(".ctadmin-deactivation-reason-input input")
      .val();

    let emailInput = $("#ctadmin-deactivation-feedback")
      .find("#ctadmin-deactivation-email-input")
      .val();

    $.ajax({
      url: ajaxurl,
      type: "POST",
      dataType: "json",
      data: {
        _ajax_nonce: cliptakes_ajax_obj.nonce, //nonce
        action: "cliptakes_send_deactivation_feedback",
        reason: ctadminDeactivationReasonId
          ? ctadminDeactivationReasons.filter(
              (reason) => reason.id == ctadminDeactivationReasonId
            )[0].text
          : "",
        additional_info: textInput,
        email: emailInput,
      },
      success: function (response) {
        ctadminSendFeedbackTries++;
        if (response.success || ctadminSendFeedbackTries >= 3) {
          location.href = ctadminDeactivatePluginLink;
        } else {
          console.log("Submitting Feedback failed. Trying again.");
          ctadminSendFeedback();
        }
      },
      error: function (xhr, status, error) {
        var errorMessage = xhr.status + ": " + xhr.statusText;
        console.error("Error - " + errorMessage);
      },
    });
  }

  $(window).load(() => {
    $ctadminDialogue = $(ctadminDeactivationFeedbackDialogueHtml);
    $ctadminDialogue.appendTo($("body"));
    for (let i = 0; i < ctadminDeactivationReasons.length; i++) {
      let reasonHtml = ctadminGetDeactivationReasonHtml(
        ctadminDeactivationReasons[i]
      );
      $("#ctadmin-deactivation-reasons-list").append(reasonHtml);
    }

    $(".deactivate > #deactivate-cliptakes").on("click", function (event) {
      event.preventDefault();
      ctadminDeactivatePluginLink = $(this).attr("href");
      ctadminResetFeedbackDialogue();
      ctadminShowFeedbackDialogue();
    });
    $("#ctadmin-deactivation-feedback").on("click", function (event) {
      let $target = $(event.target);
      if ($target.parents(".ctadmin-dialogue").length > 0) {
        return;
      }
      ctadminCloseFeedbackDialogue();
    });
    $("#ctadmin-deactivation-feedback .ctadmin-btn-deactivate-plugin").on(
      "click",
      function (event) {
        event.preventDefault();
        if ($(event.target).hasClass("disabled")) {
          $inputDiv = $("#ctadmin-deactivation-feedback").find(
            ".ctadmin-deactivation-reason-input"
          );
          $inputDiv.find("input").attr("required", true);
          if ($inputDiv.find(".ctadmin-required-message").length == 0) {
            $inputDiv.append(
              `<span class="ctadmin-required-message">${cliptakes_i18n.required}</span>`
            );
          }
          return;
        }
        ctadminCloseFeedbackDialogue();
        ctadminSendFeedback();
      }
    );

    $("#ctadmin-deactivation-feedback .ctadmin-btn-close-dialogue").on(
      "click",
      function (event) {
        event.preventDefault();
        ctadminCloseFeedbackDialogue();
      }
    );

    $("#ctadmin-deactivation-feedback").on(
      "click",
      'input[type="radio"]',
      function () {
        var $selectedReasonOption = $(this);
        // Return if selection didn't change
        if (ctadminDeactivationReasonId === $selectedReasonOption.val()) return;

        ctadminDeactivationReasonId = $selectedReasonOption.val();

        var _parent = $(this).parents("li:first");
        $("#ctadmin-deactivation-feedback")
          .find(".ctadmin-deactivation-reason-input")
          .remove();
        $("#ctadmin-deactivation-feedback")
          .find(".ctadmin-btn-deactivate-plugin")
          .html(cliptakes_i18n.submit + " &amp; " + cliptakes_i18n.deactivate);

        if (_parent.hasClass("has-input")) {
          let inputPlaceholder = _parent.data("input-placeholder");
          let reasonInputHtml = `<div class="ctadmin-deactivation-reason-input">
               <input type="text" maxlength="256" placeholder="${inputPlaceholder}">
             </div>`;

          $reasonInput = $(reasonInputHtml);
          _parent.append($reasonInput);
          $reasonInput.focus();

          if (ctadminDeactivationReasonId == 99) {
            ctadminDisableDeactivationButton();
          } else ctadminEnableDeactivationButton();
        }
      }
    );
    $("#ctadmin-deactivation-feedback").on(
      "input",
      '.ctadmin-deactivation-reason-input input[type="text"]',
      function () {
        $inputDiv = $("#ctadmin-deactivation-feedback").find(
          ".ctadmin-deactivation-reason-input"
        );
        if ($inputDiv.find("input").val().length > 0) {
          $inputDiv.find("input").attr("required", false);
          $inputDiv.find(".ctadmin-required-message").remove();
          ctadminEnableDeactivationButton();
        } else if (ctadminDeactivationReasonId == 99) {
          $inputDiv.find("input").attr("required", true);
          ctadminDisableDeactivationButton();
        }
      }
    );
  });
})(jQuery);
