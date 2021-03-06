var gulp = require('gulp'),
    uglify = require('gulp-uglify'),
    rename = require('gulp-rename');

gulp.task('js', function () {
    return gulp.src('./assets/nTracker.js')
        .pipe(rename({suffix: '.min'}))
        .pipe(uglify())
        .pipe(gulp.dest('./assets/'));
});

gulp.task('watch', function () {
    gulp.watch('./assets/nTracker.js', gulp.series('js'));
});

gulp.task('default', gulp.series('js', 'watch'));