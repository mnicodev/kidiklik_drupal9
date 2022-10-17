var gulp = require("gulp"),
  sass = require("gulp-sass")(require('sass')),
  autoprefixer = require("gulp-autoprefixer"),
  sourcemaps = require("gulp-sourcemaps"),
  del = require("del");

const sassBuild = () => {
  return gulp
    .src([
      "./assets/scss/root.scss",
      "./assets/scss/root-rtl.scss",
      "./assets/scss/content-form.scss",
    ])
    .pipe(sourcemaps.init())
    .pipe(sass().on("error", sass.logError))
    .pipe(autoprefixer("last 2 version"))
    .pipe(sourcemaps.write("./"))
    .pipe(gulp.dest("./assets/css"));
};

const watch = () => {
  gulp.watch("./assets/scss/**/*.scss", gulp.series(sassBuild));
};

exports.build = sassBuild;
exports.watch = watch;
