<?php
function get_default_intro(){
    $markup = '<h1>Welcome!</h1>
    <div>
      The purpose of this video is to introduce yourself and summarise your relevant qualifications, experience and attributes.
      Short answers, between 30 – 60 seconds are sufficient.
      <h4>General guidance</h4>
      <ul>
        <li>
          Consider your background. A neutral, tidy setting, or plain white wall is best.
        </li>
        <li>
          Lighting is important. We recommend recording during the daytime for natural lighting.
        </li>
        <li>
          Dress code: Dress as you would in a formal face-to-face interview.
        </li>
        <li>Speak clearly and smile. First impressions count!</li>
      </ul>
    </div>';
    return $markup;
}
function get_intro_next_button($button_content, $active=true){
    $markup = '<button id="ctiv-intro-next" class="ctiv-button"' . ($active ? '>' : ' disabled>');
    $markup .= esc_html__( $button_content );
    $markup .= '</button>';
    return $markup;
}

function get_default_signup(){
  $markup = '<h1>Start Your Interview</h1>
  <p>[cliptakes_input_first_name]</p>  
  <p>[cliptakes_input_last_name]</p>
  <p>&nbsp;</p>
  <div>
  <h5>Privacy Policy</h5>
  <p>By completing this online video interview, you are granting us permission to securely store and share your video with potential clients that may be hiring for suitable roles and/ or to make an internal hiring decision.</p>
  <p>You have the right to withdraw this consent at any time as per our data protection policy. If you have any questions on how your video will be used to secure you work, please seek clarification from your consultant.</p>
  [cliptakes_input_accept_policy]
  </div>
  ';
  return $markup;
}
function get_signup_submit_button($button_content){
    $markup = '<input type="submit" value="' . esc_html__( $button_content ) . '" class="ctiv-button" />';
    return $markup;
}

function get_setup_section($button_content){
    $markup = '<div id="ctiv-setup-section" class="ctiv-hidden ctiv-section">
      <div id="ctiv-setup-media-error" class="ctiv-hidden">
        <h3>' . _x('Something went wrong.', 'Frontend: Error message title', 'cliptakes') . '</h3>
        <div>' . _x('We can\'t record the interview without access to your webcam. Please click on the security lock or the camera icon at the top left of your browser and allow access to your webcam and microphone.', 'Frontend: Allow camera access message', 'cliptakes') . '</div>
        <button
          id="ctiv-setup-next-no-camera"
          class="ctiv-button ctiv-button-neutral"
        >' . _x('Continue without camera', 'Frontend: Button label (no camera, use media upload)', 'cliptakes') . '</button>
      </div>
      <div id="ctiv-setup-video-container" class="ctiv-video-container">
        <video id="ctiv-setup-video" class="ctiv-video" autoplay></video>
        <div id="ctiv-setup-bottom-overlay" class="ctiv-bottom-overlay">
          <button id="ctiv-setup-next" class="ctiv-button">' . esc_html__( $button_content ) . '</button>
        </div>
        <div id="ctiv-setup-devices">
          <div class="ctiv-dropdown">
            <button
              id="ctiv-setup-camera-button"
              class="ctiv-overlay-button"
            >
              <svg class="ctiv-svg-outline">
                <use href="#ctiv-camera-icon"></use>
              </svg>
              <p>' . _x('Camera', 'Frontend: Camera', 'cliptakes') . '</p>
            </button>
            <div
              id="ctiv-setup-camera-options"
              class="ctiv-dropdown-content ctiv-hidden"
            ></div>
          </div>
          <div class="ctiv-dropdown">
            <button
              id="ctiv-setup-microphone-button"
              class="ctiv-overlay-button"
            >
              <svg class="ctiv-svg-outline">
                <use href="#ctiv-microphone-icon"></use>
              </svg>
              <p>' . _x('Microphone', 'Frontend: Microphone', 'cliptakes') . '</p>
            </button>
            <div
              id="ctiv-setup-microphone-options"
              class="ctiv-dropdown-content ctiv-hidden"
            ></div>
          </div>
          <div id="ctiv-setup-audio-error" class="ctiv-hidden">
            ' . _x('No audio input detected.', 'Frontend: No audio input error message', 'cliptakes') . '
          </div>
        </div>
      </div>
    </div>';
  return $markup;
}

function get_question_section($question_size, $upload_button, $retake_button, $next_button){
    $markup = '
    <div id="ctiv-question-section" class="ctiv-hidden ctiv-section">
    <' . $question_size . ' id="ctiv-question-text"></' . $question_size . '>
    <div id="ctiv-question-nocam-section" class="ctiv-hidden">
      <div id="ctiv-question-answered-state" class="ctiv-hidden">
        <svg id="ctiv-question-answered-icon">
          <use href="#ctiv-tick-icon"></use>
        </svg>
        <span id="ctiv-question-answered-text">' . _x('Answered', 'Frontend: Mobile view, Question answered indicator', 'cliptakes') . '</span>
      </div>
      <div id="ctiv-question-nocam-video-container" class="ctiv-hidden">
        <video
          id="ctiv-question-nocam-video"
          class="ctiv-video"
          playsinline
        ></video>
        <div id="ctiv-question-nocam-video-controls">
          <progress id="ctiv-question-nocam-video-progress"></progress>
          <input
              id="ctiv-question-nocam-seek"
              class="ctiv-slider"
              type="range"
              step="0.01"
            />
        </div>
      </div>
      <div id="ctiv-rotate-device-note">
        <svg id="ctiv-rotate-device-svg">
          <use href="#ctiv-rotate-device-icon"></use>
        </svg>
        <span id="ctiv-rotate-device-text">' . _x('Please record in landscape mode.', 'Frontend: Mobile view, rotate device / record in landscape mode message', 'cliptakes') . '</span>
      </div>
      <label for="ctiv-question-nocam-file-input" class="ctiv-button">
        <input
          id="ctiv-question-nocam-file-input"
          type="file"
          accept="video/*"
        />
        ' . esc_html__( $upload_button ) . '
      </label>
    </div>
    <div
      id="ctiv-question-video-container"
      class="ctiv-video-container"
    >
      <video id="ctiv-question-video" class="ctiv-video"></video>
      <div
        id="ctiv-question-recording-overlay"
        class="ctiv-bottom-overlay"
      >
        <button id="ctiv-question-start-recording" class="ctiv-button">' . _x('Start Recording', 'Frontend: Button label start recording', 'cliptakes') . '</button>
        <button
          id="ctiv-question-stop-recording"
          class="ctiv-button ctiv-hidden"
        >' . _x('Stop Recording', 'Frontend: Button label stop recording', 'cliptakes') . '</button>
        <div
          id="ctiv-question-recording-indicator"
          class="ctiv-hidden"
        ></div>
      </div>
      <div id="ctiv-question-timer"></div>
      <div id="ctiv-question-nav-buttons">
        <button
          id="ctiv-question-retake"
          class="ctiv-overlay-nav-button"
        >
          <svg id="ctiv-retake-icon">
            <use href="#ctiv-retake-icon-path"></use>
          </svg> ' . esc_html__( $retake_button ) . '
        </button>
        <button id="ctiv-question-next" class="ctiv-overlay-nav-button">
        ' . esc_html__( $next_button ) . ' ❯
        </button>
      </div>
      <div
        id="ctiv-question-editing-overlay"
        class="ctiv-bottom-overlay"
      >
        <div id="ctiv-question-editing-timeline">
          <div
            id="ctiv-question-start-background"
            class="ctiv-trim-slider-background"
          ></div>
          <div
            id="ctiv-question-end-background"
            class="ctiv-trim-slider-background"
          ></div>
          <div id="ctiv-question-trimmed-frame"></div>
          <input
            id="ctiv-question-seek"
            class="ctiv-slider"
            type="range"
            step="0.01"
          />
          <input
            id="ctiv-question-start"
            class="ctiv-slider ctiv-trim-slider"
            type="range"
            step="0.01"
          />
          <input
            id="ctiv-question-end"
            class="ctiv-slider ctiv-trim-slider"
            type="range"
            step="0.01"
          />
          <div id="ctiv-question-seek-index">00:00</div>
        </div>
        <div id="ctiv-question-editing-controls">
          <div id="ctiv-question-editing-left-controls">
            <button
              data-title="Play"
              id="ctiv-question-toggle-play"
              class="ctiv-control-button"
            >
              <svg class="ctiv-svg">
                <use
                  id="ctiv-question-play-icon"
                  href="#ctiv-play-icon"
                ></use>
                <use
                  id="ctiv-question-pause-icon"
                  class="ctiv-hidden"
                  href="#ctiv-pause-icon"
                ></use>
              </svg>
            </button>
            <div id="ctiv-question-time">
              <time id="ctiv-question-time-elapsed">00:00</time>
              <span> / </span>
              <time id="ctiv-question-duration">00:00</time>
            </div>
          </div>
          <div id="ctiv-question-editing-right-controls">
            <div id="ctiv-question-volume-controls">
              <button
                data-title="Mute"
                id="ctiv-question-toggle-mute"
                class="ctiv-control-button"
              >
                <svg class="ctiv-svg">
                  <use
                    id="ctiv-question-volume-mute-icon"
                    class="ctiv-hidden"
                    href="#ctiv-volume-mute-icon"
                  ></use>
                  <use
                    id="ctiv-question-volume-low-icon"
                    class="ctiv-hidden"
                    href="#ctiv-volume-low-icon"
                  ></use>
                  <use
                    id="ctiv-question-volume-high-icon"
                    href="#ctiv-volume-high-icon"
                  ></use>
                </svg>
              </button>
              <input
                id="ctiv-question-volume"
                class="ctiv-slider"
                value="1"
                data-mute="0.5"
                type="range"
                max="1"
                min="0"
                step="0.01"
              />
            </div>
            <button
              data-title="Full screen"
              id="ctiv-question-toggle-fullscreen"
              class="ctiv-control-button"
            >
              <svg class="ctiv-svg">
                <use
                  id="ctiv-question-fullscreen-icon"
                  href="#ctiv-fullscreen-icon"
                ></use>
                <use
                  id="ctiv-question-exit-fullscreen-icon"
                  href="#ctiv-fullscreen-exit-icon"
                  class="ctiv-hidden"
                ></use>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>';
  return $markup;
}

function get_default_upload_before() {
  $markup = '
  <strong>All questions answered.</strong>
  <br>
  You may use the above navigation bar to playback your answers and retake if necessary.';
  return $markup;
}
function get_default_upload_after() {
  $markup = 'Your answers have been submitted successfully, and you can now close this page.';
  return $markup;
}
function get_upload_section($upload_button, $before, $after){
    $markup = '
    <div id="ctiv-upload-section" class="ctiv-hidden ctiv-section">
      <div id="ctiv-upload-before">
        <div>' . $before . '</div>
        <button id="ctiv-upload-button" class="ctiv-button">' . $upload_button . '</button>
      </div>
      <div id="ctiv-upload-waiting" class="ctiv-hidden">
        <div id="ctiv-upload-spinner"></div>
        <h5>' . _x('Please leave this page open until the upload is finished.', 'Frontend: Wait for upload message', 'cliptakes') . '</h5>
      </div>      
      <div id="ctiv-upload-error" class="ctiv-hidden">
        <h5>' . _x('Something went wrong.', 'Frontend: Error message title', 'cliptakes') . '</h5>
        <div>' . _x('Please navigate back through your answers to ensure all recordings have been completed and check your internet connection before retrying the upload.', 'Frontend: Upload failed error message', 'cliptakes') . '<br>
        <br>
        <p id="ctiv-upload-error-message"></p>
        </div>
      </div>
      <div id="ctiv-upload-after" class="ctiv-hidden">' . $after . '</div>
    </div>';
    return $markup;
}

function get_svg_elements(){
    $markup = '
    <svg style="display: none">
      <defs>
        <symbol id="ctiv-next-arrow-icon" viewBox="0 0 24 24">
          <path d="M2 12h 20M12 4l10 8l-10 8" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
        </symbol>
        <symbol id="ctiv-back-arrow-icon" viewBox="0 0 24 24">
          <path d="M2 12h 20M12 4l-10 8l10 8" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
        </symbol>

        <symbol id="ctiv-retake-icon-path" viewBox="0 0 24 24">
          <path stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"
            d="M18 19A 9.5 9.5 0 1 1 18 5M18 5.5l3.5-3v9l-7-3l3.5-3l1.5 1.5"
          />
        </symbol>

        <symbol id="ctiv-camera-icon" viewBox="0 0 24 24">
          <path
            d="M1 8a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z M18 12v-3l3-2c2 -1 2 0 2 2v3 M18 12v3l3 2c2  1 2 0 2 -2v-3"
          />
        </symbol>
        <symbol id="ctiv-microphone-icon" viewBox="0 0 24 24">
          <path
            d="M12,18 v5 M6 14 c0 3 3 5 6 5 3 0 6 -2 6 -5 M12,3 c 1.804167,0 3,1.1264585 3,2 v 8 c 0,0.873542 -1.195833,2 -3,2 -1.804167,0 -3,-1.126458 -3,-2 V 5 C 9,4.1264585 10.195833,3 12,3 Z"
          />
        </symbol>

        <symbol id="ctiv-pause-icon" viewBox="0 0 24 24">
          <path d="M14 5h4v14h-4v-14zM6 19v-14h4v14h-4z"></path>
        </symbol>
        <symbol id="ctiv-play-icon" viewBox="0 0 24 24">
          <path d="M8 5l11 7-11 7v-14z"></path>
        </symbol>

        <symbol id="ctiv-volume-high-icon" viewBox="0 0 24 24">
          <path
            d="M3 9h4l5-5v15l-5-5h-4v-5zM16.5 11.5q0 3-2.5 4v-4zM16.5 11.5q0 -3-2.5 -4v4zM20.5 11.5q0-6 -6.5 -8v2c2 0.5 4.5 2 4.75 6zM20.5 11.5q0 6 -6.5 8v-2c2 -0.5 4.5 -2 4.75 -6z"
          ></path>
        </symbol>
        <symbol id="ctiv-volume-low-icon" viewBox="0 0 24 24">
          <path
            d="M3 9h4l5-5v15l-5-5h-4v-5zM16.5 11.5q0 3-2.5 4v-4zM16.5 11.5q0 -3-2.5 -4v4z"
          ></path>
        </symbol>
        <symbol id="ctiv-volume-mute-icon" viewBox="0 0 24 24">
          <path
            d="M3 9h4l5-5v15l-5-5h-4v-5zM16.5 11.5q0 3-2.5 4v-4zM16.5 11.5q0 -3-2.5 -4v4zM20.5 11.5q0-6 -6.5 -8v2c2 0.5 4.5 2 4.75 6zM20.5 11.5q0 6 -6.5 8v-2c2 -0.5 4.5 -2 4.75 -6zM6 4l16 16l-3 0l-16-16z"
          ></path>
        </symbol>

        <symbol id="ctiv-fullscreen-icon" viewBox="0 0 24 24">
          <path
            d="M14 5h5v5h-2v-3h-3v-2zM17 17v-3h2v5h-5v-2h3zM5 10v-5h5v2h-3v3h-2zM7 14v3h3v2h-5v-5h2z"
          ></path>
        </symbol>
        <symbol id="ctiv-fullscreen-exit-icon" viewBox="0 0 24 24">
          <path
            d="M16 8h3v2h-5v-5h2v3zM14 19v-5h5v2h-3v3h-2zM8 8v-3h2v5h-5v-2h3zM5 16v-2h5v5h-2v-3h-3z"
          ></path>
        </symbol>

        <symbol id="ctiv-tick-icon" viewBox="0 0 24 24">
          <path d="M2 12l6 6l12-12" fill="none" stroke="#00dd00"
            stroke-width="4" stroke-linecap="round" stroke-linejoin="round"></path>
        </symbol>
        <symbol id="ctiv-rotate-device-icon" viewBox="0 0 24 24">
          <path d="m1 4v16c0 1 0 1 1 1v-16h8v14h-8v2h8c1 0 1 0 1-1v-16c0-1 0-1-1-1h-8c-1 0-1 0-1 1v-1" fill="#b3b3b3"/>
          <path d="m22 11h-16c-1 0-1 0-1 1h16v8h-14v-8h-2v8c0 1 0 1 1 1h16c1 0 1 0 1-1v-8c0-1 0-1-1-1h1" fill="#1a1a1a"/>
          <path d="m17 8h2l-1 2-1-2" fill="#1a1a1a"/>
          <path d="M13 4 a 5,4 0 0 1 5,4" fill="none" stroke="#1a1a1a" stroke-linecap="round" stroke-width=".75"/>
        </symbol>
      </defs>
    </svg>';
  return $markup;
}
?>
