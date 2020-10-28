/**
 *
 * The packages we are using.
 * Not using gulp-load-plugins as it is nice to see whats here.
 *
 **/
var gulp = require('gulp')
var sass = require('gulp-sass')
var sassLint = require('gulp-sass-lint')
var autoprefixer = require('gulp-autoprefixer')
var plumber = require('gulp-plumber')
var livereload = require('gulp-livereload')
var imagemin = require('gulp-imagemin')
var pngquant = require('imagemin-pngquant')
var sass = require('gulp-sass');
var sassUnicode = require('gulp-sass-unicode');

// Path.
path = require('path')

/**
 *
 * Styles.
 * - Compile.
 * - Autoprefixer.
 * - Catch errors (gulp-plumber).
 *
 **/
gulp.task('sass', gulp.series( function (done) {
  gulp.src([
    '../simple-css/sass/**/*.{scss,sass}',
  ])
    .pipe(sassLint())
    .pipe(sassLint.format())
    .pipe(sass({
      outputStyle: 'expanded'
    }))
    .pipe(sassUnicode())
    .pipe(autoprefixer({
      cascade: false,
      remove: false
    }))
    .pipe(plumber())
    .pipe(gulp.dest('../stylesheets'))
    .pipe(livereload())
  done();
}));

/**
 *
 * Images
 * - Compress them!
 *
 **/
gulp.task('images', gulp.series( function (done) {
  return gulp.src('images/*')
    .pipe(imagemin({
      progressive: true,
      svgoPlugins: [{removeViewBox: true}],
      use: [pngquant()]
    }))
    .pipe(gulp.dest('images'))
  done();
}));

/**
 *
 * Default task.
 * - Runs Sass, scripts, and images.
 * - Prepares all files for deployment.
 *
 **/
gulp.task('default', gulp.series(['sass', 'images'], function(done) {
  done();
}));

/**
 *
 * Watch task.
 * - Runs Sass and scripts.
 * - Watchs for file changes for sass and scripts.
 * - Refreshes browser after files have been compiled.
 *
 **/
gulp.task('watch', gulp.series('sass', function(done){
    gulp.watch('./scss/**/*.{scss,sass}', gulp.series('sass'));
    gulp.watch('js/**/*.js')
    livereload.listen()
    done();
  }
));
