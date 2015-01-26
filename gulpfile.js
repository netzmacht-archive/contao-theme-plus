var gulp = require('gulp'),
// gulp modules
    autoprefixer = require('gulp-autoprefixer'),
    plumber = require('gulp-plumber'),
    sass = require('gulp-sass'),
    sourcemaps = require('gulp-sourcemaps'),
    uglifyJs = require('gulp-uglify'),
    wait = require('gulp-wait'),
// native modules
    del = require('del'),
    runSequence = require('run-sequence');

/**
 * Build stylesheets tasks
 */
gulp.task('build-stylesheets', function () {
    return gulp.src('assets/sources/stylesheets/*.scss')
        .pipe(wait(500))
        .pipe(plumber())
        .pipe(sourcemaps.init())
        .pipe(sass({
            outputStyle: 'compressed'
        }))
        .pipe(autoprefixer({
            browsers: ['last 2 versions'],
            cascade: false
        }))
        .pipe(sourcemaps.write('.', { sourceRoot: '../sources/stylesheets' }))
        .pipe(gulp.dest('assets/stylesheets'));
});

/**
 * Build javascripts tasks
 */
gulp.task('build-javascripts', function () {
    return gulp.src('assets/sources/javascripts/*.js')
        .pipe(wait(500))
        .pipe(plumber())
        .pipe(sourcemaps.init({ loadMaps: true }))
        .pipe(uglifyJs())
        .pipe(sourcemaps.write('.', { sourceRoot: '../sources/javascripts' }))
        .pipe(gulp.dest('assets/javascripts'));
});

/**
 * Global build tasks
 */
gulp.task('clean', function (cb) {
    del(['assets/stylesheets', 'assets/javascripts'], cb);
});

gulp.task('build', function() {
    runSequence(
        'clean',
        ['build-stylesheets', 'build-javascripts']
    );
});

gulp.task('watch', function () {
    gulp.watch('assets/sources/stylesheets/*.scss', ['build-stylesheets']);
    gulp.watch('assets/sources/javascripts/*.js', ['build-javascripts']);
});

gulp.task('default', function() {
    runSequence('build', 'watch');
});
