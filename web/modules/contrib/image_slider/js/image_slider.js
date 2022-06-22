/**
 * @file
 * JavaScript behaviors for the front-end display of slider.
 */

(function ($) {

  jssor_1_slider_init = function () {
    var jssor_1_SlideoTransitions = [
      [{b: 500, d: 1000, x: 0, e: {x: 6}}],
      [{b: -1, d: 1, x: 100, p: {x: {d: 1, dO: 9}}}, {b: 0, d: 2000, x: 0, e: {x: 6}, p: {x: {dl: 0.1}}}],
      [{b: -1, d: 1, x: 200, p: {x: {d: 1, dO: 9}}}, {b: 0, d: 2000, x: 0, e: {x: 6}, p: {x: {dl: 0.1}}}],
      [{b: -1, d: 1, rX: 20, rY: 90}, {b: 0, d: 4000, rX: 0, e: {rX: 1}}],
      [{b: -1, d: 1, rY: -20}, {b: 0, d: 4000, rY: -90, e: {rY: 7}}],
      [{b: -1, d: 1, sX: 2, sY: 2}, {b: 1000, d: 3000, sX: 1, sY: 1, e: {sX: 1, sY: 1}}],
      [{b: -1, d: 1, sX: 2, sY: 2}, {b: 1000, d: 5000, sX: 1, sY: 1, e: {sX: 3, sY: 3}}],
      [{b: -1, d: 1, tZ: 300}, {b: 0, d: 2000, o: 1}, {b: 3500, d: 3500, tZ: 0, e: {tZ: 1}}],
      [{b: -1, d: 1, x: 20, p: {x: {o: 33, r: 0.5}}}, {b: 0, d: 1000, x: 0, o: 0.5, e: {x: 3, o: 1}, p: {x: {dl: 0.05, o: 33}, o: {dl: 0.02, o: 68, rd: 2}}}, {b: 1000, d: 1000, o: 1, e: {o: 1}, p: {o: {dl: 0.05, o: 68, rd: 2}}}],
      [{b: -1, d: 1, da: [0, 700]}, {b: 0, d: 600, da: [700, 700], e: {da: 1}}],
      [{b: 600, d: 1000, o: 0.4}],
      [{b: -1, d: 1, da: [0, 400]}, {b: 200, d: 600, da: [400, 400], e: {da: 1}}],
      [{b: 800, d: 1000, o: 0.4}],
      [{b: -1, d: 1, sX: 1.1, sY: 1.1}, {b: 0, d: 1600, o: 1}, {b: 1600, d: 5000, sX: 0.9, sY: 0.9, e: {sX: 1, sY: 1}}],
      [{b: 0, d: 1000, o: 1, p: {o: {o: 4}}}],
      [{b: 1000, d: 1000, o: 1, p: {o: {o: 4}}}]
    ];

    var jssor_1_options = {
      $AutoPlay: 1,
      $CaptionSliderOptions: {
        $Class: $JssorCaptionSlideo$,
        $Transitions: jssor_1_SlideoTransitions
      },
      $ArrowNavigatorOptions: {
        $Class: $JssorArrowNavigator$
      },
      $BulletNavigatorOptions: {
        $Class: $JssorBulletNavigator$,
        $SpacingX: 16,
        $SpacingY: 16
      }
    };

    var jssor_1_slider = new $JssorSlider$('jssor_1', jssor_1_options);
    var MAX_WIDTH = 980;

    function ScaleSlider() {
      var containerElement = jssor_1_slider.$Elmt.parentNode;
      var containerWidth = containerElement.clientWidth;

      if (containerWidth) {

        var expectedWidth = Math.min(MAX_WIDTH || containerWidth, containerWidth);

        jssor_1_slider.$ScaleWidth(expectedWidth);
      } else {
        window.setTimeout(ScaleSlider, 30);
      }
    }

    ScaleSlider();

    $Jssor$.$AddEvent(window, 'load', ScaleSlider);
    $Jssor$.$AddEvent(window, 'resize', ScaleSlider);
    $Jssor$.$AddEvent(window, 'orientationchange', ScaleSlider);

  };

  // Image slider.
  jssor_2_slider_init = function () {

    var jssor_2_SlideshowTransitions = [
      {$Duration: 800, x: 0.3, $During: {$Left: [0.3, 0.7]}, $Easing: {$Left: $Jease$.$InCubic, $Opacity: $Jease$.$Linear}, $Opacity: 2},
      {$Duration: 800, x: -0.3, $SlideOut: true, $Easing: {$Left: $Jease$.$InCubic, $Opacity: $Jease$.$Linear}, $Opacity: 2},
      {$Duration: 800, x: -0.3, $During: {$Left: [0.3, 0.7]}, $Easing: {$Left: $Jease$.$InCubic, $Opacity: $Jease$.$Linear}, $Opacity: 2},
      {$Duration: 800, x: 0.3, $SlideOut: true, $Easing: {$Left: $Jease$.$InCubic, $Opacity: $Jease$.$Linear}, $Opacity: 2},
      {$Duration: 800, y: 0.3, $During: {$Top: [0.3, 0.7]}, $Easing: {$Top: $Jease$.$InCubic, $Opacity: $Jease$.$Linear}, $Opacity: 2},
      {$Duration: 800, y: -0.3, $SlideOut: true, $Easing: {$Top: $Jease$.$InCubic, $Opacity: $Jease$.$Linear}, $Opacity: 2},
      {$Duration: 800, y: -0.3, $During: {$Top: [0.3, 0.7]}, $Easing: {$Top: $Jease$.$InCubic, $Opacity: $Jease$.$Linear}, $Opacity: 2},
      {$Duration: 800, y: 0.3, $SlideOut: true, $Easing: {$Top: $Jease$.$InCubic, $Opacity: $Jease$.$Linear}, $Opacity: 2},
      {$Duration: 800, x: 0.3, $Cols: 2, $During: {$Left: [0.3, 0.7]}, $ChessMode: {$Column: 3}, $Easing: {$Left: $Jease$.$InCubic, $Opacity: $Jease$.$Linear}, $Opacity: 2},
      {$Duration: 800, x: 0.3, $Cols: 2, $SlideOut: true, $ChessMode: {$Column: 3}, $Easing: {$Left: $Jease$.$InCubic, $Opacity: $Jease$.$Linear}, $Opacity: 2},
      {$Duration: 800, y: 0.3, $Rows: 2, $During: {$Top: [0.3, 0.7]}, $ChessMode: {$Row: 12}, $Easing: {$Top: $Jease$.$InCubic, $Opacity: $Jease$.$Linear}, $Opacity: 2},
      {$Duration: 800, y: 0.3, $Rows: 2, $SlideOut: true, $ChessMode: {$Row: 12}, $Easing: {$Top: $Jease$.$InCubic, $Opacity: $Jease$.$Linear}, $Opacity: 2},
      {$Duration: 800, y: 0.3, $Cols: 2, $During: {$Top: [0.3, 0.7]}, $ChessMode: {$Column: 12}, $Easing: {$Top: $Jease$.$InCubic, $Opacity: $Jease$.$Linear}, $Opacity: 2},
      {$Duration: 800, y: -0.3, $Cols: 2, $SlideOut: true, $ChessMode: {$Column: 12}, $Easing: {$Top: $Jease$.$InCubic, $Opacity: $Jease$.$Linear}, $Opacity: 2},
      {$Duration: 800, x: 0.3, $Rows: 2, $During: {$Left: [0.3, 0.7]}, $ChessMode: {$Row: 3}, $Easing: {$Left: $Jease$.$InCubic, $Opacity: $Jease$.$Linear}, $Opacity: 2},
      {$Duration: 800, x: -0.3, $Rows: 2, $SlideOut: true, $ChessMode: {$Row: 3}, $Easing: {$Left: $Jease$.$InCubic, $Opacity: $Jease$.$Linear}, $Opacity: 2},
      {$Duration: 800, x: 0.3, y: 0.3, $Cols: 2, $Rows: 2, $During: {$Left: [0.3, 0.7], $Top: [0.3, 0.7]}, $ChessMode: {$Column: 3, $Row: 12}, $Easing: {$Left: $Jease$.$InCubic, $Top: $Jease$.$InCubic, $Opacity: $Jease$.$Linear}, $Opacity: 2},
      {$Duration: 800, x: 0.3, y: 0.3, $Cols: 2, $Rows: 2, $During: {$Left: [0.3, 0.7], $Top: [0.3, 0.7]}, $SlideOut: true, $ChessMode: {$Column: 3, $Row: 12}, $Easing: {$Left: $Jease$.$InCubic, $Top: $Jease$.$InCubic, $Opacity: $Jease$.$Linear}, $Opacity: 2},
      {$Duration: 800, $Delay: 20, $Clip: 3, $Assembly: 260, $Easing: {$Clip: $Jease$.$InCubic, $Opacity: $Jease$.$Linear}, $Opacity: 2},
      {$Duration: 800, $Delay: 20, $Clip: 3, $SlideOut: true, $Assembly: 260, $Easing: {$Clip: $Jease$.$OutCubic, $Opacity: $Jease$.$Linear}, $Opacity: 2},
      {$Duration: 800, $Delay: 20, $Clip: 12, $Assembly: 260, $Easing: {$Clip: $Jease$.$InCubic, $Opacity: $Jease$.$Linear}, $Opacity: 2},
      {$Duration: 800, $Delay: 20, $Clip: 12, $SlideOut: true, $Assembly: 260, $Easing: {$Clip: $Jease$.$OutCubic, $Opacity: $Jease$.$Linear}, $Opacity: 2}
    ];

    var jssor_2_options = {
      $AutoPlay: 1,
      $SlideshowOptions: {
        $Class: $JssorSlideshowRunner$,
        $Transitions: jssor_2_SlideshowTransitions,
        $TransitionsOrder: 1
      },
      $ArrowNavigatorOptions: {
        $Class: $JssorArrowNavigator$
      },
      $ThumbnailNavigatorOptions: {
        $Class: $JssorThumbnailNavigator$,
        $SpacingX: 5,
        $SpacingY: 5
      }
    };

    var jssor_2_slider = new $JssorSlider$('jssor_2', jssor_2_options);
    var MAX_WIDTH = 980;

    function ScaleSlider() {
      var containerElement = jssor_2_slider.$Elmt.parentNode;
      var containerWidth = containerElement.clientWidth;

      if (containerWidth) {

        var expectedWidth = Math.min(MAX_WIDTH || containerWidth, containerWidth);

        jssor_2_slider.$ScaleWidth(expectedWidth);
      } else {
        window.setTimeout(ScaleSlider, 30);
      }
    }

    ScaleSlider();

    $Jssor$.$AddEvent(window, 'load', ScaleSlider);
    $Jssor$.$AddEvent(window, 'resize', ScaleSlider);
    $Jssor$.$AddEvent(window, 'orientationchange', ScaleSlider);

  };

  jssor_3_slider_init = function () {

    var jssor_3_SlideshowTransitions = [
      {$Duration: 500, $Delay: 12, $Cols: 10, $Rows: 5, $Opacity: 2, $Clip: 15, $SlideOut: true, $Formation: $JssorSlideshowFormations$.$FormationStraightStairs, $Assembly: 2049, $Easing: $Jease$.$OutQuad},
      {$Duration: 500, $Delay: 40, $Cols: 10, $Rows: 5, $Opacity: 2, $Clip: 15, $SlideOut: true, $Easing: $Jease$.$OutQuad},
      {$Duration: 1000, x: -0.2, $Delay: 20, $Cols: 16, $SlideOut: true, $Formation: $JssorSlideshowFormations$.$FormationStraight, $Assembly: 260, $Easing: {$Left: $Jease$.$InOutExpo, $Opacity: $Jease$.$InOutQuad}, $Opacity: 2, $Outside: true, $Round: {$Top: 0.5}},
      {$Duration: 1600, y: -1, $Delay: 40, $Cols: 24, $SlideOut: true, $Formation: $JssorSlideshowFormations$.$FormationStraight, $Easing: $Jease$.$OutJump, $Round: {$Top: 1.5}},
      {$Duration: 1200, x: 0.2, y: -0.1, $Delay: 16, $Cols: 10, $Rows: 5, $Opacity: 2, $Clip: 15, $During: {$Left: [0.3, 0.7], $Top: [0.3, 0.7]}, $Formation: $JssorSlideshowFormations$.$FormationStraightStairs, $Assembly: 260, $Easing: {$Left: $Jease$.$InWave, $Top: $Jease$.$InWave, $Clip: $Jease$.$OutQuad}, $Round: {$Left: 1.3, $Top: 2.5}},
      {$Duration: 1500, x: 0.3, y: -0.3, $Delay: 20, $Cols: 10, $Rows: 5, $Opacity: 2, $Clip: 15, $During: {$Left: [0.2, 0.8], $Top: [0.2, 0.8]}, $Formation: $JssorSlideshowFormations$.$FormationStraightStairs, $Assembly: 260, $Easing: {$Left: $Jease$.$InJump, $Top: $Jease$.$InJump, $Clip: $Jease$.$OutQuad}, $Round: {$Left: 0.8, $Top: 2.5}},
      {$Duration: 1500, x: 0.3, y: -0.3, $Delay: 20, $Cols: 10, $Rows: 5, $Opacity: 2, $Clip: 15, $During: {$Left: [0.1, 0.9], $Top: [0.1, 0.9]}, $SlideOut: true, $Formation: $JssorSlideshowFormations$.$FormationStraightStairs, $Assembly: 260, $Easing: {$Left: $Jease$.$InJump, $Top: $Jease$.$InJump, $Clip: $Jease$.$OutQuad}, $Round: {$Left: 0.8, $Top: 2.5}}
    ];

    var jssor_3_options = {
      $AutoPlay: 1,
      $SlideshowOptions: {
        $Class: $JssorSlideshowRunner$,
        $Transitions: jssor_3_SlideshowTransitions,
        $TransitionsOrder: 1
      },
      $ArrowNavigatorOptions: {
        $Class: $JssorArrowNavigator$
      },
      $BulletNavigatorOptions: {
        $Class: $JssorBulletNavigator$,
        $SpacingX: 16,
        $SpacingY: 16
      }
    };

    var jssor_3_slider = new $JssorSlider$('jssor_3', jssor_3_options);
    var MAX_WIDTH = 980;

    function ScaleSlider() {
      var containerElement = jssor_3_slider.$Elmt.parentNode;
      var containerWidth = containerElement.clientWidth;

      if (containerWidth) {

        var expectedWidth = Math.min(MAX_WIDTH || containerWidth, containerWidth);

        jssor_3_slider.$ScaleWidth(expectedWidth);
      } else {
        window.setTimeout(ScaleSlider, 30);
      }
    }

    ScaleSlider();

    $Jssor$.$AddEvent(window, 'load', ScaleSlider);
    $Jssor$.$AddEvent(window, 'resize', ScaleSlider);
    $Jssor$.$AddEvent(window, 'orientationchange', ScaleSlider);

  };

  // Banner slider.
  jssor_4_slider_init = function () {

    var jssor_4_SlideshowTransitions = [
      {$Duration: 800, x: -0.3, $During: {$Left: [0.3, 0.7]}, $Easing: {$Left: $Jease$.$InCubic, $Opacity: $Jease$.$Linear}, $Opacity: 2},
      {$Duration: 800, x: 0.3, $SlideOut: true, $Easing: {$Left: $Jease$.$InCubic, $Opacity: $Jease$.$Linear}, $Opacity: 2}
    ];

    var jssor_4_options = {
      $AutoPlay: 1,
      $SlideshowOptions: {
        $Class: $JssorSlideshowRunner$,
        $Transitions: jssor_4_SlideshowTransitions,
        $TransitionsOrder: 1
      },
      $ArrowNavigatorOptions: {
        $Class: $JssorArrowNavigator$
      },
      $ThumbnailNavigatorOptions: {
        $Class: $JssorThumbnailNavigator$,
        $Orientation: 2,
        $NoDrag: true
      }
    };

    var jssor_4_slider = new $JssorSlider$('jssor_4', jssor_4_options);
    var MAX_WIDTH = 980;

    function ScaleSlider() {
      var containerElement = jssor_4_slider.$Elmt.parentNode;
      var containerWidth = containerElement.clientWidth;

      if (containerWidth) {

        var expectedWidth = Math.min(MAX_WIDTH || containerWidth, containerWidth);

        jssor_4_slider.$ScaleWidth(expectedWidth);
      } else {
        window.setTimeout(ScaleSlider, 30);
      }
    }

    ScaleSlider();

    $Jssor$.$AddEvent(window, 'load', ScaleSlider);
    $Jssor$.$AddEvent(window, 'resize', ScaleSlider);
    $Jssor$.$AddEvent(window, 'orientationchange', ScaleSlider);

  };

  // Carousel.
  jssor_5_slider_init = function () {

    var jssor_5_options = {
      $AutoPlay: 1,
      $AutoPlaySteps: 5,
      $SlideDuration: 160,
      $SlideWidth: 200,
      $SlideSpacing: 3,
      $ArrowNavigatorOptions: {
        $Class: $JssorArrowNavigator$,
        $Steps: 5
      },
      $BulletNavigatorOptions: {
        $Class: $JssorBulletNavigator$,
        $SpacingX: 16,
        $SpacingY: 16
      }
    };

    var jssor_5_slider = new $JssorSlider$('jssor_5', jssor_5_options);
    var MAX_WIDTH = 980;

    function ScaleSlider() {
      var containerElement = jssor_5_slider.$Elmt.parentNode;
      var containerWidth = containerElement.clientWidth;

      if (containerWidth) {

        var expectedWidth = Math.min(MAX_WIDTH || containerWidth, containerWidth);

        jssor_5_slider.$ScaleWidth(expectedWidth);
      } else {
        window.setTimeout(ScaleSlider, 30);
      }
    }

    ScaleSlider();

    $Jssor$.$AddEvent(window, 'load', ScaleSlider);
    $Jssor$.$AddEvent(window, 'resize', ScaleSlider);
    $Jssor$.$AddEvent(window, 'orientationchange', ScaleSlider);

  };

  // Partial slider.
  jssor_6_slider_init = function () {

    var jssor_6_options = {
      $AutoPlay: 1,
      $SlideWidth: 720,
      $ArrowNavigatorOptions: {
        $Class: $JssorArrowNavigator$
      },
      $BulletNavigatorOptions: {
        $Class: $JssorBulletNavigator$,
        $SpacingX: 16,
        $SpacingY: 16
      }
    };

    var jssor_6_slider = new $JssorSlider$('jssor_6', jssor_6_options);
    var MAX_WIDTH = 980;

    function ScaleSlider() {
      var containerElement = jssor_6_slider.$Elmt.parentNode;
      var containerWidth = containerElement.clientWidth;

      if (containerWidth) {

        var expectedWidth = Math.min(MAX_WIDTH || containerWidth, containerWidth);

        jssor_6_slider.$ScaleWidth(expectedWidth);
      } else {
        window.setTimeout(ScaleSlider, 30);
      }
    }

    ScaleSlider();

    $Jssor$.$AddEvent(window, 'load', ScaleSlider);
    $Jssor$.$AddEvent(window, 'resize', ScaleSlider);
    $Jssor$.$AddEvent(window, 'orientationchange', ScaleSlider);

  };

  // Verical slider with thumb.
  jssor_7_slider_init = function () {

    var jssor_7_SlideshowTransitions = [
      {$Duration: 1200, $Zoom: 1, $Easing: {$Zoom: $Jease$.$InCubic, $Opacity: $Jease$.$OutQuad}, $Opacity: 2},
      {$Duration: 1000, $Zoom: 11, $SlideOut: true, $Easing: {$Zoom: $Jease$.$InExpo, $Opacity: $Jease$.$Linear}, $Opacity: 2},
      {$Duration: 1200, $Zoom: 1, $Rotate: 1, $During: {$Zoom: [0.2, 0.8], $Rotate: [0.2, 0.8]}, $Easing: {$Zoom: $Jease$.$Swing, $Opacity: $Jease$.$Linear, $Rotate: $Jease$.$Swing}, $Opacity: 2, $Round: {$Rotate: 0.5}},
      {$Duration: 1000, $Zoom: 11, $Rotate: 1, $SlideOut: true, $Easing: {$Zoom: $Jease$.$InQuint, $Opacity: $Jease$.$Linear, $Rotate: $Jease$.$InQuint}, $Opacity: 2, $Round: {$Rotate: 0.8}},
      {$Duration: 1200, x: 0.5, $Cols: 2, $Zoom: 1, $Assembly: 2049, $ChessMode: {$Column: 15}, $Easing: {$Left: $Jease$.$InCubic, $Zoom: $Jease$.$InCubic, $Opacity: $Jease$.$Linear}, $Opacity: 2},
      {$Duration: 1200, x: 4, $Cols: 2, $Zoom: 11, $SlideOut: true, $Assembly: 2049, $ChessMode: {$Column: 15}, $Easing: {$Left: $Jease$.$InExpo, $Zoom: $Jease$.$InExpo, $Opacity: $Jease$.$Linear}, $Opacity: 2},
      {$Duration: 1200, x: 0.6, $Zoom: 1, $Rotate: 1, $During: {$Left: [0.2, 0.8], $Zoom: [0.2, 0.8], $Rotate: [0.2, 0.8]}, $Opacity: 2, $Round: {$Rotate: 0.5}},
      {$Duration: 1000, x: -4, $Zoom: 11, $Rotate: 1, $SlideOut: true, $Easing: {$Left: $Jease$.$InQuint, $Zoom: $Jease$.$InQuart, $Opacity: $Jease$.$Linear, $Rotate: $Jease$.$InQuint}, $Opacity: 2, $Round: {$Rotate: 0.8}},
      {$Duration: 1200, x: -0.6, $Zoom: 1, $Rotate: 1, $During: {$Left: [0.2, 0.8], $Zoom: [0.2, 0.8], $Rotate: [0.2, 0.8]}, $Opacity: 2, $Round: {$Rotate: 0.5}},
      {$Duration: 1000, x: 4, $Zoom: 11, $Rotate: 1, $SlideOut: true, $Easing: {$Left: $Jease$.$InQuint, $Zoom: $Jease$.$InQuart, $Opacity: $Jease$.$Linear, $Rotate: $Jease$.$InQuint}, $Opacity: 2, $Round: {$Rotate: 0.8}},
      {$Duration: 1200, x: 0.5, y: 0.3, $Cols: 2, $Zoom: 1, $Rotate: 1, $Assembly: 2049, $ChessMode: {$Column: 15}, $Easing: {$Left: $Jease$.$InCubic, $Top: $Jease$.$InCubic, $Zoom: $Jease$.$InCubic, $Opacity: $Jease$.$OutQuad, $Rotate: $Jease$.$InCubic}, $Opacity: 2, $Round: {$Rotate: 0.7}},
      {$Duration: 1000, x: 0.5, y: 0.3, $Cols: 2, $Zoom: 1, $Rotate: 1, $SlideOut: true, $Assembly: 2049, $ChessMode: {$Column: 15}, $Easing: {$Left: $Jease$.$InExpo, $Top: $Jease$.$InExpo, $Zoom: $Jease$.$InExpo, $Opacity: $Jease$.$Linear, $Rotate: $Jease$.$InExpo}, $Opacity: 2, $Round: {$Rotate: 0.7}},
      {$Duration: 1200, x: -4, y: 2, $Rows: 2, $Zoom: 11, $Rotate: 1, $Assembly: 2049, $ChessMode: {$Row: 28}, $Easing: {$Left: $Jease$.$InCubic, $Top: $Jease$.$InCubic, $Zoom: $Jease$.$InCubic, $Opacity: $Jease$.$OutQuad, $Rotate: $Jease$.$InCubic}, $Opacity: 2, $Round: {$Rotate: 0.7}},
      {$Duration: 1200, x: 1, y: 2, $Cols: 2, $Zoom: 11, $Rotate: 1, $Assembly: 2049, $ChessMode: {$Column: 19}, $Easing: {$Left: $Jease$.$InCubic, $Top: $Jease$.$InCubic, $Zoom: $Jease$.$InCubic, $Opacity: $Jease$.$OutQuad, $Rotate: $Jease$.$InCubic}, $Opacity: 2, $Round: {$Rotate: 0.8}}
    ];

    var jssor_7_options = {
      $AutoPlay: 1,
      $SlideshowOptions: {
        $Class: $JssorSlideshowRunner$,
        $Transitions: jssor_7_SlideshowTransitions,
        $TransitionsOrder: 1
      },
      $ArrowNavigatorOptions: {
        $Class: $JssorArrowNavigator$
      },
      $ThumbnailNavigatorOptions: {
        $Class: $JssorThumbnailNavigator$,
        $Rows: 2,
        $SpacingX: 14,
        $SpacingY: 12,
        $Orientation: 2,
        $Align: 156
      }
    };

    var jssor_7_slider = new $JssorSlider$('jssor_7', jssor_7_options);
    var MAX_WIDTH = 960;

    function ScaleSlider() {
      var containerElement = jssor_7_slider.$Elmt.parentNode;
      var containerWidth = containerElement.clientWidth;

      if (containerWidth) {

        var expectedWidth = Math.min(MAX_WIDTH || containerWidth, containerWidth);

        jssor_7_slider.$ScaleWidth(expectedWidth);
      } else {
        window.setTimeout(ScaleSlider, 30);
      }
    }

    ScaleSlider();

    $Jssor$.$AddEvent(window, 'load', ScaleSlider);
    $Jssor$.$AddEvent(window, 'resize', ScaleSlider);
    $Jssor$.$AddEvent(window, 'orientationchange', ScaleSlider);

  };

  // Scrolling logo thumbnail slider.
  jssor_8_slider_init = function () {

    var jssor_8_options = {
      $AutoPlay: 1,
      $Idle: 0,
      $SlideDuration: 5000,
      $SlideEasing: $Jease$.$Linear,
      $PauseOnHover: 4,
      $SlideWidth: 140,
      $Align: 0
    };

    var jssor_8_slider = new $JssorSlider$('jssor_8', jssor_8_options);
    var MAX_WIDTH = 980;

    function ScaleSlider() {
      var containerElement = jssor_8_slider.$Elmt.parentNode;
      var containerWidth = containerElement.clientWidth;

      if (containerWidth) {

        var expectedWidth = Math.min(MAX_WIDTH || containerWidth, containerWidth);

        jssor_8_slider.$ScaleWidth(expectedWidth);
      } else {
        window.setTimeout(ScaleSlider, 30);
      }
    }

    ScaleSlider();

    $Jssor$.$AddEvent(window, 'load', ScaleSlider);
    $Jssor$.$AddEvent(window, 'resize', ScaleSlider);
    $Jssor$.$AddEvent(window, 'orientationchange', ScaleSlider);

  };

  // Full width slider.
  jssor_9_slider_init = function () {

    var jssor_9_SlideoTransitions = [
      [{b: -1, d: 1, ls: 0.5}, {b: 0, d: 1000, y: 5, e: {y: 6}}],
      [{b: -1, d: 1, ls: 0.5}, {b: 200, d: 1000, y: 25, e: {y: 6}}],
      [{b: -1, d: 1, ls: 0.5}, {b: 400, d: 1000, y: 45, e: {y: 6}}],
      [{b: -1, d: 1, ls: 0.5}, {b: 600, d: 1000, y: 65, e: {y: 6}}],
      [{b: -1, d: 1, ls: 0.5}, {b: 800, d: 1000, y: 85, e: {y: 6}}],
      [{b: -1, d: 1, ls: 0.5}, {b: 500, d: 1000, y: 195, e: {y: 6}}],
      [{b: 0, d: 2000, y: 30, e: {y: 3}}],
      [{b: -1, d: 1, rY: -15, tZ: 100}, {b: 0, d: 1500, y: 30, o: 1, e: {y: 3}}],
      [{b: -1, d: 1, rY: -15, tZ: -100}, {b: 0, d: 1500, y: 100, o: 0.8, e: {y: 3}}],
      [{b: 500, d: 1500, o: 1}],
      [{b: 0, d: 1000, y: 380, e: {y: 6}}],
      [{b: 300, d: 1000, x: 80, e: {x: 6}}],
      [{b: 300, d: 1000, x: 330, e: {x: 6}}],
      [{b: -1, d: 1, r: -110, sX: 5, sY: 5}, {b: 0, d: 2000, o: 1, r: -20, sX: 1, sY: 1, e: {o: 6, r: 6, sX: 6, sY: 6}}],
      [{b: 0, d: 600, x: 150, o: 0.5, e: {x: 6}}],
      [{b: 0, d: 600, x: 1140, o: 0.6, e: {x: 6}}],
      [{b: -1, d: 1, sX: 5, sY: 5}, {b: 600, d: 600, o: 1, sX: 1, sY: 1, e: {sX: 3, sY: 3}}]
    ];

    var jssor_9_options = {
      $AutoPlay: 1,
      $LazyLoading: 1,
      $CaptionSliderOptions: {
        $Class: $JssorCaptionSlideo$,
        $Transitions: jssor_9_SlideoTransitions
      },
      $ArrowNavigatorOptions: {
        $Class: $JssorArrowNavigator$
      },
      $BulletNavigatorOptions: {
        $Class: $JssorBulletNavigator$,
        $SpacingX: 20,
        $SpacingY: 20
      }
    };

    var jssor_9_slider = new $JssorSlider$('jssor_9', jssor_9_options);
    var MAX_WIDTH = 1600;

    function ScaleSlider() {
      var containerElement = jssor_9_slider.$Elmt.parentNode;
      var containerWidth = containerElement.clientWidth;

      if (containerWidth) {

        var expectedWidth = Math.min(MAX_WIDTH || containerWidth, containerWidth);

        jssor_9_slider.$ScaleWidth(expectedWidth);
      } else {
        window.setTimeout(ScaleSlider, 30);
      }
    }

    ScaleSlider();

    $Jssor$.$AddEvent(window, 'load', ScaleSlider);
    $Jssor$.$AddEvent(window, 'resize', ScaleSlider);
    $Jssor$.$AddEvent(window, 'orientationchange', ScaleSlider);


  };

  jssor_10_slider_init = function () {

    var jssor_10_SlideshowTransitions = [
      {$Duration: 800, x: 0.25, $Zoom: 1.5, $Easing: {$Left: $Jease$.$InWave, $Zoom: $Jease$.$InCubic}, $Opacity: 2, $ZIndex: -10, $Brother: {$Duration: 800, x: -0.25, $Zoom: 1.5, $Easing: {$Left: $Jease$.$InWave, $Zoom: $Jease$.$InCubic}, $Opacity: 2, $ZIndex: -10}},
      {$Duration: 1200, x: 0.5, $Cols: 2, $ChessMode: {$Column: 3}, $Easing: {$Left: $Jease$.$InOutCubic}, $Opacity: 2, $Brother: {$Duration: 1200, $Opacity: 2}},
      {$Duration: 600, x: 0.3, $During: {$Left: [0.6, 0.4]}, $Easing: {$Left: $Jease$.$InCubic, $Opacity: $Jease$.$Linear}, $Opacity: 2, $Brother: {$Duration: 600, x: -0.3, $Easing: {$Left: $Jease$.$InCubic, $Opacity: $Jease$.$Linear}, $Opacity: 2}},
      {$Duration: 800, x: 0.25, y: 0.5, $Rotate: -0.1, $Easing: {$Left: $Jease$.$InQuad, $Top: $Jease$.$InQuad, $Opacity: $Jease$.$Linear, $Rotate: $Jease$.$InQuad}, $Opacity: 2, $Brother: {$Duration: 800, x: -0.1, y: -0.7, $Rotate: 0.1, $Easing: {$Left: $Jease$.$InQuad, $Top: $Jease$.$InQuad, $Opacity: $Jease$.$Linear, $Rotate: $Jease$.$InQuad}, $Opacity: 2}},
      {$Duration: 1000, x: 1, $Rows: 2, $ChessMode: {$Row: 3}, $Easing: {$Left: $Jease$.$InOutQuart, $Opacity: $Jease$.$Linear}, $Opacity: 2, $Brother: {$Duration: 1000, x: -1, $Rows: 2, $ChessMode: {$Row: 3}, $Easing: {$Left: $Jease$.$InOutQuart, $Opacity: $Jease$.$Linear}, $Opacity: 2}},
      {$Duration: 1000, y: -1, $Cols: 2, $ChessMode: {$Column: 12}, $Easing: {$Top: $Jease$.$InOutQuart, $Opacity: $Jease$.$Linear}, $Opacity: 2, $Brother: {$Duration: 1000, y: 1, $Cols: 2, $ChessMode: {$Column: 12}, $Easing: {$Top: $Jease$.$InOutQuart, $Opacity: $Jease$.$Linear}, $Opacity: 2}},
      {$Duration: 800, y: 1, $Easing: {$Top: $Jease$.$InOutQuart, $Opacity: $Jease$.$Linear}, $Opacity: 2, $Brother: {$Duration: 800, y: -1, $Easing: {$Top: $Jease$.$InOutQuart, $Opacity: $Jease$.$Linear}, $Opacity: 2}},
      {$Duration: 1000, x: -0.1, y: -0.7, $Rotate: 0.1, $During: {$Left: [0.6, 0.4], $Top: [0.6, 0.4], $Rotate: [0.6, 0.4]}, $Easing: {$Left: $Jease$.$InQuad, $Top: $Jease$.$InQuad, $Opacity: $Jease$.$Linear, $Rotate: $Jease$.$InQuad}, $Opacity: 2, $Brother: {$Duration: 1000, x: 0.2, y: 0.5, $Rotate: -0.1, $Easing: {$Left: $Jease$.$InQuad, $Top: $Jease$.$InQuad, $Opacity: $Jease$.$Linear, $Rotate: $Jease$.$InQuad}, $Opacity: 2}},
      {$Duration: 800, x: -0.2, $Delay: 40, $Cols: 12, $During: {$Left: [0.4, 0.6]}, $SlideOut: true, $Formation: $JssorSlideshowFormations$.$FormationStraight, $Assembly: 260, $Easing: {$Left: $Jease$.$InOutExpo, $Opacity: $Jease$.$InOutQuad}, $Opacity: 2, $Outside: true, $Round: {$Top: 0.5}, $Brother: {$Duration: 800, x: 0.2, $Delay: 40, $Cols: 12, $Formation: $JssorSlideshowFormations$.$FormationStraight, $Assembly: 1028, $Easing: {$Left: $Jease$.$InOutExpo, $Opacity: $Jease$.$InOutQuad}, $Opacity: 2, $Round: {$Top: 0.5}, $Shift: -200}},
      {$Duration: 700, $Opacity: 2, $Brother: {$Duration: 700, $Opacity: 2}},
      {$Duration: 800, x: 1, $Easing: {$Left: $Jease$.$InOutQuart, $Opacity: $Jease$.$Linear}, $Opacity: 2, $Brother: {$Duration: 800, x: -1, $Easing: {$Left: $Jease$.$InOutQuart, $Opacity: $Jease$.$Linear}, $Opacity: 2}}
    ];

    var jssor_10_options = {
      $AutoPlay: 1,
      $FillMode: 5,
      $SlideshowOptions: {
        $Class: $JssorSlideshowRunner$,
        $Transitions: jssor_10_SlideshowTransitions,
        $TransitionsOrder: 1
      },
      $ArrowNavigatorOptions: {
        $Class: $JssorArrowNavigator$
      },
      $BulletNavigatorOptions: {
        $Class: $JssorBulletNavigator$,
        $SpacingX: 16,
        $SpacingY: 16
      }
    };

    var jssor_10_slider = new $JssorSlider$('jssor_10', jssor_10_options);
    var MAX_WIDTH = 600;

    function ScaleSlider() {
      var containerElement = jssor_10_slider.$Elmt.parentNode;
      var containerWidth = containerElement.clientWidth;

      if (containerWidth) {

        var expectedWidth = Math.min(MAX_WIDTH || containerWidth, containerWidth);

        jssor_10_slider.$ScaleWidth(expectedWidth);
      } else {
        window.setTimeout(ScaleSlider, 30);
      }
    }

    ScaleSlider();

    $Jssor$.$AddEvent(window, 'load', ScaleSlider);
    $Jssor$.$AddEvent(window, 'resize', ScaleSlider);
    $Jssor$.$AddEvent(window, 'orientationchange', ScaleSlider);

  };

  jssor_11_slider_init = function () {

    var jssor_11_options = {
      $Idle: 2000,
      $SlideEasing: $Jease$.$InOutSine,
      $DragOrientation: 3,
      $ArrowNavigatorOptions: {
        $Class: $JssorArrowNavigator$
      },
      $BulletNavigatorOptions: {
        $Class: $JssorBulletNavigator$,
        $SpacingX: 16,
        $SpacingY: 16
      }
    };

    var jssor_11_slider = new $JssorSlider$('jssor_11', jssor_11_options);

    // make sure to clear margin of the slider container element.
    jssor_11_slider.$Elmt.style.margin = '';


    var MAX_WIDTH = 3000;
    var MAX_HEIGHT = 3000;
    var MAX_BLEEDING = 1;

    function ScaleSlider() {
      var containerElement = jssor_11_slider.$Elmt.parentNode;
      var containerWidth = containerElement.clientWidth;

      if (containerWidth) {
        var originalWidth = jssor_11_slider.$OriginalWidth();
        var originalHeight = jssor_11_slider.$OriginalHeight();

        var containerHeight = containerElement.clientHeight || originalHeight;

        var expectedWidth = Math.min(MAX_WIDTH || containerWidth, containerWidth);
        var expectedHeight = Math.min(MAX_HEIGHT || containerHeight, containerHeight);

        // scale the slider to expected size.
        jssor_11_slider.$ScaleSize(expectedWidth, expectedHeight, MAX_BLEEDING);

        // position slider at center in vertical orientation.
        jssor_11_slider.$Elmt.style.top = ((containerHeight - expectedHeight) / 2) + "px";

        // position slider at center in horizontal orientation.
        jssor_11_slider.$Elmt.style.left = ((containerWidth - expectedWidth) / 2) + "px";
      } else {
        window.setTimeout(ScaleSlider, 30);
      }
    }

    function OnOrientationChange() {
      ScaleSlider();
      window.setTimeout(ScaleSlider, 800);
    }

    ScaleSlider();

    $Jssor$.$AddEvent(window, 'load', ScaleSlider);
    $Jssor$.$AddEvent(window, 'resize', ScaleSlider);
    $Jssor$.$AddEvent(window, 'orientationchange', OnOrientationChange);

  };
})(jQuery);

jQuery(document).ready(function () {
  slider_tyle = drupalSettings.image_slider.slider_tyle;
  if (slider_tyle === 'image-gallery') {
    jssor_2_slider_init();
  }
  else if (slider_tyle === 'image-slider') {
    jssor_1_slider_init();
  }
  else if (slider_tyle === 'banner-rotator') {
    jssor_3_slider_init();
  }
  else if (slider_tyle === 'banner-slider') {
    jssor_4_slider_init();
  }
  else if (slider_tyle === 'carousel-slider') {
    jssor_5_slider_init();
  }
  else if (slider_tyle === 'nearby-image-partial-visible-slider') {
    jssor_6_slider_init();
  }
  else if (slider_tyle === 'image-gallery-with-vertical-thumbnail') {
    jssor_7_slider_init();
  }
  else if (slider_tyle === 'scrolling-logo-thumbnail-slider') {
    jssor_8_slider_init();
  }
  else if (slider_tyle === 'full-width-slider') {
    jssor_9_slider_init();
  }
  else if (slider_tyle === 'different-size-photo-slider') {
    jssor_10_slider_init();
  }
  else if (slider_tyle === 'full-window-for-pc') {
    jssor_11_slider_init();
  }
});
