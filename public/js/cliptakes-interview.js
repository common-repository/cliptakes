// #region Script Setup
// ====================
// #region General
const ctivClassNames = {
  hidden: "ctiv-hidden",
  navDone: "ctiv-nav-done",
  navActive: "ctiv-nav-active",
  navSpacer: "ctiv-nav-spacer",
  longNav: "ctiv-long-nav",
};
const ctivApiUrl =
  "https://europe-west2-cliptakes-api.cloudfunctions.net/plugin";
const ctivUserInfo = {
  subscriptionId: cliptakes_subscription_info.subscription_id,
  licenseKey: cliptakes_subscription_info.license_key,
};
const ctivContactId = new URLSearchParams(window.location.search).get(
  "contact"
);
const ctivIsMobileBrowser = /mobi/i.test(navigator.userAgent);
// #endregion
// #region Sections
const ctivAllSections = document.querySelectorAll(".ctiv-section");
const ctivIntroSection = document.getElementById("ctiv-intro-section");
const ctivSignUpForm = document.getElementById("ctiv-signup-form");
const ctivNavSection = document.getElementById("ctiv-nav-section");
const ctivSetupSection = document.getElementById("ctiv-setup-section");
const ctivQuestionSection = document.getElementById("ctiv-question-section");
const ctivUploadSection = document.getElementById("ctiv-upload-section");
// #endregion
// #region Intro
const ctivIntroNextBtn = document.getElementById("ctiv-intro-next");
const ctivIntroAuthorizationSpinner =
  document.getElementById("ctiv-auth-spinner");
const ctivIntroAuthorizationError = document.getElementById(
  "ctiv-intro-authorization-error"
);
let ctivSubscriptionAuthorized;
ctivAuthorizeSubscription();
// #endregion
// #region SignUp
const ctivInterviewInfo = {
  interviewId: null,
  firstName: "",
  lastName: "",
  userAgent: navigator.userAgent,
  customInfo: [],
};
if (document.getElementById("ctiv-main-container").dataset.sendCandidateMail)
  ctivInterviewInfo.sendCandidateMail = true;

// #endregion
// #region Navigation
const ctivNavigationText = document.getElementById("ctiv-nav-text-heading");
const ctivNavigationBar = document.getElementById("ctiv-nav-bar");
const ctivAllNavigationItems = document.querySelectorAll("#ctiv-nav-bar div");
const ctivNavigationBackBtn = document.getElementById("ctiv-nav-back");
const ctivNavigationNextBtn = document.getElementById("ctiv-nav-next");
let ctivNoCameraMode = false;
let ctivNavigationCurrent = -1;
// #endregion
// #region Setup
const ctivSetup = {
  MediaError: document.getElementById("ctiv-setup-media-error"),
  NextNoCameraBtn: document.getElementById("ctiv-setup-next-no-camera"),
  VideoContainer: document.getElementById("ctiv-setup-video-container"),
  Video: document.getElementById("ctiv-setup-video"),
  NextBtn: document.getElementById("ctiv-setup-next"),
  CameraBtn: document.getElementById("ctiv-setup-camera-button"),
  CameraOptions: document.getElementById("ctiv-setup-camera-options"),
  MicrophoneBtn: document.getElementById("ctiv-setup-microphone-button"),
  MicrophoneOptions: document.getElementById("ctiv-setup-microphone-options"),
  AudioError: document.getElementById("ctiv-setup-audio-error"),
};
// #endregion
// #region Questions
const ctivQuestionText = document.getElementById("ctiv-question-text");
// #region No Cam
const ctivQuestionNoCam = {
  Section: document.getElementById("ctiv-question-nocam-section"),
  AnsweredState: document.getElementById("ctiv-question-answered-state"),
  VideoContainer: document.getElementById(
    "ctiv-question-nocam-video-container"
  ),
  Video: document.getElementById("ctiv-question-nocam-video"),
  Seek: document.getElementById("ctiv-question-nocam-seek"),
  Progress: document.getElementById("ctiv-question-nocam-video-progress"),
  RotateDevice: document.getElementById("ctiv-rotate-device-note"),
  FileInput: document.getElementById("ctiv-question-nocam-file-input"),
};
// #endregion No Cam
const ctivQuestionVideoContainer = document.getElementById(
  "ctiv-question-video-container"
);
const ctivQuestionVideo = document.getElementById("ctiv-question-video");
// #region Recording
const ctivQuestionRecording = {
  Overlay: document.getElementById("ctiv-question-recording-overlay"),
  StartBtn: document.getElementById("ctiv-question-start-recording"),
  StopBtn: document.getElementById("ctiv-question-stop-recording"),
  Indicator: document.getElementById("ctiv-question-recording-indicator"),
};
const ctivQuestionTimer = {
  Container: document.getElementById("ctiv-question-timer"),
  TimePassedPath: null,
  Label: null,
  Interval: null,
};
// #endregion Recording
// #region Editing
const ctivQuestionNavButtons = document.getElementById(
  "ctiv-question-nav-buttons"
);
const ctivQuestionRetakeBtn = document.getElementById("ctiv-question-retake");
const ctivQuestionNextBtn = document.getElementById("ctiv-question-next");
const ctivQuestionEditing = {
  Overlay: document.getElementById("ctiv-question-editing-overlay"),
  Seek: document.getElementById("ctiv-question-seek"),
  SeekIndex: document.getElementById("ctiv-question-seek-index"),
  Start: document.getElementById("ctiv-question-start"),
  End: document.getElementById("ctiv-question-end"),
  StartBackground: document.getElementById("ctiv-question-start-background"),
  EndBackground: document.getElementById("ctiv-question-end-background"),
  TrimmedFrame: document.getElementById("ctiv-question-trimmed-frame"),
};
const ctivQuestionEditingControls = {
  TogglePlayBtn: document.getElementById("ctiv-question-toggle-play"),
  PlayIcon: document.getElementById("ctiv-question-play-icon"),
  PlayIcon: document.getElementById("ctiv-question-play-icon"),
  PauseIcon: document.getElementById("ctiv-question-pause-icon"),
  TimeElapsed: document.getElementById("ctiv-question-time-elapsed"),
  Duration: document.getElementById("ctiv-question-duration"),
  ToggleMuteBtn: document.getElementById("ctiv-question-toggle-mute"),
  Volume: document.getElementById("ctiv-question-volume"),
  VolumeMuteIcon: document.getElementById("ctiv-question-volume-mute-icon"),
  VolumeLowIcon: document.getElementById("ctiv-question-volume-low-icon"),
  VolumeHighIcon: document.getElementById("ctiv-question-volume-high-icon"),
  ToggleFullscreenBtn: document.getElementById(
    "ctiv-question-toggle-fullscreen"
  ),
  FullscreenIcon: document.getElementById("ctiv-question-fullscreen-icon"),
  ExitFullscreenIcon: document.getElementById(
    "ctiv-question-exit-fullscreen-icon"
  ),
};

// #endregion Editing
const ctivQuestionDetails = cliptakes_interview_questions;
if (ctivQuestionDetails.length > 7)
  ctivNavSection.classList.add(ctivClassNames.longNav);
const ctivQuestionVideoModes = {
  playback: "playback",
  stream: "stream",
};
const ctivGetFileFormat = () => {
  try {
    if (!MediaRecorder.isTypeSupported) {
      console.log("MediaRecorder is not supported. No camera access.");
      return "mp4";
    }
    return MediaRecorder.isTypeSupported("video/mp4") ? "mp4" : "webm";
  } catch (err) {
    return "mp4";
  }
};
const ctivQuestionFileFormat = ctivGetFileFormat();
let ctivQuestionRecordingChunks = [];
let ctivQuestionVideoDuration = -1;
// #endregion
// #region Upload
const ctivUploadBtn = document.getElementById("ctiv-upload-button");
const ctivUpload = {
  Before: document.getElementById("ctiv-upload-before"),
  Waiting: document.getElementById("ctiv-upload-waiting"),
  After: document.getElementById("ctiv-upload-after"),
  Error: document.getElementById("ctiv-upload-error"),
  ErrorMessage: document.getElementById("ctiv-upload-error-message"),
};
// #endregion
// =======================
// #endregion Script Setup

// #region Functions
// =================
// #region UI-Visibility
function ctivShow(input) {
  if (input.forEach) {
    input.forEach((element) => element.classList.remove(ctivClassNames.hidden));
    return;
  }
  input.classList.remove(ctivClassNames.hidden);
}
function ctivHide(input) {
  if (input.forEach) {
    input.forEach((element) => element.classList.add(ctivClassNames.hidden));
    return;
  }
  input.classList.add(ctivClassNames.hidden);
}
function ctivScrollToTop() {
  if (!ctivIsMobileBrowser) return;
  let pageHeader = document.getElementsByTagName("header")[0];
  if (!pageHeader) {
    pageHeader = document.getElementsByClassName("header")[0];
  }
  if (!pageHeader) return;
  const headerHeight = pageHeader.offsetHeight;
  document.body.scrollTop = document.documentElement.scrollTop = headerHeight;
}
function ctivShowSection(section) {
  ctivHide(ctivAllSections);
  ctivShow(section);
  ctivScrollToTop();
}
//#endregion

// #region Navigation
function ctivWarnBeforeLeavingPage(event) {
  event.preventDefault();
  event.returnValue = cliptakes_i18n.confirm_leave_page;
}
function ctivStartSignUp() {
  if (ctivSubscriptionAuthorized == null) {
    ctivShow(ctivIntroAuthorizationSpinner);
    setTimeout(ctivStartSignUp, 500);
    return;
  }
  if (!ctivSubscriptionAuthorized) {
    ctivShow(ctivIntroAuthorizationError);
    return;
  }
  ctivHide(ctivIntroSection);
  ctivShow([ctivSignUpForm]);
  ctivScrollToTop();
}
async function ctivSubmitSignUp(event) {
  event.preventDefault();
  // Warn user if they leave the page without completing the interview
  window.addEventListener("beforeunload", ctivWarnBeforeLeavingPage);
  if (ctivSignUpForm.firstname)
    ctivInterviewInfo.firstName = ctivSignUpForm.firstname.value;
  if (ctivSignUpForm.lastname)
    ctivInterviewInfo.lastName = ctivSignUpForm.lastname.value;
  if (ctivSignUpForm.email)
    ctivInterviewInfo.email = ctivSignUpForm.email.value;

  let customInputs = Array.from(
    ctivSignUpForm.getElementsByClassName("ctiv-custom-input")
  );
  if (customInputs.length == 0) {
    delete ctivInterviewInfo.customInfo;
  } else {
    customInputs.forEach((input) => {
      ctivInterviewInfo.customInfo.push({
        label: input.name,
        value: input.value,
      });
    });
  }

  await ctivCheckCameraAccess();
  ctivShow(ctivNavSection);
  ctivNavigateTo(ctivNavigationCurrent);
}
function ctivNavigationHandler(event) {
  let id = parseInt(event.target.dataset.id);
  ctivNavigateTo(id);
}
function ctivNavigateTo(id) {
  ctivQuestionVideo.pause();
  ctivQuestionNoCam.Video.pause();
  if (
    id > ctivNavigationCurrent &&
    ctivQuestionDetails
      .slice(0, id)
      .some((questionDetail) => !questionDetail.url)
  ) {
    alert(cliptakes_i18n.answer_each_question);
    return;
  }
  if (ctivQuestionDetails[ctivNavigationCurrent]) {
    if (!ctivNoCameraMode) {
      ctivQuestionDetails[ctivNavigationCurrent].startTime =
        ctivQuestionEditing.Start.value;
      ctivQuestionDetails[ctivNavigationCurrent].endTime =
        ctivQuestionEditing.End.value;
    }
  }
  ctivAllNavigationItems.forEach((item) => {
    if (item.dataset.id != id) item.classList.remove(ctivClassNames.navActive);
    else item.classList.add(ctivClassNames.navActive);
    if (
      ctivQuestionDetails.length <= 7 ||
      item.dataset.id < 1 ||
      item.dataset.id >= ctivQuestionDetails.length - 1
    )
      return; // no need to apply long interview styling

    // set spacers
    if (
      [1, ctivQuestionDetails.length - 2].includes(parseInt(item.dataset.id))
    ) {
      if (Math.abs(item.dataset.id - id) > 2)
        item.classList.add(ctivClassNames.navSpacer);
      else item.classList.remove(ctivClassNames.navSpacer);
      return;
    }
    // hide/show nav-items
    if (id <= 3) {
      item.dataset.id < 5
        ? item.classList.remove(ctivClassNames.hidden)
        : item.classList.add(ctivClassNames.hidden);
    } else if (id > ctivQuestionDetails.length - 5) {
      item.dataset.id > ctivQuestionDetails.length - 6
        ? item.classList.remove(ctivClassNames.hidden)
        : item.classList.add(ctivClassNames.hidden);
    } else {
      if (Math.abs(item.dataset.id - id) > 1)
        item.classList.add(ctivClassNames.hidden);
      else item.classList.remove(ctivClassNames.hidden);
    }
  });
  ctivNavigationCurrent = id;
  let target;
  ctivShow([ctivNavigationBackBtn, ctivNavigationNextBtn]);
  if (id < 0) {
    ctivHide(ctivNavigationBackBtn);
    ctivPrepareSetupSection();
    target = ctivSetupSection;
    ctivNavigationText.innerText = "Setup";
  } else if (id < ctivQuestionDetails.length) {
    if (ctivNoCameraMode && ctivNavigationCurrent == 0) {
      ctivHide(ctivNavigationBackBtn);
    }
    ctivPrepareQuestionView(id);
    target = ctivQuestionSection;
    ctivNavigationText.innerText = `${ctivNavigationCurrent + 1} / ${
      ctivQuestionDetails.length
    }`;
  } else {
    ctivHide(ctivNavigationNextBtn);
    ctivStopMediaTracks();
    target = ctivUploadSection;
    ctivNavigationText.innerText = "Upload";
  }
  ctivShowSection(target);
}
function ctivNavigateBack() {
  if (ctivNoCameraMode && ctivNavigationCurrent == 0) return;
  if (ctivNavigationCurrent >= 0) ctivNavigateTo(ctivNavigationCurrent - 1);
}
function ctivNavigateNext() {
  if (ctivNavigationCurrent < ctivQuestionDetails.length)
    ctivNavigateTo(ctivNavigationCurrent + 1);
}
function ctivNavigationSetDone(id) {
  ctivAllNavigationItems[id + 1].classList.add(ctivClassNames.navDone);
}
function ctivNavigationSetUndone(id) {
  ctivAllNavigationItems[id + 1].classList.remove(ctivClassNames.navDone);
}
function ctivSetDoneNavigateNext() {
  ctivNavigationSetDone(ctivNavigationCurrent);
  ctivNavigateNext();
}
// #endregion

// #region Setup
async function ctivCheckCameraAccess() {
  // Don't use MediaRecorder on mobile devices and unsupported browsers
  if (ctivIsMobileBrowser) {
    ctivSetNoCameraMode();
    ctivNavigationCurrent = 0;
    return;
  } else if (!("MediaRecorder" in window)) {
    ctivSetNoCameraMode();
    ctivNavigationCurrent = 0;
    let message = cliptakes_i18n.old_browser;
    message += "\r\n";
    message += cliptakes_i18n.old_browser_alternative_options;
    alert(message);
    return;
  }
  let canAccessMediaDevices = await ctivGetMediaDevices();
  if (!canAccessMediaDevices) {
    ctivHide(ctivSetup.VideoContainer);
    ctivShow(ctivSetup.MediaError);
    return;
  }
}
function ctivSetupNextNoCamera() {
  ctivSetNoCameraMode();
  ctivNavigateTo(0);
}
function ctivSetNoCameraMode() {
  ctivNoCameraMode = true;
  ctivNavigationBar.removeChild(ctivAllNavigationItems[0]);
  ctivHide(ctivQuestionVideoContainer);
  ctivShow(ctivQuestionNoCam.Section);
}
async function ctivPrepareSetupSection() {
  ctivStopMediaTracks();
  await ctivGetUserMedia();
  ctivSetup.Video.srcObject = new MediaStream([ctivMediaTracks.video]);
  ctivCheckAudioInput();
}

const ctivMediaConstraints = {
  audio: true,
  video: {
    width: {
      ideal: 1280,
    },
    height: {
      ideal: 720,
    },
    aspectRatio: 1.777777778,
  },
};
const ctivMediaTracks = {
  audio: null,
  video: null,
};
async function ctivGetUserMedia() {
  if (ctivMediaTracks.audio && ctivMediaTracks.video) {
    return;
  }
  let stream = await navigator.mediaDevices.getUserMedia(ctivMediaConstraints);
  ctivMediaTracks.audio = stream.getAudioTracks()[0];
  ctivMediaTracks.video = stream.getVideoTracks()[0];
}
function ctivStopMediaTracks() {
  if (ctivMediaTracks.audio) ctivMediaTracks.audio.stop();
  if (ctivMediaTracks.video) ctivMediaTracks.video.stop();
  ctivMediaTracks.audio = null;
  ctivMediaTracks.video = null;
}
async function ctivGetMediaDevices() {
  try {
    // call getUserMedia to prompt user for media permissions
    await ctivGetUserMedia();
    ctivSetup.Video.srcObject = new MediaStream([ctivMediaTracks.video]);

    const devices = await navigator.mediaDevices.enumerateDevices();
    const cameras = devices.filter((device) => device.kind === "videoinput");
    cameras.forEach((camera) => {
      let itemButton = document.createElement("button");
      itemButton.value = camera.deviceId;
      itemButton.textContent = camera.label;
      itemButton.addEventListener("click", ctivSetCamera);
      ctivSetup.CameraOptions.appendChild(itemButton);
    });
    const microphones = devices.filter(
      (device) => device.kind === "audioinput"
    );
    microphones.forEach((mic) => {
      let itemButton = document.createElement("button");
      itemButton.value = mic.deviceId;
      itemButton.textContent = mic.label;
      itemButton.addEventListener("click", ctivSetMicrophone);
      ctivSetup.MicrophoneOptions.appendChild(itemButton);
    });
    return true;
  } catch (error) {
    console.log(error);
    return false;
  }
}
async function ctivSetCamera() {
  ctivHide(ctivSetup.CameraOptions);
  ctivMediaConstraints.video.deviceId = { exact: this.value };
  ctivStopMediaTracks();
  await ctivGetUserMedia();
  ctivSetup.Video.srcObject = new MediaStream([ctivMediaTracks.video]);
}
async function ctivSetMicrophone() {
  ctivHide(ctivSetup.MicrophoneOptions);
  ctivHide(ctivSetup.AudioError);
  ctivMediaConstraints.audio = {
    deviceId: {
      exact: this.value,
    },
  };
  ctivStopMediaTracks();
  await ctivGetUserMedia();
  ctivSetup.Video.srcObject = new MediaStream([ctivMediaTracks.video]);
  ctivCheckAudioInput();
}
function ctivCheckAudioInput() {
  let audioContext = new (window.AudioContext || window.webkitAudioContext)();
  let analyser = audioContext.createAnalyser();
  let dataArray = new Uint8Array(100);
  let audioSourceNode = audioContext.createMediaStreamSource(
    new MediaStream([ctivMediaTracks.audio])
  );
  audioSourceNode.connect(analyser);
  function checkAudio(i = 0) {
    analyser.getByteTimeDomainData(dataArray);
    if (dataArray.some((val) => val != 128)) {
      return;
    } else {
      if (i >= 10) {
        ctivShow(ctivSetup.AudioError);
      } else
        setTimeout(() => {
          checkAudio(i + 1);
        }, 100);
    }
  }
  setTimeout(checkAudio, 100);
}
// #endregion

// #region Questions
async function ctivQuestionSetRecordingView() {
  ctivHide([ctivQuestionNavButtons, ctivQuestionEditing.Overlay]);
  ctivShow([ctivQuestionRecording.Overlay, ctivQuestionTimer.Container]);
  await ctivGetUserMedia();
  ctivQuestionVideo.srcObject = new MediaStream([ctivMediaTracks.video]);
  ctivQuestionVideo.autoplay = true;
  ctivQuestionVideo.setAttribute("data-mode", ctivQuestionVideoModes.stream);
}
function ctivQuestionSetEditingView() {
  ctivHide([ctivQuestionRecording.Overlay, ctivQuestionTimer.Container]);
  ctivShow([ctivQuestionNavButtons, ctivQuestionEditing.Overlay]);
}
function ctivQuestionTimerFormatTime(time) {
  const minutes = Math.floor(time / 60);
  let seconds = time % 60;
  if (seconds < 10) {
    seconds = `0${seconds}`;
  }
  return `${minutes}:${seconds}`;
}
function ctivQuestionTimerGetMarkup(timelimit) {
  return `<svg viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
  <g>
    <circle id="ctiv-question-timer-center" cx="20" cy="20" r="16.5" />
    <circle
      id="ctiv-question-timer-outline-expected"
      class="ctiv-question-timer-oriented-outline"
      cx="20" cy="20" r="15"
    />
    <circle
      id="ctiv-question-timer-outline-passed"
      class="ctiv-question-timer-oriented-outline"
      cx="20" cy="20" r="15"
      stroke-dasharray="0 95"
    />
  </g>
</svg>
<span id="ctiv-question-timer-label">${ctivQuestionTimerFormatTime(
    timelimit
  )}</span>`;
}
function ctivQuestionTimerUpdateUI(timePassed, timelimit) {
  const timePassedPath = document.getElementById(
    "ctiv-question-timer-outline-passed"
  );
  if (timePassed >= Math.floor((timelimit * 3) / 4)) {
    timePassedPath.classList.add("ctiv-question-timer-reached-expected");
  }
  const circleDasharray = `${(
    ((timePassed + 1) / timelimit) *
    (Math.PI * 30)
  ).toFixed(1)} ${Math.PI * 30}`;
  timePassedPath.setAttribute("stroke-dasharray", circleDasharray);
}
function ctivPrepareQuestionView(id) {
  ctivQuestionText.innerText = ctivQuestionDetails[id].text;
  if (ctivNoCameraMode) {
    if (
      !ctivQuestionDetails[id].url ||
      ctivQuestionDetails[id].url.length == 0
    ) {
      ctivHide([
        ctivQuestionNoCam.AnsweredState,
        ctivQuestionNoCam.VideoContainer,
      ]);
      ctivShow(ctivQuestionNoCam.RotateDevice);
    } else {
      ctivQuestionNoCam.Video.srcObject = null;
      ctivQuestionNoCam.Video.src = ctivQuestionDetails[id].url;
      ctivQuestionNoCam.Video.autoplay = false;
      ctivQuestionNoCam.Video.currentTime = 0;
      ctivQuestionNoCam.Seek.value = 0;
      ctivQuestionNoCam.Progress.value = 0;

      ctivHide(ctivQuestionNoCam.RotateDevice);
      ctivShow([
        ctivQuestionNoCam.AnsweredState,
        ctivQuestionNoCam.VideoContainer,
      ]);
    }
  } else {
    if (
      !ctivQuestionDetails[id].url ||
      ctivQuestionDetails[id].url.length == 0
    ) {
      const timelimit = parseInt(ctivQuestionDetails[id].timelimit ?? 0);
      ctivQuestionTimer.Container.innerHTML =
        timelimit > 0 ? ctivQuestionTimerGetMarkup(timelimit) : "";
      ctivQuestionTimer.TimePassedPath = document.getElementById(
        "ctiv-question-timer-outline-passed"
      );
      ctivQuestionTimer.Label = document.getElementById(
        "ctiv-question-timer-label"
      );
      ctivQuestionSetRecordingView();
      ctivShow(ctivQuestionRecording.StartBtn);
      ctivHide([
        ctivQuestionRecording.StopBtn,
        ctivQuestionRecording.Indicator,
      ]);
    } else {
      ctivQuestionVideo.srcObject = null;
      ctivQuestionVideo.src = ctivQuestionDetails[id].url;
      ctivQuestionVideo.autoplay = false;
      ctivQuestionVideo.setAttribute(
        "data-mode",
        ctivQuestionVideoModes.playback
      );
      ctivQuestionVideo.load();
      ctivQuestionSetPlayIcon();
      ctivQuestionSetEditingView();
    }
  }
}
async function ctivQuestionNoCamLoadVideo() {
  const fileUrl = URL.createObjectURL(ctivQuestionNoCam.FileInput.files[0]);
  ctivQuestionDetails[ctivNavigationCurrent].url = fileUrl;
  const duration = await ctivQuestionGetDuration(fileUrl);

  const timelimit = parseInt(
    ctivQuestionDetails[ctivNavigationCurrent].timelimit ?? 0
  );
  if (timelimit > 0 && duration > timelimit > 0) {
    ctivQuestionDetails[ctivNavigationCurrent].url = null;
    ctivQuestionDetails[ctivNavigationCurrent].startTime = null;
    ctivQuestionDetails[ctivNavigationCurrent].endTime = null;
    alert(
      cliptakes_i18n.timelimit_answer_too_long.replace("<TIMELIMIT>", timelimit)
    );
    ctivNavigationSetUndone(ctivNavigationCurrent);
    ctivPrepareQuestionView(ctivNavigationCurrent);
    return;
  }
  ctivQuestionDetails[ctivNavigationCurrent].startTime = 0;
  ctivQuestionDetails[ctivNavigationCurrent].endTime = duration;
  ctivQuestionNoCam.Seek.max = duration;
  ctivQuestionNoCam.Progress.max = duration;
  ctivNavigationSetDone(ctivNavigationCurrent);
  ctivPrepareQuestionView(ctivNavigationCurrent);
}
function ctivQuestionNoCamTogglePlay() {
  if (ctivQuestionNoCam.Video.paused || ctivQuestionNoCam.Video.ended) {
    ctivQuestionNoCam.Video.play();
  } else {
    ctivQuestionNoCam.Video.pause();
  }
}
function ctivQuestionNoCamVideoTimeUpdate() {
  const currentTime = parseFloat(ctivQuestionNoCam.Video.currentTime);
  ctivQuestionNoCam.Seek.value = currentTime;
  ctivQuestionNoCam.Progress.value = currentTime;
}
function ctivQuestionNoCamSetVideoTime(event) {
  ctivQuestionNoCam.Video.currentTime = parseFloat(event.target.value);
}
function ctivQuestionStartRecording() {
  ctivHide(ctivQuestionRecording.StartBtn);
  ctivShow([ctivQuestionRecording.StopBtn, ctivQuestionRecording.Indicator]);
  let recStream = new MediaStream([
    ctivMediaTracks.video,
    ctivMediaTracks.audio,
  ]);
  let options = {
    audioBitsPerSecond: 128000,
    videoBitsPerSecond: 2500000,
    mimeType: "video/" + ctivQuestionFileFormat,
  };
  let mediaRecorder = new MediaRecorder(recStream, options);

  let timePassed = 0;
  const timelimit = parseInt(
    ctivQuestionDetails[ctivNavigationCurrent].timelimit ?? 0
  );
  const startCountdownAt = timelimit >= 45 ? 15 : timelimit >= 25 ? 10 : 5;
  let timeLeft = timelimit;
  if (timelimit > 0) ctivQuestionTimerUpdateUI(timePassed, timelimit);
  clearInterval(ctivQuestionTimer.Interval);

  ctivQuestionRecording.StopBtn.onclick = function () {
    mediaRecorder.stop();
  };

  mediaRecorder.ondataavailable = function (e) {
    ctivQuestionRecordingChunks.push(e.data);
  };

  mediaRecorder.onstop = function (e) {
    ctivStopMediaTracks();
    clearInterval(ctivQuestionTimer.Interval);
    let videoBlob = new Blob(ctivQuestionRecordingChunks, {
      type: options.mimeType,
    });
    ctivQuestionDetails[ctivNavigationCurrent].url =
      URL.createObjectURL(videoBlob);
    ctivNavigationSetDone(ctivNavigationCurrent);
    ctivPrepareQuestionView(ctivNavigationCurrent);
  };

  ctivQuestionRecordingChunks = [];
  mediaRecorder.start();
  if (timelimit > 0) {
    ctivQuestionTimer.Interval = setInterval(() => {
      timePassed = timePassed += 1;
      timeLeft = timelimit - timePassed;
      if (timeLeft > startCountdownAt) {
        ctivQuestionTimer.Label.innerHTML =
          ctivQuestionTimerFormatTime(timeLeft);
      } else {
        ctivQuestionTimer.Label.classList.add("countdown");
        ctivQuestionTimer.Label.innerHTML = Math.max(timeLeft, 0);
      }
      ctivQuestionTimerUpdateUI(timePassed, timelimit);
      if (timePassed > timelimit) {
        mediaRecorder.stop();
      }
    }, 1000);
  }
}
function ctivQuestionRetakeVideo() {
  if (confirm(cliptakes_i18n.confirm_retake)) {
    // reset url / startTime / endTime
    ctivQuestionDetails[ctivNavigationCurrent].url = null;
    ctivQuestionDetails[ctivNavigationCurrent].startTime = null;
    ctivQuestionDetails[ctivNavigationCurrent].endTime = null;
    ctivNavigationSetUndone(ctivNavigationCurrent);
    ctivPrepareQuestionView(ctivNavigationCurrent);
  }
}
// #region Trimming UI
function ctivQuestionTogglePlay() {
  if (ctivQuestionVideo.dataset.mode === ctivQuestionVideoModes.stream) {
    return;
  }
  if (ctivQuestionVideo.paused || ctivQuestionVideo.ended) {
    if (
      ctivQuestionVideo.currentTime < ctivQuestionEditing.Start.value ||
      ctivQuestionVideo.currentTime >= ctivQuestionEditing.End.value
    ) {
      ctivQuestionVideo.currentTime = ctivQuestionEditing.Start.value;
    }
    ctivQuestionVideo.play();
  } else {
    ctivQuestionVideo.pause();
  }
}
function ctivQuestionSetPlayIcon() {
  ctivHide(ctivQuestionEditingControls.PauseIcon);
  ctivShow(ctivQuestionEditingControls.PlayIcon);
  ctivQuestionEditingControls.TogglePlayBtn.dataset.title = "Play";
}
function ctivQuestionSetPauseIcon() {
  ctivHide(ctivQuestionEditingControls.PlayIcon);
  ctivShow(ctivQuestionEditingControls.PauseIcon);
  ctivQuestionEditingControls.TogglePlayBtn.dataset.title = "Pause";
}
function ctivQuestionFormatTime(timeInSeconds) {
  const result = new Date(timeInSeconds * 1000).toISOString().substr(11, 11);
  return {
    minutes: result.substr(3, 2),
    seconds: result.substr(6, 2),
    milliseconds: result.substr(9, 1),
  };
}
function ctivQuestionVideoTimeUpdate() {
  if (ctivQuestionVideo.dataset.mode != ctivQuestionVideoModes.playback) {
    return;
  }
  if (ctivQuestionVideo.currentTime >= ctivQuestionEditing.End.value) {
    ctivQuestionVideo.pause();
  }
  const currentTime = parseFloat(ctivQuestionVideo.currentTime);
  ctivQuestionEditing.Seek.value = Math.min(
    currentTime,
    ctivQuestionEditing.End.value
  );
  const time = ctivQuestionFormatTime(currentTime);
  ctivQuestionEditingControls.TimeElapsed.innerText = `${time.minutes}:${time.seconds}`;
  ctivQuestionEditingControls.TimeElapsed.setAttribute(
    "datetime",
    `${time.minutes}m ${time.seconds}s`
  );
}
function ctivQuestionUpdateSeekIndex(time, tooltipPos) {
  ctivQuestionEditing.SeekIndex.textContent = `${time.minutes}:${time.seconds}.${time.milliseconds}`;
  const rect = ctivQuestionEditing.Seek.getBoundingClientRect();
  let left =
    tooltipPos < 15
      ? 0
      : tooltipPos > rect.right - 45
      ? rect.right - 60
      : tooltipPos - 15;
  ctivQuestionEditing.SeekIndex.style.left = left + "px";
}
function ctivQuestionSetSeekIndexOnHover(event) {
  if (ctivQuestionVideoDuration < 0) return;
  const rect = ctivQuestionEditing.Seek.getBoundingClientRect();
  let rectLeft = Math.round(rect.left) + 2;
  event.pageX = Math.max(rectLeft, Math.min(rect.right, event.pageX - 1));
  let skipTo =
    Math.round(
      (10 * ((event.pageX - rectLeft) * ctivQuestionVideoDuration)) /
        (ctivQuestionEditing.Seek.clientWidth - 4)
    ) / 10;

  if (skipTo < ctivQuestionEditing.Start.value) {
    skipTo = ctivQuestionEditing.Start.value;
  } else if (skipTo > ctivQuestionEditing.End.value) {
    skipTo = ctivQuestionEditing.End.value;
  }
  ctivQuestionEditing.Seek.dataset.seek = skipTo;
  const time = ctivQuestionFormatTime(skipTo);
  const tooltipPos =
    (skipTo / ctivQuestionVideoDuration) * ctivQuestionEditing.Seek.clientWidth;
  ctivQuestionUpdateSeekIndex(time, tooltipPos);
}
function ctivGetSplitBackground(idx, splitPercent) {
  let leftColor = idx == 0 ? "var(--cliptakes-dark)" : "transparent";
  let rightColor = idx == 0 ? "transparent" : "var(--cliptakes-dark)";
  let background = `linear-gradient(to right,
      ${leftColor} 0%, ${leftColor} ${splitPercent}%,
      ${rightColor} ${splitPercent}%, ${rightColor} 100%)`;
  return background;
}
// update background and skip video to timestamp when trimming slider is scrubbed
function ctivQuestionSetSliderValue(event) {
  let targetValue = parseFloat(
    event.target.dataset.seek ? event.target.dataset.seek : event.target.value
  );
  let startValue = parseFloat(ctivQuestionEditing.Start.value);
  let endValue = parseFloat(ctivQuestionEditing.End.value);
  if (ctivQuestionEditing.Seek.id == event.target.id) {
    if (targetValue > endValue) {
      ctivQuestionEditing.Seek.value = endValue;
    } else if (targetValue < startValue) {
      ctivQuestionEditing.Seek.value = startValue;
    }
  } else if (ctivQuestionEditing.Start.id == event.target.id) {
    if (targetValue > endValue - 1.0) {
      startValue = endValue - 1.0;
      ctivQuestionEditing.Start.value = startValue;
    }
    if (targetValue > parseFloat(ctivQuestionEditing.Seek.value)) {
      ctivQuestionEditing.Seek.value = startValue;
    }
    let splitPercent = (100 * startValue) / ctivQuestionVideoDuration;
    ctivQuestionEditing.TrimmedFrame.style.marginLeft = splitPercent + "%";
    ctivQuestionEditing.StartBackground.style.background =
      ctivGetSplitBackground(0, splitPercent);
  } else if (ctivQuestionEditing.End.id == event.target.id) {
    if (targetValue < startValue + 1.0) {
      endValue = startValue + 1.0;
      ctivQuestionEditing.End.value = endValue;
    }
    if (targetValue < parseFloat(ctivQuestionEditing.Seek.value)) {
      ctivQuestionEditing.Seek.value = endValue;
    }
    let splitPercent = (100 * endValue) / ctivQuestionVideoDuration;
    ctivQuestionEditing.TrimmedFrame.style.marginRight =
      100 - splitPercent + "%";
    ctivQuestionEditing.EndBackground.style.background = ctivGetSplitBackground(
      1,
      splitPercent
    );
  }
  ctivQuestionVideo.currentTime = targetValue;
  const time = ctivQuestionFormatTime(targetValue);
  const rect = ctivQuestionEditing.Seek.getBoundingClientRect();
  const tooltipPos = (targetValue / ctivQuestionVideoDuration) * rect.width;
  ctivQuestionUpdateSeekIndex(time, tooltipPos);
}
async function ctivQuestionGetDuration(blobURL) {
  const tempVideo = document.createElement("video");
  const duration = new Promise((resolve, reject) => {
    tempVideo.addEventListener("loadedmetadata", () => {
      if (tempVideo.duration === Infinity) {
        tempVideo.currentTime = Number.MAX_SAFE_INTEGER;
        tempVideo.ontimeupdate = () => {
          tempVideo.ontimeupdate = null;
          let result = parseFloat(tempVideo.duration).toFixed(2);
          tempVideo.remove();
          resolve(result);
        };
        tempVideo.play();
      } else {
        let result = parseFloat(tempVideo.duration).toFixed(2);
        tempVideo.remove();
        resolve(result);
      }
    });
    tempVideo.onerror = (event) => {
      tempVideo.remove();
      reject(event.target.error);
    };
  });
  tempVideo.src = blobURL;
  return duration;
}
async function ctivQuestionInitializeVideo() {
  if (ctivQuestionVideo.dataset.mode != ctivQuestionVideoModes.playback) {
    return;
  }
  const videoDuration = await ctivQuestionGetDuration(ctivQuestionVideo.src);
  ctivQuestionVideoDuration = videoDuration;
  ctivQuestionEditing.Seek.max = videoDuration;
  ctivQuestionEditing.Start.max = videoDuration;
  ctivQuestionEditing.End.max = videoDuration;
  let questionDetail = ctivQuestionDetails[ctivNavigationCurrent];
  ctivQuestionEditing.Start.value = questionDetail.startTime
    ? parseFloat(questionDetail.startTime)
    : 0.0;
  ctivQuestionEditing.End.value = questionDetail.endTime
    ? parseFloat(questionDetail.endTime)
    : videoDuration;
  ctivQuestionEditing.Seek.value = ctivQuestionEditing.Start.value;
  ctivQuestionSetSliderValue({ target: ctivQuestionEditing.Start });
  ctivQuestionSetSliderValue({ target: ctivQuestionEditing.End });
  ctivQuestionVideo.currentTime = ctivQuestionEditing.Start.value;
  const time = ctivQuestionFormatTime(videoDuration);
  ctivQuestionEditingControls.Duration.innerText = `${time.minutes}:${time.seconds}`;
  ctivQuestionEditingControls.Duration.setAttribute(
    "datetime",
    `${time.minutes}m ${time.seconds}s`
  );
}
// #endregion Trimming UI
// #region Controls
function ctivQuestionUpdateVolume(event) {
  if (ctivQuestionVideo.muted) {
    ctivQuestionVideo.muted = false;
  }
  ctivQuestionVideo.volume = event.target.value;
}
function ctivQuestionUpdateVolumeIcon() {
  ctivQuestionEditingControls.ToggleMuteBtn.dataset.title = "Mute";
  ctivHide([
    ctivQuestionEditingControls.VolumeMuteIcon,
    ctivQuestionEditingControls.VolumeLowIcon,
    ctivQuestionEditingControls.VolumeHighIcon,
  ]);

  if (ctivQuestionVideo.muted || ctivQuestionVideo.volume === 0) {
    ctivShow(ctivQuestionEditingControls.VolumeMuteIcon);
    ctivQuestionEditingControls.ToggleMuteBtn.dataset.title = "Unmute";
  } else if (ctivQuestionVideo.volume > 0 && ctivQuestionVideo.volume <= 0.5) {
    ctivShow(ctivQuestionEditingControls.VolumeLowIcon);
  } else {
    ctivShow(ctivQuestionEditingControls.VolumeHighIcon);
  }
}
function ctivQuestionToggleMute() {
  ctivQuestionVideo.muted = !ctivQuestionVideo.muted;
  if (ctivQuestionVideo.muted) {
    ctivQuestionEditingControls.Volume.dataset.volume =
      ctivQuestionEditingControls.Volume.value;
    ctivQuestionEditingControls.Volume.value = 0;
  } else {
    ctivQuestionEditingControls.Volume.value =
      ctivQuestionEditingControls.Volume.dataset.volume;
  }
}
function ctivQuestionToggleFullscreen() {
  ctivHide(ctivQuestionEditingControls.ExitFullscreenIcon);
  ctivShow(ctivQuestionEditingControls.FullscreenIcon);
  ctivQuestionEditingControls.ToggleFullscreenBtn.dataset.title = "Full Screen";

  if (document.fullscreenElement) {
    document.exitFullscreen();
  } else if (document.webkitFullscreenElement) {
    // Need this to support Safari
    document.webkitExitFullscreen();
  } else {
    ctivHide(ctivQuestionEditingControls.FullscreenIcon);
    ctivShow(ctivQuestionEditingControls.ExitFullscreenIcon);
    ctivQuestionEditingControls.ToggleFullscreenBtn.dataset.title =
      "Exit Full Screen";
    if (ctivQuestionVideoContainer.webkitRequestFullscreen) {
      // Need this to support Safari
      ctivQuestionVideoContainer.webkitRequestFullscreen();
    } else {
      ctivQuestionVideoContainer.requestFullscreen();
    }
  }
}
// #endregion Controls
// #endregion
// #region Cliptakes-API-Calls
async function ctivGetAuthHeader(authUrl, dateString) {
  const enc = new TextEncoder("utf-8");
  const cryptoKey = await window.crypto.subtle.importKey(
    "raw",
    enc.encode(ctivUserInfo.licenseKey),
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
    enc.encode("POST" + authUrl + dateString)
  );
  const digestBuffer = new Uint8Array(signature);
  const digest = Array.prototype.map
    .call(digestBuffer, (x) => ("00" + x.toString(16)).slice(-2))
    .join("");

  return ctivUserInfo.subscriptionId + ":" + digest;
}
async function ctivAuthorizeSubscription() {
  const authUrl = "/v1/authorize";
  const dateString = new Date().toISOString();
  const authHeader = await ctivGetAuthHeader(authUrl, dateString);
  const validationResponse = await fetch(ctivApiUrl + authUrl, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Timestamp: dateString,
      Authorization: authHeader,
    },
    body: JSON.stringify({ subscriptionId: ctivUserInfo.subscriptionId }),
  });
  ctivSubscriptionAuthorized = validationResponse.status == 200;
  if (!ctivSubscriptionAuthorized) {
    ctivIntroNextBtn.setAttribute("disabled", true);
    ctivShow(ctivIntroAuthorizationError);
  }
}
function ctivGenerateInterviewId() {
  const lut = [];
  for (let i = 0; i < 256; i++) {
    lut[i] = (i < 16 ? "0" : "") + i.toString(16);
  }
  const d0 = (Math.random() * 0xffffffff) | 0;
  const d1 = (Math.random() * 0xffffffff) | 0;
  const d2 = (Math.random() * 0xffffffff) | 0;
  const d3 = (Math.random() * 0xffffffff) | 0;
  return (
    lut[d0 & 0xff] +
    lut[(d0 >> 8) & 0xff] +
    lut[(d0 >> 16) & 0xff] +
    lut[(d0 >> 24) & 0xff] +
    "-" +
    lut[d1 & 0xff] +
    lut[(d1 >> 8) & 0xff] +
    "-" +
    lut[((d1 >> 16) & 0x0f) | 0x40] +
    lut[(d1 >> 24) & 0xff] +
    "-" +
    lut[(d2 & 0x3f) | 0x80] +
    lut[(d2 >> 8) & 0xff] +
    "-" +
    lut[d3 & 0xff] +
    lut[(d3 >> 8) & 0xff] +
    Date.now().toString().substr(4)
  );
}
async function ctivSubmitVideos() {
  if (ctivQuestionDetails.some((question) => !question.url)) {
    alert(cliptakes_i18n.answer_all_questions_alert);
    return;
  }
  ctivHide([ctivNavSection, ctivUpload.Before, ctivUpload.Error]);
  ctivShow(ctivUpload.Waiting);
  try {
    const getUploadLinkUrl = "/v1/upload";
    const addConcatRequestUrl = "/v1/concat";
    const startProcessingRequestUrl = "/v1/startProcessing";
    if (!ctivInterviewInfo.interviewId) {
      ctivInterviewInfo.interviewId = ctivGenerateInterviewId();
    }

    let clips = {};
    for (let i = 0; i < ctivQuestionDetails.length; i++) {
      clips[`${i}`] = {
        questionId: ctivQuestionDetails[i].id,
        startTime: ctivQuestionDetails[i].startTime,
        endTime: ctivQuestionDetails[i].endTime,
        sourceFileFormat: ctivQuestionFileFormat,
        status: "uploaded",
      };
    }
    const templateId = document.getElementById("ctiv-main-container").dataset
      .templateId;
    let concatRequestBody = JSON.stringify({
      subscriptionId: ctivUserInfo.subscriptionId,
      interviewId: ctivInterviewInfo.interviewId,
      templateId,
      contactId: ctivContactId,
      firstName: ctivInterviewInfo.firstName,
      lastName: ctivInterviewInfo.lastName,
      email: ctivInterviewInfo.email,
      sendCandidateMail: ctivInterviewInfo.sendCandidateMail,
      userAgent: ctivInterviewInfo.userAgent,
      customInfo: ctivInterviewInfo.customInfo,
      clips,
    });

    let dateString = new Date().toISOString();
    let authHeader = await ctivGetAuthHeader(addConcatRequestUrl, dateString);
    await fetch(ctivApiUrl + addConcatRequestUrl, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Timestamp: dateString,
        Authorization: authHeader,
      },
      body: concatRequestBody,
    }).then((response) => response.json());

    for (i = 0; i < ctivQuestionDetails.length; i++) {
      let fileName = `clip_${i}.${ctivQuestionFileFormat}`;
      let filePath = `${ctivUserInfo.subscriptionId}/${ctivInterviewInfo.interviewId}/${fileName}`;
      let type = "video/" + ctivQuestionFileFormat;
      let uploadRequestBody = JSON.stringify({ filePath, type });

      dateString = new Date().toISOString();
      authHeader = await ctivGetAuthHeader(getUploadLinkUrl, dateString);
      const signedUrlResponse = await fetch(ctivApiUrl + getUploadLinkUrl, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Timestamp: dateString,
          Authorization: authHeader,
        },
        body: uploadRequestBody,
      }).then((response) => response.json());

      const clip = await fetch(ctivQuestionDetails[i].url).then((r) =>
        r.blob()
      );

      await fetch(signedUrlResponse.data.url, {
        method: "PUT",
        headers: { "content-type": type },
        body: clip,
      });
    }
    const startProcessingRequestBody = JSON.stringify({
      subscriptionId: ctivUserInfo.subscriptionId,
      interviewId: ctivInterviewInfo.interviewId,
    });

    dateString = new Date().toISOString();
    authHeader = await ctivGetAuthHeader(startProcessingRequestUrl, dateString);
    await fetch(ctivApiUrl + startProcessingRequestUrl, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Timestamp: dateString,
        Authorization: authHeader,
      },
      body: startProcessingRequestBody,
    });
    // Don't warn anymore when leaving the page
    window.removeEventListener("beforeunload", ctivWarnBeforeLeavingPage);
    ctivHide(ctivUpload.Waiting);
    ctivShow(ctivUpload.After);
  } catch (err) {
    const reportErrorUrl = "/v1/reportError";
    const reportErrorRequestBody = JSON.stringify({
      subscriptionId: ctivUserInfo.subscriptionId,
      interviewId: ctivInterviewInfo.interviewId,
      error: err.message,
    });
    dateString = new Date().toISOString();
    authHeader = await ctivGetAuthHeader(reportErrorUrl, dateString);
    ctivHide(ctivUpload.Waiting);
    ctivUpload.ErrorMessage.innerHTML = "Error Message: " + err.message;
    ctivShow([ctivNavSection, ctivUpload.Before, ctivUpload.Error]);
    await fetch(ctivApiUrl + reportErrorUrl, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Timestamp: dateString,
        Authorization: authHeader,
      },
      body: reportErrorRequestBody,
    });
  }
}
// #endregion
// ====================
// #endregion Functions

// #region Event-Listeners
// =======================
// #region Intro
ctivIntroNextBtn.addEventListener("click", ctivStartSignUp);
// #endregion
// #region Sign-Up
ctivSignUpForm.addEventListener("submit", ctivSubmitSignUp);
// #endregion
// #region Navigation
ctivNavigationBackBtn.addEventListener("click", ctivNavigateBack);
ctivNavigationNextBtn.addEventListener("click", ctivNavigateNext);
ctivAllNavigationItems.forEach((navItem) => {
  navItem.addEventListener("click", ctivNavigationHandler);
});
// #endregion
// #region Setup
ctivSetup.NextNoCameraBtn.addEventListener("click", ctivSetupNextNoCamera);
ctivSetup.NextBtn.addEventListener("click", ctivSetDoneNavigateNext);
ctivSetup.CameraBtn.addEventListener("mouseenter", () => {
  ctivHide(ctivSetup.MicrophoneOptions);
  ctivShow(ctivSetup.CameraOptions);
});
ctivSetup.CameraBtn.addEventListener("click", () =>
  ctivSetup.CameraOptions.classList.toggle(ctivClassNames.hidden)
);
ctivSetup.CameraOptions.addEventListener("mouseleave", () =>
  ctivHide(ctivSetup.CameraOptions)
);
ctivSetup.MicrophoneBtn.addEventListener("click", () =>
  ctivSetup.MicrophoneOptions.classList.toggle(ctivClassNames.hidden)
);
ctivSetup.MicrophoneBtn.addEventListener("mouseenter", () => {
  ctivHide(ctivSetup.CameraOptions);
  ctivShow(ctivSetup.MicrophoneOptions);
});
ctivSetup.MicrophoneOptions.addEventListener("mouseleave", () =>
  ctivHide(ctivSetup.MicrophoneOptions)
);
// #endregion
// #region Questions
ctivQuestionNoCam.FileInput.addEventListener("click", (e) => {
  if (ctivQuestionDetails[ctivNavigationCurrent].url) {
    if (!confirm(cliptakes_i18n.confirm_retake)) {
      e.preventDefault();
    }
  }
});
ctivQuestionNoCam.FileInput.addEventListener(
  "change",
  ctivQuestionNoCamLoadVideo
);
ctivQuestionNoCam.Video.addEventListener("click", ctivQuestionNoCamTogglePlay);
ctivQuestionNoCam.Video.addEventListener(
  "timeupdate",
  ctivQuestionNoCamVideoTimeUpdate
);
ctivQuestionNoCam.Seek.addEventListener("input", ctivQuestionNoCamSetVideoTime);
ctivQuestionRecording.StartBtn.addEventListener(
  "click",
  ctivQuestionStartRecording
);
ctivQuestionRetakeBtn.addEventListener("click", ctivQuestionRetakeVideo);
ctivQuestionNextBtn.addEventListener("click", ctivNavigateNext);
ctivQuestionVideo.addEventListener("click", ctivQuestionTogglePlay);
ctivQuestionVideo.addEventListener("play", ctivQuestionSetPauseIcon);
ctivQuestionVideo.addEventListener("pause", ctivQuestionSetPlayIcon);
ctivQuestionVideo.addEventListener(
  "loadedmetadata",
  ctivQuestionInitializeVideo
);
ctivQuestionVideo.addEventListener("timeupdate", ctivQuestionVideoTimeUpdate);
ctivQuestionVideo.addEventListener(
  "volumechange",
  ctivQuestionUpdateVolumeIcon
);
ctivQuestionEditing.Seek.addEventListener(
  "mousemove",
  ctivQuestionSetSeekIndexOnHover
);
ctivQuestionEditing.Start.addEventListener(
  "mousemove",
  ctivQuestionSetSeekIndexOnHover
);
ctivQuestionEditing.End.addEventListener(
  "mousemove",
  ctivQuestionSetSeekIndexOnHover
);
ctivQuestionEditing.Seek.addEventListener("input", ctivQuestionSetSliderValue);
ctivQuestionEditing.Start.addEventListener("input", ctivQuestionSetSliderValue);
ctivQuestionEditing.End.addEventListener("input", ctivQuestionSetSliderValue);
ctivQuestionEditingControls.TogglePlayBtn.addEventListener(
  "click",
  ctivQuestionTogglePlay
);
ctivQuestionEditingControls.Volume.addEventListener(
  "input",
  ctivQuestionUpdateVolume
);
ctivQuestionEditingControls.ToggleMuteBtn.addEventListener(
  "click",
  ctivQuestionToggleMute
);
ctivQuestionEditingControls.ToggleFullscreenBtn.addEventListener(
  "click",
  ctivQuestionToggleFullscreen
);
// #endregion
// #region Upload
ctivUploadBtn.addEventListener("click", ctivSubmitVideos);
// #endregion
// ==========================
// #endregion Event-Listeners
