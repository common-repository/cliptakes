(function ($) {
  // #region Script Setup
  const ctadminPluginApiUrl =
    "https://europe-west2-cliptakes-api.cloudfunctions.net/plugin";
  const ctadminSettingsApiUrl =
    "https://europe-west2-cliptakes-api.cloudfunctions.net/settings";

  const ctadminData = { TemplateData: [] };
  ctadminData.SearchParams = new URLSearchParams(window.location.search);
  ctadminData.CurrentPage = ctadminData.SearchParams.get("page");
  ctadminData.NoActiveSubscriptionAlert =
    cliptakes_i18n.no_active_sub + "\n\n" + cliptakes_i18n.check_api_settings;
  ctadminData.CustomColumnIds = [];

  const ctadminTimelimitOptions = [0, 15, 30, 60, 90, 120];
  // #endregion Script Setup

  // #region General functions
  function ctadminGetTimelimitMinAndSec(timelimit) {
    const minutes = Math.floor(timelimit / 60);
    let seconds = timelimit % 60;
    if (seconds < 10) {
      seconds = `0${seconds}`;
    }
    return { minutes, seconds };
  }
  function ctadminGetFormattedDate(dateTimestamp, time = true) {
    const monthsShort = [
      "Jan",
      "Feb",
      "Mar",
      "Apr",
      "May",
      "Jun",
      "Jul",
      "Aug",
      "Sep",
      "Oct",
      "Nov",
      "Dec",
    ];
    const dateTime = new Date(dateTimestamp * 1000);
    // Format: j M Y H:i
    const date = dateTime.getDate();
    const month = monthsShort[dateTime.getMonth()];
    const year = dateTime.getFullYear();
    let hours = String(dateTime.getHours());
    if (hours.length < 2) hours = "0" + hours;
    let minutes = String(dateTime.getMinutes());
    if (minutes.length < 2) minutes = "0" + minutes;
    const result =
      `${date} ${month} ${year}` + (time ? ` ${hours}:${minutes}` : "");
    return result;
  }
  function ctadminGetTimeDifferenceInDays(dateTimestamp) {
    // const dateNow = new Date.now();
    const diffTime = Math.abs(Date.now() / 1000 - dateTimestamp);
    const diffDays = Math.floor(diffTime / (60 * 60 * 24));
    return diffDays;
  }

  async function ctadminGetAuthHeader(
    licenseKey,
    subscriptionId,
    endpoint,
    dateString
  ) {
    if (!window.crypto.subtle) {
      throw new Error(cliptakes_i18n.use_https_alert);
    }
    const enc = new TextEncoder("utf-8");
    const cryptoKey = await window.crypto.subtle.importKey(
      "raw",
      enc.encode(licenseKey),
      {
        name: "HMAC",
        hash: { name: "SHA-256" },
      },
      false,
      ["sign", "verify"]
    );
    const signature = await window.crypto.subtle.sign(
      "HMAC",
      cryptoKey,
      enc.encode("POST" + endpoint + dateString)
    );
    const digestBuffer = new Uint8Array(signature);
    const digest = Array.prototype.map
      .call(digestBuffer, (x) => ("00" + x.toString(16)).slice(-2))
      .join("");

    return subscriptionId + ":" + digest;
  }

  async function ctadminGetSubscriptionDetails() {
    if (ctadminData.SubscriptionDetails) {
      return ctadminData.SubscriptionDetails;
    }
    const authUrl = "/v1/getSubscriptionLimits";
    const dateString = new Date().toISOString();

    if (!cliptakes_ajax_obj.subscription.license_key) {
      return {};
    }

    const authHeader = await ctadminGetAuthHeader(
      cliptakes_ajax_obj.subscription.license_key,
      cliptakes_ajax_obj.subscription.subscription_id,
      authUrl,
      dateString
    );

    let subscriptionDetails = {};
    try {
      subscriptionDetails = await $.ajax({
        url: ctadminSettingsApiUrl + authUrl,
        type: "POST",
        dataType: "json",
        headers: {
          "Content-Type": "application/json",
          Timestamp: dateString,
          Authorization: authHeader,
        },
        data: JSON.stringify({
          subscriptionId: cliptakes_ajax_obj.subscription.subscription_id,
        }),
      });
    } catch (err) {}

    if (
      subscriptionDetails.product != cliptakes_ajax_obj.subscription.product
    ) {
      $.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        data: {
          _ajax_nonce: cliptakes_ajax_obj.nonce, //nonce
          action: "cliptakes_update_subscription_status",
        },
      });
    }
    ctadminData.SubscriptionDetails = subscriptionDetails;
    if (
      subscriptionDetails.usedMinutes >=
      (subscriptionDetails.limits
        ? subscriptionDetails.limits.recordingTime
        : 0)
    ) {
      $("#ctadmin-reached-recording-limit").html(
        `<p>${cliptakes_i18n.recording_limit_reached
          .replace("<MINUTES_LIMIT>", subscriptionDetails.limits.recordingTime)
          .replace(
            "<RESET_DATE>",
            ctadminGetFormattedDate(subscriptionDetails.resetDate, false)
          )}</p>
        <p>${cliptakes_i18n.upgrade_sub_increase_rec_limit}</p>
        <hr>`
      );
    }
    return ctadminData.SubscriptionDetails;
  }
  function ctadminSetCustomColumnIds(html) {
    const regex = /id=(["'])custom(\w+)\1/g;
    const columnIds = [];
    let match;
    while ((match = regex.exec(html)) !== null) {
      columnIds.push(match[2].toLowerCase());
    }
    ctadminData.CustomColumnIds = columnIds;
  }
  // #endregion General functions

  // #region Page: Create Free Account
  async function ctadminInitCreateAccountPage() {
    $("#ctadmin-create-account-show-password").change(function (event) {
      event.preventDefault();
      const passwordInput = document.getElementById(
        "ctadmin-create-account-password"
      );
      passwordInput.type =
        passwordInput.type == "password" ? "text" : "password";
    });

    $("#ctadmin-create-account-form").on("submit", function (event) {
      event.preventDefault();
      $("#ctadmin-create-account-loading")
        .addClass("ctadmin-loading-spinner")
        .show();
      const formData = Object.fromEntries(new FormData(event.target).entries());
      $.ajax({
        url: cliptakes_ajax_obj.ajax_url,
        type: "POST",
        dataType: "json",
        data: {
          _ajax_nonce: cliptakes_ajax_obj.nonce, //nonce
          action: "cliptakes_create_account",
          email: formData["email"],
          password: formData["password"],
        },
        success: function (response) {
          $("#ctadmin-create-account-loading").hide();
          if (response.success) {
            location.href = response.path;
            return;
          }
          alert(response.message);
        },
        error: function (xhr, ajaxOptions, thrownError) {
          $("#ctadmin-create-account-loading").hide();
          alert(xhr.responseJSON);
          console.log(xhr.responseJSON);
        },
      });
    });
  }
  // #endregion Page: Create Free Account

  // #region Page: Settings
  async function ctadminInitSettingsPage() {
    if (ctadminData.SearchParams.get("create-free-account")) {
      await ctadminInitCreateAccountPage();
      return;
    }

    if ($.isFunction(jQuery.fn.wpColorPicker)) {
      $("input.ctadmin-color-picker").wpColorPicker();
    }

    const subscriptionDetails = cliptakes_ajax_obj.subscription.status
      ? await ctadminGetSubscriptionDetails()
      : {};

    if (
      !subscriptionDetails.product ||
      subscriptionDetails.product.toLowerCase() == "free"
    ) {
      $("#ctadmin-logo-input").click(function (event) {
        event.preventDefault();
        if (
          !cliptakes_ajax_obj.subscription ||
          cliptakes_ajax_obj.subscription.length == 0
        ) {
          return alert(ctadminData.NoActiveSubscriptionAlert);
        }
        alert(
          cliptakes_i18n.upgrade_sub_custom_logo +
            " " +
            cliptakes_i18n.pro_only_feature
        );
      });
    }
    $("#ctadmin-logo-input")
      .prop("disabled", false)
      .parent()
      .removeClass("button-disabled");

    $("#ctadmin-logo-input").change(function (event) {
      event.preventDefault();
      let logoUrl = URL.createObjectURL(event.target.files[0]);
      $("#ctadmin-logo-preview").attr("src", logoUrl);
      let fileName = event.target.files[0].name;
      let fileExtension = fileName.substring(fileName.lastIndexOf(".") + 1);
      $("#ctadmin-logo-url").val(fileExtension);
    });

    $("#ctadmin-reset-intro").click(function (event) {
      event.preventDefault();
      let confirmMessage = cliptakes_i18n.reset_intro;
      let action = "cliptakes_reset_intro_html";
      let editorId = "intro_html_markup";
      ctadminResetInterviewPart(confirmMessage, action, editorId);
    });

    $("#ctadmin-reset-signup").click(function (event) {
      event.preventDefault();
      let confirmMessage = cliptakes_i18n.reset_sign_up;
      let action = "cliptakes_reset_signup_html";
      let editorId = "signup_html_markup";
      ctadminResetInterviewPart(confirmMessage, action, editorId);
    });

    $("#ctadmin-reset-upload-before").click(function (event) {
      event.preventDefault();
      let confirmMessage = cliptakes_i18n.reset_ready_for_upload;
      let action = "cliptakes_reset_upload_before_html";
      let editorId = "upload_before_html_markup";
      ctadminResetInterviewPart(confirmMessage, action, editorId);
    });

    $("#ctadmin-reset-upload-after").click(function (event) {
      event.preventDefault();
      let confirmMessage = cliptakes_i18n.reset_upload_success;
      let action = "cliptakes_reset_upload_after_html";
      let editorId = "upload_after_html_markup";
      ctadminResetInterviewPart(confirmMessage, action, editorId);
    });
  }
  function ctadminResetInterviewPart(confirmMessage, action, editorId) {
    if (confirm(confirmMessage)) {
      $.post(
        cliptakes_ajax_obj.ajax_url,
        {
          //POST request
          _ajax_nonce: cliptakes_ajax_obj.nonce, //nonce
          action, //action
        },
        function (data) {
          //callback
          var introEditor = tinyMCE.get(editorId);
          if (introEditor) {
            introEditor.setContent(data);
            return;
          }
          jQuery(`#${editorId}`).html(data);
        }
      );
    }
  }
  // #endregion Page: Settings

  // #region Page: Templates
  async function ctadminInitTemplatesPage() {
    if (cliptakes_ajax_obj.subscription.status) {
      ctadminGetTemplates();
    }

    const subscriptionDetails = cliptakes_ajax_obj.subscription.status
      ? await ctadminGetSubscriptionDetails()
      : {};

    if (
      !subscriptionDetails.product ||
      subscriptionDetails.product.toLowerCase() == "free"
    ) {
      $("#ctadmin-template-editor").on(
        "mousedown",
        ".ctadmin-question-timelimit",
        function (event) {
          event.preventDefault();
          if (
            !cliptakes_ajax_obj.subscription ||
            cliptakes_ajax_obj.subscription.length == 0
          ) {
            return alert(ctadminData.NoActiveSubscriptionAlert);
          }
          alert(
            cliptakes_i18n.upgrade_sub_timelimit +
              " " +
              cliptakes_i18n.pro_only_feature
          );
        }
      );
    }

    $("#ctadmin-template-editor").on(
      "change",
      ".ctadmin-question-timelimit",
      function (event) {
        event.preventDefault();
        const targetElement = $(event.target);
        const parentElement = targetElement.closest("div");
        const newValue = targetElement.val();
        if (newValue == 0) {
          parentElement
            .find(".ctadmin-question-timelimit-input-section")
            .addClass("ctadmin-hidden");
          return;
        }
        parentElement
          .find(".ctadmin-question-timelimit-input-section")
          .removeClass("ctadmin-hidden");
        const { minutes, seconds } = ctadminGetTimelimitMinAndSec(newValue);
        parentElement
          .find("input.ctadmin-question-timelimit-minutes")
          .val(minutes);
        parentElement
          .find("input.ctadmin-question-timelimit-seconds")
          .val(seconds);
      }
    );
    $("#ctadmin-template-editor").on(
      "change",
      ".ctadmin-question-timelimit-input",
      function (event) {
        event.preventDefault();
        const targetElement = $(event.target);
        const parentElement = targetElement.closest("div");
        let newValue = Math.max(0, parseInt(targetElement.val()));
        if (
          targetElement.hasClass("ctadmin-question-timelimit-seconds") &&
          newValue > 59
        ) {
          newValue = 59;
        }
        targetElement.val(newValue);
        const minutes = parseInt(
          parentElement.find("input.ctadmin-question-timelimit-minutes").val()
        );
        const seconds = parseInt(
          parentElement.find("input.ctadmin-question-timelimit-seconds").val()
        );
        const newTimelimit = minutes * 60 + seconds;
        const customOption = parentElement
          .closest(".ctadmin-question-options")
          .find("option[ctadmin-is-custom-timelimit]");
        if (newTimelimit == 0) {
          parentElement.addClass("ctadmin-hidden");
        } else {
          customOption.val(
            ctadminTimelimitOptions.includes(newTimelimit) ? 150 : newTimelimit
          );
        }
        parentElement
          .closest(".ctadmin-question-options")
          .find(".ctadmin-question-timelimit")
          .val(newTimelimit);
      }
    );

    $("#ctadmin-template-select").on("change", (event) =>
      ctadminSetTemplate(event.target.value)
    );

    $("#ctadmin-add-template-button").click(function (event) {
      event.preventDefault();
      ctadminAddNewTemplate();
    });

    $("#ctadmin-copy-shortcode-button").click(function (event) {
      event.preventDefault();
      navigator.clipboard
        .writeText($("#ctadmin-template-shortcode").val())
        .then(function () {
          $("#ctadmin-template-shortcode")
            .addClass("ctadmin-green-overlay")
            .delay(800)
            .queue(function (next) {
              $("#ctadmin-template-shortcode").removeClass(
                "ctadmin-green-overlay"
              );
              next();
            });
        });
    });

    $("#ctadmin-add-question-button").click(async function (event) {
      event.preventDefault();
      const subscriptionDetails = cliptakes_ajax_obj.subscription.status
        ? await ctadminGetSubscriptionDetails()
        : {};
      if (
        $(".ctadmin-question").length >=
        (subscriptionDetails.limits ? subscriptionDetails.limits.questions : 3)
      ) {
        return alert(
          cliptakes_ajax_obj.subscription.status
            ? cliptakes_i18n.question_limit_reached
                .replace("<PRODUCT>", subscriptionDetails.product)
                .replace(
                  "<QUESTION_LIMIT>",
                  subscriptionDetails.limits.questions
                ) +
                "\n\n" +
                cliptakes_i18n.upgrade_sub_questions
            : cliptakes_i18n.no_active_sub +
                ".\n\n" +
                cliptakes_i18n.no_sub_question_limit
        );
      }
      $("#ctadmin-template-questions").append(
        ctadminGetQuestionMarkup(`new-${Date.now()}`, "", "")
      );
    });

    $("#ctadmin-template-editor").on(
      "click",
      ".ctadmin-delete-question",
      async function (event) {
        event.preventDefault();
        let id = $(this).data("id");
        $(`#${id}`).remove();
      }
    );

    $("#ctadmin-save-template-button").click(function (event) {
      event.preventDefault();
      if (!cliptakes_ajax_obj.subscription.status) {
        return alert(ctadminData.NoActiveSubscriptionAlert);
      }
      ctadminUpdateTemplate();
    });

    $("#ctadmin-delete-template-button").click(async function (event) {
      event.preventDefault();
      ctadminDeleteTemplate();
    });
  }
  function ctadminSetupTemplatesDropdown() {
    $("#ctadmin-template-select").html(
      "<option selected disabled hidden>" +
        cliptakes_i18n.select_interview_template +
        "</option>"
    );
    for (let i = 0; i < ctadminData.TemplateData.length; i++) {
      $("#ctadmin-template-select").append(
        `<option value="${i}" data-id="${
          ctadminData.TemplateData[i].templateId
        }"${i >= ctadminData.MaxTemplates ? "disabled" : ""}>${
          ctadminData.TemplateData[i].name
        }</option>`
      );
    }
  }
  async function ctadminGetTemplates() {
    const getTemplatesUrl = "/v1/getTemplates";
    const dateString = new Date().toISOString();
    const authHeader = await ctadminGetAuthHeader(
      cliptakes_ajax_obj.subscription.license_key,
      cliptakes_ajax_obj.subscription.subscription_id,
      getTemplatesUrl,
      dateString
    );
    $.ajax({
      url: ctadminSettingsApiUrl + getTemplatesUrl,
      type: "POST",
      dataType: "json",
      headers: {
        "Content-Type": "application/json",
        Timestamp: dateString,
        Authorization: authHeader,
      },
      data: JSON.stringify({
        subscriptionId: cliptakes_ajax_obj.subscription.subscription_id,
      }),
      success: function (response) {
        ctadminData.TemplateData = response.templateData;
        ctadminSetupTemplatesDropdown();
        $("#ctadmin-template-loading-indicator").remove();
      },
    });
  }
  function ctadminGetQuestionMarkup(questionId, text, timelimit) {
    const { minutes, seconds } = ctadminGetTimelimitMinAndSec(timelimit);
    const isCustomTimelimit = !ctadminTimelimitOptions.includes(
      parseInt(timelimit ? timelimit : 0)
    );
    const timelimit_option_markup = ctadminTimelimitOptions
      .map((option) => {
        return `<option value="${option}"${
          option == timelimit ? " selected" : ""
        }>\
        ${
          option <= 0 ? cliptakes_i18n.timelimit_none : option.toString() + "s"
        }</option>`;
      })
      .join();
    return `<div id="${questionId}" class="ctadmin-question">
      <textarea rows="3" maxlength="500">${text}</textarea>
      <div class="ctadmin-question-options">
        <div>
          <label for="ctadmin-question-timelimit">${
            cliptakes_i18n.timelimit
          }</label>
          <select class="ctadmin-question-timelimit">
            ${timelimit_option_markup}
            <option value="${isCustomTimelimit ? timelimit : 150}"
            ${
              isCustomTimelimit ? " selected" : ""
            } ctadmin-is-custom-timelimit>${
      cliptakes_i18n.timelimit_custom
    }</option>
          </select>
          <div class="ctadmin-question-timelimit-input-section${
            timelimit <= 0 ? " ctadmin-hidden" : ""
          }">
          <input class="ctadmin-question-timelimit-input ctadmin-question-timelimit-minutes" \
          type="number" min="0" value="${minutes}"><label>${
      cliptakes_i18n.timelimit_minutes
    }</label> 
          <input class="ctadmin-question-timelimit-input ctadmin-question-timelimit-seconds" \
          type="number" min="0" max="59" value="${seconds}"><label>${
      cliptakes_i18n.timelimit_seconds
    }</label>
          </div>
        </div>
        <button
          class="ctadmin-delete-question button ctadmin-button ctadmin-button-red"
          data-id="${questionId}"
        >${cliptakes_i18n.remove_question}</button>
      </div>
    </div>`;
  }
  function ctadminSetTemplate(id) {
    $("#ctadmin-template-name").val(ctadminData.TemplateData[id].name);
    let questionsHtml = "";
    ctadminData.TemplateData[id].questions.forEach((question) => {
      questionsHtml += ctadminGetQuestionMarkup(
        question.questionId,
        question.text,
        question.timelimit ?? ""
      );
    });
    $("#ctadmin-template-questions").html(questionsHtml);
    $("#ctadmin-template-first").val(ctadminData.TemplateData[id].first);
    $("#ctadmin-template-last").val(ctadminData.TemplateData[id].last);
    if (!ctadminData.TemplateData[id].templateId) {
      $("#ctadmin-template-shortcode-section").hide();
    } else {
      $("#ctadmin-template-shortcode-section").show();
      $("#ctadmin-template-shortcode").val(
        `[cliptakes_interview templateId="${ctadminData.TemplateData[id].templateId}"]`
      );
    }
    $("#ctadmin-template-editor").show("slow");
    $("#ctadmin-add-question-button").prop(
      "disabled",
      $(".ctadmin-question").length >= ctadminData.MaxQuestions
    );
  }
  async function ctadminAddNewTemplate() {
    const templateCount = ctadminData.TemplateData.length;
    const subscriptionDetails = cliptakes_ajax_obj.subscription.status
      ? await ctadminGetSubscriptionDetails()
      : {};
    if (
      templateCount == 0 ||
      templateCount < subscriptionDetails.limits.templates
    ) {
      ctadminData.TemplateData[templateCount] = {
        id: `new-${Date.now()}`,
        name: cliptakes_i18n.new_template,
        questions: [
          {
            questionId: `new-${Date.now()}`,
            text: cliptakes_i18n.enter_question_here,
          },
        ],
        first: cliptakes_i18n.default_first_slide + ":\n[FirstName] [LastName]",
        last: cliptakes_i18n.default_last_slide,
      };
      $("#ctadmin-template-select").append(
        `<option value="${templateCount}" data-id="new-${Date.now()}">${
          ctadminData.TemplateData[templateCount].name
        }</option>`
      );
      $("#ctadmin-template-select").val(templateCount).trigger("change");
    } else {
      return alert(
        cliptakes_i18n.template_limit_reached
          .replace("<PRODUCT>", subscriptionDetails.product)
          .replace("<TEMPLATES_LIMIT>", subscriptionDetails.limits.templates)
          .replace(
            "<N_TEMPLATES>",
            subscriptionDetails.limits.templates > 1
              ? cliptakes_i18n.templates_plural
              : cliptakes_i18n.template_singular
          ) +
          "\n\n" +
          cliptakes_i18n.upgrade_sub_templates
      );
    }
  }
  async function ctadminUpdateTemplate() {
    let questions = [];
    $(".ctadmin-question").each((index, element) => {
      questions.push({
        questionId: element.id,
        text: $(`#${element.id}`).children("textarea").val(),
        timelimit: $(`#${element.id} .ctadmin-question-timelimit`)[0].value,
      });
    });

    const updateTemplateUrl = "/v1/updateTemplate";
    const dateString = new Date().toISOString();
    const authHeader = await ctadminGetAuthHeader(
      cliptakes_ajax_obj.subscription.license_key,
      cliptakes_ajax_obj.subscription.subscription_id,
      updateTemplateUrl,
      dateString
    );

    $.ajax({
      url: ctadminSettingsApiUrl + updateTemplateUrl,
      type: "POST",
      dataType: "json",
      headers: {
        "Content-Type": "application/json",
        Timestamp: dateString,
        Authorization: authHeader,
      },
      data: JSON.stringify({
        subscriptionId: cliptakes_ajax_obj.subscription.subscription_id,
        template: {
          templateId: $("#ctadmin-template-select")
            .children(":selected")
            .data("id"),
          name: $("#ctadmin-template-name").val(),
          questions,
          first: $("#ctadmin-template-first").val(),
          last: $("#ctadmin-template-last").val(),
        },
      }),
      success: function (response) {
        let selected = $("#ctadmin-template-select").val();
        ctadminData.TemplateData[selected].templateId =
          response.template.templateId;
        ctadminData.TemplateData[selected].name = $(
          "#ctadmin-template-name"
        ).val();
        ctadminData.TemplateData[selected].questions = questions;
        ctadminData.TemplateData[selected].first = $(
          "#ctadmin-template-first"
        ).val();
        ctadminData.TemplateData[selected].last = $(
          "#ctadmin-template-last"
        ).val();
        ctadminSetupTemplatesDropdown();
        $("#ctadmin-template-select").val(selected).trigger("change");
        $("#ctadmin-save-template-success")
          .show("slow")
          .delay(1000)
          .hide("slow");
      },
    });
  }
  async function ctadminDeleteTemplate() {
    if (confirm(cliptakes_i18n.confirm_delete_template)) {
      const deleteTemplateUrl = "/v1/deleteTemplate";
      const dateString = new Date().toISOString();
      const authHeader = await ctadminGetAuthHeader(
        cliptakes_ajax_obj.subscription.license_key,
        cliptakes_ajax_obj.subscription.subscription_id,
        deleteTemplateUrl,
        dateString
      );
      $.ajax({
        url: ctadminSettingsApiUrl + deleteTemplateUrl,
        type: "POST",
        dataType: "json",
        headers: {
          "Content-Type": "application/json",
          Timestamp: dateString,
          Authorization: authHeader,
        },
        data: JSON.stringify({
          subscriptionId: cliptakes_ajax_obj.subscription.subscription_id,
          templateId: $("#ctadmin-template-select")
            .children(":selected")
            .data("id"),
        }),
        success: function (response) {
          ctadminData.TemplateData.splice(
            $("#ctadmin-template-select").val(),
            1
          );
          ctadminSetupTemplatesDropdown();
          if (ctadminData.TemplateData.length == 0) {
            $("#ctadmin-template-editor").hide("fast");
          } else {
            $("#ctadmin-template-select").val(0).trigger("change");
          }
        },
      });
    }
  }
  // #endregion Page: Templates

  // #region Page: Contacts
  async function ctadminInitContactsPage() {
    if (!cliptakes_ajax_obj.subscription.status) {
      $("#ctadmin-add-contact-button").click(function (event) {
        event.preventDefault();
        return alert(ctadminData.NoActiveSubscriptionAlert);
      });
      return;
    }

    const subscriptionDetails = await ctadminGetSubscriptionDetails();
    if (subscriptionDetails.limits.contacts == 0) {
      $("#ctadmin-contact-list").hide();
      $("#ctadmin-contact-list-no-items").hide();
      $("#ctadmin-add-contact-button").click(function (event) {
        event.preventDefault();
        alert(
          cliptakes_i18n.upgrade_sub_add_contacts +
            " " +
            cliptakes_i18n.pro_only_feature
        );
      });
      return;
    }
    ctadminLoadContactList();
    $("#ctadmin-add-contact-button").click(async function (event) {
      event.preventDefault();
      const contactCount = Object.keys(ctadminData.Contacts).length;
      const subscriptionDetails = await ctadminGetSubscriptionDetails();
      if (contactCount >= subscriptionDetails.limits.contacts) {
        return alert(
          cliptakes_i18n.contacts_limit_reached
            .replace("<PRODUCT>", subscriptionDetails.product)
            .replace("<CONTACTS_LIMIT>", subscriptionDetails.limits.contacts) +
            "\n\n" +
            cliptakes_i18n.upgrade_sub_more_contacts
        );
      }
      if (!$("#ctadmin-contact-info").hasClass("ctadmin-hidden")) {
        if (ctadminData.currentContactId != "new-contact") {
          if (!confirm(cliptakes_i18n.dismiss_contact_changes)) {
            return;
          }
        }
      }
      ctadminData.currentContactId = "new-contact";
      ctadminShowContactInfo();
    });
    $("#ctadmin-contacts").on(
      "click",
      ".ctadmin-edit-contact-button",
      function (event) {
        event.preventDefault();
        if (!$("#ctadmin-contact-info").hasClass("ctadmin-hidden")) {
          if (ctadminData.currentContactId != "new-contact") {
            if (!confirm(cliptakes_i18n.dismiss_contact_changes)) {
              return;
            }
          }
        }
        const contactId = $(this).closest("tr").data("contactId");
        ctadminData.currentContactId = contactId;
        ctadminShowContactInfo(ctadminData.Contacts[contactId]);
      }
    );
    $("#ctadmin-contacts").on(
      "click",
      ".ctadmin-delete-contact-button",
      function (event) {
        event.preventDefault();
        if (!confirm(cliptakes_i18n.confirm_delete_contact)) {
          return;
        }
        const contactId = $(this).closest("tr").data("contactId");
        ctadminDeleteContact(contactId);
      }
    );
    $("#ctadmin-contact-cancel-button").click(function (event) {
      event.preventDefault();
      ctadminData.currentContactId = null;
      $("#ctadmin-contact-info").addClass("ctadmin-hidden");
    });
    $("#ctadmin-contacts").on("keypress", "input", function (event) {
      if (event.which == 13) {
        event.preventDefault();
        ctadminSaveContactInfo();
      }
    });
    $("#ctadmin-contact-save-button").click(async function (event) {
      event.preventDefault();
      ctadminSaveContactInfo();
    });
    $("#ctadmin-contacts").on(
      "click",
      ".ctadmin-copy-url-param-button",
      function (event) {
        event.preventDefault();
        const contactId = $(this).closest("tr").data("contactId");
        const cell = $(this).closest("td");
        navigator.clipboard
          .writeText(`?contact=${ctadminData.Contacts[contactId].handle}`)
          .then(function () {
            cell
              .addClass("ctadmin-green-overlay")
              .delay(1000)
              .queue(function (next) {
                cell.removeClass("ctadmin-green-overlay");
                next();
              });
          });
      }
    );
  }
  async function ctadminLoadContactList() {
    const getContactsUrl = "/v1/getContacts";
    const dateString = new Date().toISOString();
    const authHeader = await ctadminGetAuthHeader(
      cliptakes_ajax_obj.subscription.license_key,
      cliptakes_ajax_obj.subscription.subscription_id,
      getContactsUrl,
      dateString
    );
    $.ajax({
      url: ctadminSettingsApiUrl + getContactsUrl,
      type: "POST",
      dataType: "json",
      headers: {
        "Content-Type": "application/json",
        Timestamp: dateString,
        Authorization: authHeader,
      },
      data: JSON.stringify({
        subscriptionId: cliptakes_ajax_obj.subscription.subscription_id,
      }),
      success: function (response) {
        if (response.contacts.length == 0) {
          ctadminData.Contacts = {};
          $("#ctadmin-contact-list-no-items").show();
          $("#ctadmin-contact-list-no-items").removeClass(
            "ctadmin-loading-spinner"
          );
        } else {
          ctadminData.Contacts = Object.assign(
            {},
            ...response.contacts.map((contact) => ({
              [contact.contactId]: {
                name: contact.name,
                handle: contact.handle,
                email: contact.email,
              },
            }))
          );
          ctadminUpdateContactList();
        }
      },
      error: function (xhr, ajaxOptions, thrownError) {
        $("#ctadmin-contact-list-no-items").html(
          `<p>${cliptakes_i18n.load_contacts_error}</p>`
        );
        $("#ctadmin-contact-list-no-items").removeClass(
          "ctadmin-loading-spinner"
        );
        return;
      },
    });
  }
  function ctadminUpdateContactList() {
    let sortedContacts = [];
    for (contactId in ctadminData.Contacts) {
      const contact = ctadminData.Contacts[contactId];
      sortedContacts.push({
        contactId,
        name: contact.name,
        handle: contact.handle,
        email: contact.email,
      });
    }
    sortedContacts.sort((a, b) =>
      a.name.localeCompare(b.name, undefined, { sensitivity: "base" })
    );
    let tableMarkup = "";
    sortedContacts.forEach((contact) => {
      tableMarkup += `<tr data-contact-id="${contact.contactId}">
      <td>${contact.name}
        <div class="ctadmin-contact-list-actions">
          <a class="ctadmin-edit-contact-button">${cliptakes_i18n.edit}</a> |
          <a class="ctadmin-delete-contact-button">${cliptakes_i18n.delete}</a>
        </div>
      </td>
      <td>${contact.handle}</td>
      <td>${contact.email}</td>
      <td><i>?contact=${contact.handle}</i>
        <div class="ctadmin-contact-list-actions">
          <a class="ctadmin-copy-url-param-button">${cliptakes_i18n.copy}</a>
        </div>
      </td>
      </tr>`;
    });
    $("#ctadmin-contact-list tbody").html(tableMarkup);
    $("#ctadmin-contact-list-no-items").hide();
  }
  function ctadminShowContactInfo(
    contact = { name: "", handle: "", email: "" }
  ) {
    $("#ctadmin-contact-info-name").val(contact.name || "");
    $("#ctadmin-contact-info-handle").val(contact.handle || "");
    $("#ctadmin-contact-info-email").val(contact.email || "");
    $("#ctadmin-contact-info").removeClass("ctadmin-hidden");
  }
  async function ctadminSaveContactInfo() {
    let contact = {
      name: $("#ctadmin-contact-info-name").val(),
      handle: $("#ctadmin-contact-info-handle").val(),
      email: $("#ctadmin-contact-info-email").val(),
    };
    let contactRequest = {
      contactId: ctadminData.currentContactId,
      ...contact,
    };
    if (!ctadminValidateContactInput(contactRequest)) {
      return;
    }

    const updateResult = await ctadminUpdateContact(contactRequest);
    ctadminData.currentContactId = updateResult.contactId;
    ctadminData.Contacts[updateResult.contactId] = contact;
    ctadminUpdateContactList();

    $("#ctadmin-contact-saved-message")
      .show()
      .delay(800)
      .queue(function (next) {
        $("#ctadmin-contact-saved-message").hide("slow");
        next();
      });
    $("#ctadmin-contact-info").addClass("ctadmin-hidden");
  }
  function ctadminValidateContactInput(contact) {
    let valid = true;
    let nameInput = document.getElementById("ctadmin-contact-info-name");
    let handleInput = document.getElementById("ctadmin-contact-info-handle");
    let emailInput = document.getElementById("ctadmin-contact-info-email");

    nameInput.setCustomValidity("");
    handleInput.setCustomValidity("");
    emailInput.setCustomValidity("");

    if (!contact.name) {
      valid = false;
      nameInput.setCustomValidity(cliptakes_i18n.contact_name_missing);
    } else if (contact.name.match("[^A-Za-z0-9 ]")) {
      valid = false;
      nameInput.setCustomValidity(cliptakes_i18n.contact_name_error);
    }
    if (!contact.handle) {
      valid = false;
      handleInput.setCustomValidity(cliptakes_i18n.contact_handle_missing);
    } else if (contact.handle.match("[^A-Za-z0-9_.-]")) {
      valid = false;
      handleInput.setCustomValidity(cliptakes_i18n.contact_handle_error);
    } else {
      for (contactId in ctadminData.Contacts) {
        if (
          contactId != contact.contactId &&
          contact.handle.localeCompare(
            ctadminData.Contacts[contactId].handle,
            undefined,
            { sensitivity: "base" }
          ) == 0
        ) {
          valid = false;
          handleInput.setCustomValidity(cliptakes_i18n.contact_handle_taken);
        }
      }
    }
    if (!contact.email.match("^[^\\s@]+@([^\\s@.,]+\\.)+[^\\s@.,]{2,}$")) {
      valid = false;
      emailInput.setCustomValidity(cliptakes_i18n.contact_email_error);
    }
    if (!valid) document.forms[0].reportValidity();
    return valid;
  }
  async function ctadminUpdateContact(contact) {
    const updateContactUrl = "/v1/updateContact";
    const dateString = new Date().toISOString();
    const authHeader = await ctadminGetAuthHeader(
      cliptakes_ajax_obj.subscription.license_key,
      cliptakes_ajax_obj.subscription.subscription_id,
      updateContactUrl,
      dateString
    );
    return $.ajax({
      url: ctadminSettingsApiUrl + updateContactUrl,
      type: "POST",
      dataType: "json",
      headers: {
        "Content-Type": "application/json",
        Timestamp: dateString,
        Authorization: authHeader,
      },
      data: JSON.stringify({
        subscriptionId: cliptakes_ajax_obj.subscription.subscription_id,
        contact,
      }),
    });
  }
  async function ctadminDeleteContact(contactId) {
    const deleteContactUrl = "/v1/deleteContact";
    const dateString = new Date().toISOString();
    const authHeader = await ctadminGetAuthHeader(
      cliptakes_ajax_obj.subscription.license_key,
      cliptakes_ajax_obj.subscription.subscription_id,
      deleteContactUrl,
      dateString
    );
    $.ajax({
      url: ctadminSettingsApiUrl + deleteContactUrl,
      type: "POST",
      dataType: "json",
      headers: {
        "Content-Type": "application/json",
        Timestamp: dateString,
        Authorization: authHeader,
      },
      data: JSON.stringify({
        subscriptionId: cliptakes_ajax_obj.subscription.subscription_id,
        contactId,
      }),
      success: function (response) {
        delete ctadminData.Contacts[contactId];
        ctadminUpdateContactList();
      },
      error: function (xhr, ajaxOptions, thrownError) {
        alert(
          cliptakes_i18n.delete_contact_error +
            "\n" +
            cliptakes_i18n.please_refresh
        );
      },
    });
  }
  // #endregion Page: Contacts

  // #region Page: Interviews
  function ctadminInitInterviewsPage() {
    ctadminData.InterviewListParams = {
      paged: "1",
      order: "desc",
      orderby: "recorded",
      search: null,
    };
    if (!cliptakes_ajax_obj.subscription.status) {
      return;
    }
    ctadminGetRecordingStatistics();
    $("#ctadmin-interview-list-loading")
      .addClass("ctadmin-loading-spinner")
      .show();
    ctadminDisplayInterviewList();

    $("#ctadmin-interview-search-submit").on("click", function (event) {
      event.preventDefault();
      ctadminData.InterviewListParams.search = $(
        "#ctadmin-interview-search"
      ).val();
      ctadminUpdateInterviewList();
    });
  }
  async function ctadminGetRecordingStatistics() {
    const getRecordingStatisticsUrl = "/v1/getRecordingStatistics";
    const dateString = new Date().toISOString();
    const authHeader = await ctadminGetAuthHeader(
      cliptakes_ajax_obj.subscription.license_key,
      cliptakes_ajax_obj.subscription.subscription_id,
      getRecordingStatisticsUrl,
      dateString
    );
    $.ajax({
      url: ctadminSettingsApiUrl + getRecordingStatisticsUrl,
      type: "POST",
      dataType: "json",
      headers: {
        "Content-Type": "application/json",
        Timestamp: dateString,
        Authorization: authHeader,
      },
      data: JSON.stringify({
        subscriptionId: cliptakes_ajax_obj.subscription.subscription_id,
      }),
      success: function (response) {
        const remaining = Math.max(0, response.limit - response.used);
        const markup = `<b>${
          cliptakes_i18n.recording_time
        }: </b> ${cliptakes_i18n.recording_statistics
          .replace("<REMAINING>", remaining)
          .replace("<LIMIT>", response.limit)}<br>
        <b>${cliptakes_i18n.resets_on}: </b> ${ctadminGetFormattedDate(
          response.resetDate,
          false
        )}`;
        $("#ctadmin-recording-statistics").html(markup).show();
      },
      error: function (xhr, ajaxOptions, thrownError) {
        alert(
          cliptakes_i18n.load_statistics_error +
            "\n" +
            cliptakes_i18n.please_refresh
        );
      },
    });
  }
  function ctadminDisplayInterviewList() {
    $.ajax({
      url: ajaxurl,
      type: "POST",
      dataType: "json",
      data: {
        _ajax_nonce: cliptakes_ajax_obj.nonce, //nonce
        action: "cliptakes_interview_data_display",
      },
      success: function (response) {
        $("#ctadmin-interview-list-table").html(response.display);
        ctadminGetInterviews();
        $("#ctadmin-interview-list-table").on(
          "click",
          ".ctadmin-embed-link",
          function (event) {
            event.preventDefault();
            let embedLink = `<iframe src="https://api.cliptakes.com/embed/${$(
              this
            ).data(
              "link"
            )}" width="960" height="540" frameborder="0" allowfullscreen></iframe>`;
            navigator.clipboard.writeText(embedLink).then(function () {
              $(event.target)
                .addClass("ctadmin-copied-message")
                .delay(1000)
                .queue(function (next) {
                  $(event.target).removeClass("ctadmin-copied-message");
                  next();
                });
            });
          }
        );

        $("#ctadmin-interview-list-table").on(
          "click",
          ".ctadmin-embed-new-page-link",
          function (event) {
            event.preventDefault();
            const pageDetails = $(event.target).data();
            $.ajax({
              url: ajaxurl,
              type: "POST",
              dataType: "json",
              data: {
                _ajax_nonce: cliptakes_ajax_obj.nonce, //nonce
                action: "cliptakes_create_embed_page",
                link: pageDetails.link,
                first_name: pageDetails.firstName,
                last_name: pageDetails.lastName,
              },
              success: function (response) {
                window.open(response.link, "_blank").focus();
              },
            });
          }
        );
        $("#ctadmin-interview-list-table").on(
          "click",
          ".ctadmin-delete-interview-link",
          async function (event) {
            event.preventDefault();
            if (!confirm(cliptakes_i18n.confirm_delete_interview)) {
              return;
            }

            const deleteInterviewUrl = "/v1/deleteInterview";
            const dateString = new Date().toISOString();
            const authHeader = await ctadminGetAuthHeader(
              cliptakes_ajax_obj.subscription.license_key,
              cliptakes_ajax_obj.subscription.subscription_id,
              deleteInterviewUrl,
              dateString
            );
            const interviewId = $(event.target).data().interviewId;
            $.ajax({
              url: ctadminSettingsApiUrl + deleteInterviewUrl,
              type: "POST",
              dataType: "json",
              headers: {
                "Content-Type": "application/json",
                Timestamp: dateString,
                Authorization: authHeader,
              },
              data: JSON.stringify({
                subscriptionId: cliptakes_ajax_obj.subscription.subscription_id,
                interviewId,
              }),
              success: function (response) {
                ctadminData.Interviews = ctadminData.Interviews.filter(
                  (interview) => {
                    return interview.interviewId != interviewId;
                  }
                );
                ctadminUpdateInterviewList();
              },
              error: function (xhr, ajaxOptions, thrownError) {
                alert(
                  cliptakes_i18n.delete_interview_error +
                    "\n" +
                    cliptakes_i18n.please_refresh
                );
              },
            });
          }
        );
      },
    });
  }
  async function ctadminGetInterviews() {
    const getInterviewsUrl = "/v1/getInterviews";
    const dateString = new Date().toISOString();
    const authHeader = await ctadminGetAuthHeader(
      cliptakes_ajax_obj.subscription.license_key,
      cliptakes_ajax_obj.subscription.subscription_id,
      getInterviewsUrl,
      dateString
    );
    $.ajax({
      url: ctadminSettingsApiUrl + getInterviewsUrl,
      type: "POST",
      dataType: "json",
      headers: {
        "Content-Type": "application/json",
        Timestamp: dateString,
        Authorization: authHeader,
      },
      data: JSON.stringify({
        subscriptionId: cliptakes_ajax_obj.subscription.subscription_id,
      }),
      success: function (response) {
        ctadminData.Interviews = response.interviews || [];
        ctadminUpdateInterviewList();
        $("#ctadmin-interview-list-loading").hide();
      },
      error: function (xhr, ajaxOptions, thrownError) {
        $("#ctadmin-interview-list-loading").html(
          `<p>${cliptakes_i18n.load_interviews_error}</p>`
        );
        $("#ctadmin-interview-list-loading").removeClass(
          "ctadmin-loading-spinner"
        );
        return;
      },
    });
  }
  function ctadminInitInterviewList() {
    var timer;
    var delay = 800;
    $("td.column-recorded").each(function (index, tdElement) {
      const dateTimestamp = parseInt($(tdElement).html());
      const dateString = ctadminGetFormattedDate(dateTimestamp);
      const diffDays = ctadminGetTimeDifferenceInDays(dateTimestamp);
      const expiresInString = `<div class="row-actions"><p>${cliptakes_i18n.expiry_message.replace(
        "<DAYS_UNTIL_DELETION>",
        180 - diffDays
      )}</p></div>`;
      $(this).html(dateString + expiresInString);
    });
    $(
      ".tablenav-pages a, .manage-column.sortable a, .manage-column.sorted a"
    ).on("click", function (event) {
      event.preventDefault();
      let linkUrl = new URL(this.getAttribute("href"));
      var linkParams = new URLSearchParams(linkUrl.search);
      ctadminData.InterviewListParams.paged = linkParams.get("paged") || "1";
      ctadminData.InterviewListParams.order =
        linkParams.get("order") || ctadminData.InterviewListParams.order;
      ctadminData.InterviewListParams.orderby =
        linkParams.get("orderby") || ctadminData.InterviewListParams.orderby;
      ctadminUpdateInterviewList();
    });

    const ctadminSetPageIndex = function () {
      ctadminData.InterviewListParams.paged =
        parseInt($("input[name=paged]").val()) || "1";
      ctadminUpdateInterviewList();
    };
    $("input[name=paged]").on("keyup", function (event) {
      window.clearTimeout(timer);

      if (13 == event.which) {
        event.preventDefault();
        return ctadminSetPageIndex();
      }

      timer = window.setTimeout(ctadminSetPageIndex, delay);
    });

    $("#ctadmin-interview-list").on("submit", function (event) {
      event.preventDefault();
    });

    $("#ctadmin-interview-list-table").show();
  }
  function ctadminGetCustomColumnContents(customInfo) {
    let customColumnContents = "";
    for (let columnId of ctadminData.CustomColumnIds.values()) {
      customColumnContents += customInfo[columnId] ?? "";
    }
    return customColumnContents;
  }
  function ctadminUpdateInterviewList() {
    const ITEMS_PER_PAGE = 15;
    const filteredInterviews = !ctadminData.InterviewListParams.search
      ? ctadminData.Interviews
      : ctadminData.Interviews.filter((interview) => {
          const customColumnContents = interview.customInfo
            ? ctadminGetCustomColumnContents(interview.customInfo)
            : "";
          const searchableInfo = String(
            interview.firstName +
              interview.lastName +
              interview.contact +
              interview.template +
              customColumnContents
          ).toLowerCase();
          return searchableInfo.includes(
            ctadminData.InterviewListParams.search.toLowerCase()
          );
        });
    filteredInterviews.sort((a, b) => {
      const aValue = a[ctadminData.InterviewListParams.orderby].toString();
      const bValue = b[ctadminData.InterviewListParams.orderby].toString();
      return (
        aValue.localeCompare(bValue, undefined, { sensitivity: "accent" }) *
        (ctadminData.InterviewListParams.order == "asc" ? 1 : -1)
      );
    });
    const maxPageNumber = Math.ceil(filteredInterviews.length / ITEMS_PER_PAGE);
    ctadminData.InterviewListParams.paged =
      ctadminData.InterviewListParams.paged < 1
        ? 1
        : ctadminData.InterviewListParams.paged > maxPageNumber
        ? maxPageNumber
        : ctadminData.InterviewListParams.paged;
    const firstShownInterviewIdx =
      (ctadminData.InterviewListParams.paged - 1) * ITEMS_PER_PAGE;
    const shownInterviews = filteredInterviews.slice(
      firstShownInterviewIdx,
      firstShownInterviewIdx + ITEMS_PER_PAGE
    );
    $("#ctadmin-interview-list-loading")
      .addClass("ctadmin-loading-spinner")
      .show();
    $.ajax({
      url: ajaxurl,
      type: "POST",
      data: $.extend(
        {
          _ajax_nonce: cliptakes_ajax_obj.nonce, //nonce
          action: "cliptakes_fetch_interview_data",
          interview_items: shownInterviews,
          total_items: filteredInterviews.length,
          items_per_page: ITEMS_PER_PAGE,
        },
        ctadminData.InterviewListParams
      ),
      success: function (response) {
        var response = JSON.parse(response);
        ctadminSetCustomColumnIds(response.column_headers);
        $("#the-list").html(response.rows);
        $("thead tr, tfoot tr").html(response.column_headers);
        $(".tablenav.top").html(response.pagination.top);
        $(".tablenav.bottom").html(response.pagination.bottom);
        ctadminInitInterviewList();
        $("#ctadmin-interview-list-loading").hide();
      },
      error: function (xhr, ajaxOptions, thrownError) {
        $("#ctadmin-interview-list-loading").html(
          `<p>${cliptakes_i18n.load_interviews_error}</p>`
        );
        $("#ctadmin-interview-list-loading").removeClass(
          "ctadmin-loading-spinner"
        );
      },
    });
  }
  // #endregion Page: Interviews

  // #region Page: API Settings
  function ctadminInitApiSettingsPage() {
    $("#ctadmin-check-subscription").click(function (event) {
      event.preventDefault();
      ctadminCheckSubscription();
    });
  }
  async function ctadminCheckSubscription() {
    $("#ctadmin-check-subscription-error").hide("fast");

    const authUrl = "/v1/authorize";
    const dateString = new Date().toISOString();

    const licenseKeyValue = $("#ctadmin-license_key").val().replace("#", "");
    const subscriptionIdValue = $("#ctadmin-subscription_id")
      .val()
      .replace("#", "");

    const authHeader = await ctadminGetAuthHeader(
      licenseKeyValue,
      subscriptionIdValue,
      authUrl,
      dateString
    );

    $.ajax({
      url: ctadminPluginApiUrl + authUrl,
      type: "POST",
      dataType: "json",
      headers: {
        "Content-Type": "application/json",
        Timestamp: dateString,
        Authorization: authHeader,
      },
      data: JSON.stringify({
        subscriptionId: subscriptionIdValue,
      }),
      success: function (response) {
        $("#ctadmin-api-connection-status").addClass(
          "ctadmin-subscription-checked"
        );
        $("#ctadmin-check-subscription-response").html(response.message);
        $("#ctadmin-check-subscription-response")
          .show("slow")
          .delay(5000)
          .hide("slow");
        if (
          licenseKeyValue == cliptakes_ajax_obj.subscription.license_key &&
          subscriptionIdValue == cliptakes_ajax_obj.subscription.subscription_id
        ) {
          $.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            data: {
              _ajax_nonce: cliptakes_ajax_obj.nonce, //nonce
              action: "cliptakes_update_subscription_status",
            },
          });
        } else {
          $("#ctadmin-check-subscription-response").append(
            `<p>${cliptakes_i18n.save_changes_alert}</p>`
          );
        }
      },
      error: function (xhr, ajaxOptions, thrownError) {
        $("#ctadmin-api-connection-status").removeClass(
          "ctadmin-subscription-checked"
        );
        let message =
          xhr.responseJSON.message == "Authentication failed"
            ? `<p>${cliptakes_i18n.authentication_error}.</p>
        <p>${cliptakes_i18n.check_license_and_key}</p>`
            : xhr.responseJSON.message;
        $("#ctadmin-check-subscription-error").html(message);
        $("#ctadmin-check-subscription-error").show("slow");
      },
    });
  }
  // #endregion Page: API Settings

  // #region Window Load
  $(window).load(async function () {
    await ctadminGetSubscriptionDetails();
    if (ctadminData.CurrentPage == "cliptakes-settings") {
      ctadminInitSettingsPage();
    } else if (ctadminData.CurrentPage == "cliptakes-templates") {
      ctadminInitTemplatesPage();
    } else if (ctadminData.CurrentPage == "cliptakes-contacts") {
      ctadminInitContactsPage();
    } else if (ctadminData.CurrentPage == "cliptakes-interviews") {
      ctadminInitInterviewsPage();
    } else if (ctadminData.CurrentPage == "cliptakes-api-settings") {
      ctadminInitApiSettingsPage();
    }
  });
  // #endregion Window Load
})(jQuery);
